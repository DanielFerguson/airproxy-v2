<?php

use App\Http\Controllers\BaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TableController;
use App\Jobs\FetchBases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::inertia('/', 'Welcome');

Route::get('/blog', fn () => view('blog.index'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user_id = auth()->user()->id;

        $result = DB::select(
            "SELECT
                COUNT(*) AS total_requests,
                COUNT(DISTINCT ip_address) AS unique_users
            FROM
                requests
            WHERE
                requestable_type = 'table' AND 
                requestable_id IN(
                    SELECT
                        tables.id 
                    FROM
                        tables
                    LEFT JOIN bases ON bases.id = tables.base_id 
                    WHERE
                        bases.user_id = :user_id)
                AND created_at > DATE_SUB(NOW(), INTERVAL 30 DAY);",
            ['user_id' => $user_id]
        )[0];

        $requests = collect(DB::select(
            "SELECT
                COUNT(id) AS requests,
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00') AS minute
            FROM
                requests
            WHERE
                created_at > :one_hour_ago AND
                requestable_type = 'table' AND
                requestable_id IN(
                    SELECT
                        tables.id 
                    FROM
                        tables
                    LEFT JOIN bases ON bases.id = tables.base_id 
                    WHERE
                        bases.user_id = :user_id)
            GROUP BY
                minute
            ORDER BY
                minute DESC;",
            [':one_hour_ago' => now()->subHour()->toDateTimeString(), ':user_id' => $user_id]
        ))->pluck('requests', 'minute');

        $normalised_requests = [];
        $last_hour = now()->subHour();

        for ($i = 0; $i < 60; $i++) {
            $minute = $last_hour->addMinute()->startOfMinute()->toDateTimeString();

            $normalised_requests[] = [
                'minute' => $minute,
                'requests' => $requests->get($minute, 0),
            ];
        }

        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => auth()->user(),
                'plan' => auth()->user() ? auth()->user()->sparkPlan() : null,
            ],
            'bases' => auth()->user()->bases()->with('tables')->get(),
            'stats' => [
                'total_requests' => $result->total_requests,
                'unique_users' => $result->unique_users,
                'api_tokens_count' => auth()->user()->apiTokens()->count(),
            ],
            'requests' => $normalised_requests,
        ]);
    })->name('dashboard');

    Route::post('/tokens', function (Request $request) {
        // Check that key is valid
        $validated = $request->validate([
            'key' => 'required',
        ]);

        $key = $validated['key'];

        // Check that the key is valid
        $result = Http::withToken($key)->get('https://api.airtable.com/v0/meta/bases');

        if ($result->failed()) {
            return redirect()->back()->with('error', 'Invalid API key');
        }

        // Create the token if it doesnt exist
        $token = auth()->user()->apiTokens()->firstOrCreate([
            'value' => $key,
        ]);

        // Import account data
        FetchBases::dispatch($token);

        return redirect()->back()->with('success', 'Successfully created API token');
    })->name('tokens.create');

    Route::controller(BaseController::class)->group(function () {
        Route::get('/bases/{base}', 'show')->name('base');
        Route::post('/bases/{base}/disable', 'disable')->name('base.disable');
        Route::post('/bases/{base}/bust-cache', 'bustCache')->name('base.bust-cache');
        Route::post('/bases/{base}/token', 'createToken')->name('base.create-token');
    });

    Route::controller(TableController::class)->group(function () {
        Route::post('/tables/{table}/toggle', 'toggle')->name('table.toggle');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
