<?php

namespace App\Http\Controllers;

use App\Models\Base;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BaseController extends Controller
{
    public function show(Base $base)
    {
        $result = DB::select(
            "SELECT
                COUNT(*) AS total_requests
                COUNT(DISTINCT ip_address) AS unique_users
            FROM
                requests
            WHERE
                base_id = :base_id AND
                created_at > DATE_SUB(NOW(), INTERVAL 30 DAY);",
            ['base_id' => $base->id]
        )[0];

        return Inertia::render('Base', [
            'base' => $base->load('tables'),
            'stats' => [
                'total_requests' => $result->total_requests,
                'unique_users' => $result->unique_users,
            ],
        ]);
    }

    public function bustCache(Request $request, Base $base): RedirectResponse
    {
        // Check whether the user can bust this cache (if the base belongs to them)
        if ($request->user()->cannot('bustCache', $base)) {
            abort(403);
        }

        // Bust the cache
        Cache::tags(["base:$base->id"])->flush();

        // TODO: Return a success message to trigger a toast
        return to_route('base', $base);
    }

    /**
     * Update the specified resource in storage.
     */
    public function disable(Request $request, Base $base): RedirectResponse
    {
        // Check whether the user can disable this base (if the base belongs to them)
        if ($request->user()->cannot('disable', $base)) {
            abort(403);
        }

        // Disable the base
        $base->update(['is_active' => false]);

        // Bust the cache
        Cache::tags(["base:$base->id"])->flush();

        // TODO: Return a success message to trigger a toast
        return to_route('base', $base);
    }
}
