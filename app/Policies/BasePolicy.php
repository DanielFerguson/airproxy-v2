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
        // Check that the user owns the base, and they have an active subscription to either a Team or Business plan
        return $user->id === $base->user_id && $user->subscribedToPrice(
            [
                // Testing
                'price_1MnwyQFbI9pBujiNilmd6sFs', // Team (monthly)
                'price_1MnwyQFbI9pBujiN8QUWbKtx', // Team (yearly)
                'price_1MnwylFbI9pBujiN6try6JyO', // Business (monthly)
                'price_1MnwylFbI9pBujiNvOuDAQda', // Business (yearly)
                // Production
                'price_1McxNfFbI9pBujiNk1G9DYC3', // Team (monthly)
                'price_1McxNfFbI9pBujiNbBKDdaEs', // Team (yearly)
                'price_1McxOHFbI9pBujiNF9u2MBHN', // Business (monthly)
                'price_1McxOGFbI9pBujiNGwFVjrOg', // Business (yearly)
            ]
        );
    }
}
