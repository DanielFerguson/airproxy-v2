<?php

use App\Http\Controllers\AirtableController;
use App\Jobs\CacheStaticFiles;
use App\Models\Base;
use App\Models\Table;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/asset/{asset_id}', function ($asset_id) {
        // TODO: Record the request

        // Serve the static file
        return response()->file(storage_path("app/{$asset_id}"));
    });

    // TODO: Add sortBy
    // TODO: Add filter
    Route::get('/{base_id}/{table_id}/{view_id?}', function (Request $request, string $base_id, string $table_id, string|null $view_id = null) {
        $page = $request->query('page', '1');
        $per_page = $request->query('perPage', '100');

        if (intval($page) < 1) {
            return response()->json([
                'error' => 'The page number must be positive',
            ], 400);
        }

        if (intval($per_page) < 1 || intval($per_page) > 100) {
            return response()->json([
                'error' => 'The per_page number must be between 1 and 100',
            ], 400);
        }

        DB::table('requests')->insert([
            'base_id' => $base_id,
            'table_id' => $table_id,
            'view_id' => $view_id,
            'page' => $page,
            'per_page' => $per_page,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('Referer'),
            'headers' => json_encode($request->headers->all()),
            'city' => $request->header('cf-ipcity'),
            'country' => $request->header('cf-ipcountry'),
            'continent' => $request->header('cf-ipcontinent'),
            'latitude' => $request->header('cf-iplatitude'),
            'longitude' => $request->header('cf-iplongitude'),
        ]);

        $checks = DB::table('bases')
            ->select(DB::raw('bases.is_active AS base_is_active, tables.is_active AS table_is_active, api_tokens.value AS token, secret, tables.ttl'))
            ->leftJoin('tables', 'tables.base_id', '=', 'bases.id')
            ->leftJoin('api_tokens', 'api_tokens.id', '=', 'bases.api_token_id')
            ->where('bases.id', '=', $base_id)
            ->where('tables.id', '=', $table_id)
            ->first();

        // Check that the base and table are active
        if (!$checks->base_is_active || !$checks->table_is_active) {
            return response()->json([
                'error' => 'The base or table is not active',
            ], 404);
        }

        // If the base has a secret, check that the header has been passed through and matches
        if (isset($checks->secret) && $checks->secret !== $request->header('Authorization')) {
            return response()->json([
                'error' => 'The Authorization header is invalid',
            ], 401);
        }

        $cache_key = "data-base:{$base_id}:table:{$table_id}:view:{$view_id}:page:{$page}:per_page:{$per_page}";

        // Check whether the data exists in the cache and return it if it does
        if (Cache::has($cache_key)) {
            return response()->json(Cache::get($cache_key));
        }

        // Else, fetch the data from Airtable
        $airtable = new AirtableController($checks->token);

        // Check that the base, table and view exists
        if (Base::where('id', '=', $base_id)->doesntExist()) {
            return response()->json([
                'error' => 'The base does not exist',
            ], 404);
        }

        if (Table::where('id', '=', $table_id)->doesntExist()) {
            return response()->json([
                'error' => 'The table does not exist',
            ], 404);
        }

        if ($view_id && View::where('id', '=', $view_id)->doesntExist()) {
            return response()->json([
                'error' => 'The view does not exist',
            ], 404);
        }

        $data = $airtable->getRecords($base_id, $table_id, $view_id, $page, $per_page);

        // Cache the response for the base's TTL and return the response
        Cache::put($cache_key, $data, Base::where('id', '=', $base_id)->first()->ttl);

        // Fire off a job to fetch and cache all of the static files
        CacheStaticFiles::dispatch($data);

        return response()->json($data);
    });
});
