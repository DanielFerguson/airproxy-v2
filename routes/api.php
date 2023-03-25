<?php

use App\Http\Controllers\AirtableController;
use App\Jobs\CacheStaticFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;

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

Route::prefix('v1')->group(function () {
    Route::get('/asset/{asset_id}', function ($asset_id) {
        return Storage::download($asset_id);
    });

    Route::get('/data/{base_id}/{table_id}', function (Request $request, string $base_id, string $table_id) {
        $view_id = $request->query('view', null);

        $cache = Cache::tags(["base:$base_id", "table:$table_id", "view:$view_id"]);

        $per_page = $request->query('perPage', '100');
        $filter = $request->query('filter', null);

        if (intval($per_page) < 1 || intval($per_page) > 100) {
            return response()->json([
                'error' => 'perPage must be between 1 and 100.',
            ], 400);
        }

        // Check that the base, table, and view exists, and that the base and table are active
        $check = $cache->remember(
            "check",
            now()->addDay(),
            fn () => DB::table('bases')
                ->select(
                    'bases.is_active AS base_is_active,',
                    'tables.is_active AS table_is_active',
                    'api_tokens.value AS token',
                    'bases.secret',
                    'tables.ttl'
                )
                ->leftJoin('tables', 'tables.base_id', '=', 'bases.id')
                ->leftJoin('api_tokens', 'api_tokens.id', '=', 'bases.api_token_id')
                ->where('bases.id', $base_id)
                ->where('tables.id', $table_id)
                ->when($view_id, function (Builder $query, string $view_id) {
                    $query
                        ->addSelect('views.is_active as view_is_active')
                        ->leftJoin('views', 'views.table_id', '=', 'tables.id')
                        ->where('views.id', $view_id);
                }, function (Builder $query) {
                    $query->selectRaw('NULL as view_is_active');
                })
                ->first()
        );

        // If the base, table, or view does not exist, return a 404
        if (is_null($check)) {
            return response()->json([
                'error' => $view_id
                    ? 'The base, table, or view does not exist'
                    : 'The base or table does not exist',
            ], 404);
        }

        // Destructure $results
        [$base_is_active, $table_is_active, $token, $secret, $ttl, $view_is_active] = array_values((array) $check);

        // If the base, table or view is not active, return a 404
        if (!$base_is_active || !$table_is_active || ($view_id && !$view_is_active)) {
            return response()->json([
                'error' => $view_id
                    ? 'The base, table, or view is not active'
                    : 'The base or table is not active',
            ], 404);
        }

        // If the base has a secret, check that the header has been passed through and matches
        if (isset($secret) && $secret !== $request->header('Authorization')) {
            return response()->json([
                'error' => 'The Authorization header is invalid',
            ], 401);
        }

        $cache_key = "data-per_page:$per_page:filter:$filter";

        // If the data exists in the cache, return it
        if ($cache->has($cache_key)) {
            return response()->json($cache->get($cache_key));
        }

        // Fetch the data from Airtable
        $airtable = new AirtableController($token);
        $data = $airtable->getRecords($base_id, $table_id, $view_id, $per_page, $filter);

        // Cache the data for the base's TTL
        $cache->put($cache_key, $data, now()->addSeconds($ttl));

        // Fire off a job to fetch and cache all of the static files
        CacheStaticFiles::dispatchAfterResponse(
            data: $data,
            base_id: $base_id,
            table_id: $table_id,
        );

        return response()->json($data);
    })->middleware('gzipped');
});
