<?php

namespace App\Policies;

use App\Models\Base;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BasePolicy
{
    /**
     * Determine whether the user can bust the cache for the base.
     */
    public function bustCache(User $user, Base $base): bool
    {
        return $user->id === $base->user_id;
    }

    /**
     * Determine whether the user can disable the base.
     */
    public function disable(User $user, Base $base): bool
    {
        return $user->id === $base->user_id;
    }

    public function createToken(User $user, Base $base): bool
    {
        // Check that the user owns the base.
        return $user->id === $base->user_id;
    }
}
