<?php

use App\Http\Controllers\AirtableController;
use App\Jobs\CacheStaticFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
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
    Route::get('/asset/{asset_id}/{name}', function ($asset_id, $name) {
        return Storage::download("app/$asset_id-$name");
    });

    Route::get('/data/{user_uuid}/{base_id}/{table_id}', function (Request $request, string $user_uuid, string $base_id, string $table_id) {
        $view = $request->query('view', null);
        $per_page = $request->query('perPage', '100');
        $filter = $request->query('filter', null);

        $cache = Cache::tags(["uuid:$user_uuid", "base:$base_id", "table:$table_id", "view:$view"]);

        if (intval($per_page) < 1 || intval($per_page) > 100) {
            return response()->json([
                'error' => 'perPage must be between 1 and 100.',
            ], 400);
        }

        $check_cache_key = "check-user:$user_uuid:base:$base_id:table:$table_id:view:$view";

        // Check that the base, table, and view exists, and that the base and table are active
        $check = $cache->remember(
            $check_cache_key,
            now()->addDay(),
            function () use ($base_id, $table_id) {
                $query = DB::table('bases')
                    ->select(
                        'bases.id AS base_id',
                        'tables.id AS table_id',
                        'bases.is_active AS base_is_active',
                        'tables.is_active AS table_is_active',
                        'users.uuid AS uuid',
                        'api_tokens.value AS token',
                        'bases.secret',
                        'tables.ttl'
                    )
                    ->leftJoin('users', 'users.id', '=', 'bases.user_id')
                    ->leftJoin('tables', 'tables.base_id', '=', 'bases.id')
                    ->leftJoin('api_tokens', 'api_tokens.id', '=', 'bases.api_token_id');

                // Check whether the base_id is an id or name (starts with app)
                $is_base_id = substr($base_id, 0, 3) !== 'app';
                $query->where($is_base_id ? 'bases.name' : 'bases.id', $base_id);

                // Check whether the table_id is an id or name (starts with tbl)
                $is_table_id = substr($table_id, 0, 3) !== 'tbl';
                $query->where($is_table_id ? 'tables.name' : 'tables.id', $table_id);

                return $query->first();
            }
        );

        // If the base, table, or view does not exist, return a 404
        if (is_null($check)) {
            return response()->json([
                'error' => $view
                    ? 'The base, table, or view does not exist'
                    : 'The base or table does not exist',
            ], 404);
        }

        // Destructure $results
        [$base_id, $table_id, $base_is_active, $table_is_active, $uuid, $token, $secret, $ttl] = array_values((array) $check);

        // If the base, table or view is not active, return a 404
        if (!$base_is_active || !$table_is_active) {
            return response()->json([
                'error' => $view
                    ? 'The base, table, or view is not active'
                    : 'The base or table is not active',
            ], 404);
        }

        // If the user_uuid does not match the base's user, return a 401
        if ($user_uuid !== $uuid) {
            return response()->json([
                'error' => 'Could not authenticate with the provided credentials'
            ], 401);
        }

        // If the base has a secret, check that the header has been passed through and matches
        if (isset($secret) && "Bearer $secret" !== $request->header('Authorization')) {
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
        $data = $airtable->getRecords($base_id, $table_id, $view, $per_page, $filter);

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

    Route::delete('/data/{user_uuid}/{base_id}/{table_id}', function (Request $request, string $user_uuid, string $base_id, string $table_id) {
        $view = $request->query('view', null);

        $cache = Cache::tags(["uuid:$user_uuid", "base:$base_id", "table:$table_id", "view:$view"]);

        $check_cache_key = "check-user:$user_uuid:base:$base_id:table:$table_id:view:$view";

        // Check that the base, table, and view exists, and that the base and table are active
        $check = $cache->remember(
            $check_cache_key,
            now()->addDay(),
            function () use ($base_id, $table_id) {
                $query = DB::table('bases')
                    ->select(
                        'bases.id AS base_id',
                        'tables.id AS table_id',
                        'bases.is_active AS base_is_active',
                        'tables.is_active AS table_is_active',
                        'users.uuid AS uuid',
                        'api_tokens.value AS token',
                        'bases.secret',
                        'tables.ttl'
                    )
                    ->leftJoin('users', 'users.id', '=', 'bases.user_id')
                    ->leftJoin('tables', 'tables.base_id', '=', 'bases.id')
                    ->leftJoin('api_tokens', 'api_tokens.id', '=', 'bases.api_token_id');

                // Check whether the base_id is an id or name (starts with app)
                $is_base_id = substr($base_id, 0, 3) !== 'app';
                $query->where($is_base_id ? 'bases.name' : 'bases.id', $base_id);

                // Check whether the table_id is an id or name (starts with tbl)
                $is_table_id = substr($table_id, 0, 3) !== 'tbl';
                $query->where($is_table_id ? 'tables.name' : 'tables.id', $table_id);

                return $query->first();
            }
        );

        // If the base, table, or view does not exist, return a 404
        if (is_null($check)) {
            return response()->json([
                'error' => $view
                    ? 'The base, table, or view does not exist'
                    : 'The base or table does not exist',
            ], 404);
        }

        // Destructure $results
        [$base_id, $table_id, $base_is_active, $table_is_active, $uuid, $token, $secret, $ttl] = array_values((array) $check);

        // If the base, table or view is not active, return a 404
        if (!$base_is_active || !$table_is_active) {
            return response()->json([
                'error' => $view
                    ? 'The base, table, or view is not active'
                    : 'The base or table is not active',
            ], 404);
        }

        // If the user_uuid does not match the base's user, return a 401
        if ($user_uuid !== $uuid) {
            return response()->json([
                'error' => 'Could not authenticate with the provided credentials'
            ], 401);
        }

        // If the base has a secret, check that the header has been passed through and matches
        if (isset($secret) && "Bearer $secret" !== $request->header('Authorization')) {
            return response()->json([
                'error' => 'The Authorization header is invalid',
            ], 401);
        }

        // Erase the data from the cache
        $cache->flush();

        return response()->json([
            'success' => true,
        ]);
    })->middleware('gzipped');
});
