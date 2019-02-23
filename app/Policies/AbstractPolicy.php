<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class AbstractPolicy
{
    use HandlesAuthorization;

    /**
     * @param User   $actingUser
     * @param string $ability
     */
    public function before(User $actingUser, string $ability)
    {
        if ($actingUser->hasRole('admin')) {
            return true;
        }
    }
}
