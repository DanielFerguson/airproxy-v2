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
}
