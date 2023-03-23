<?php

use App\Http\Controllers\BaseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
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
                base_id IN(
                    SELECT
                        id FROM bases
                    WHERE
                        user_id = :user_id) AND
                created_at > DATE_SUB(NOW(), INTERVAL 30 DAY);",
            ['user_id' => $user_id]
        )[0];

        return Inertia::render('Dashboard', [
            'bases' => auth()->user()->bases()->with('tables')->get(),
            'stats' => [
                'total_requests' => $result->total_requests,
                'unique_users' => $result->unique_users,
            ],
        ]);
    })->name('dashboard');

    Route::controller(BaseController::class)->group(function () {
        Route::get('/bases/{base}', 'show')->name('base');
        Route::post('/bases/{base}/disable', 'disable')->name('base.disable');
        Route::post('/bases/{base}/bust-cache', 'bustCache')->name('base.bust-cache');
    });
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
