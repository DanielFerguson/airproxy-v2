<?php

namespace App\Policies;

use App\Models\Table;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TablePolicy
{
    /**
     * Determine whether the user can disable the table.
     */
    public function disable(User $user, Table $table): bool
    {
        return $user->id === $table->base->user_id;
    }

    /**
     * Determine whether the user can update the ttl.
     */
    public function updateTtl(User $user, Table $table): bool
    {
        return $user->id === $table->base->user_id;
    }
}
