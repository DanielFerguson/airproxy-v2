<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TableController extends Controller
{
    public function toggle(Request $request, Table $table): RedirectResponse
    {
        // Check whether the user can disable this base (if the base belongs to them)
        if ($request->user()->cannot('disable', $table)) {
            abort(403);
        }

        // Toggle the status of the base is_active flag
        $table->is_active = !$table->is_active;
        $table->save();

        // Bust the cache
        Cache::tags(["table:$table->id"])->flush();

        // TODO: Return a success message to trigger a toast
        return to_route('base', $table->base);
    }

    public function updateTtl(Request $request, Table $table): RedirectResponse
    {
        // Check whether the user can disable this base (if the base belongs to them)
        if ($request->user()->cannot('updateTtl', $table)) {
            abort(403);
        }

        // Validate the request
        $validated = $request->validate([
            'ttl' => 'required|integer|min:60|max:604800',
        ]);

        // Update the ttl
        $table->ttl = $validated['ttl'];
        $table->save();

        // Bust the cache
        Cache::tags(["table:$table->id"])->flush();

        return to_route('base', $table->base);
    }
}
