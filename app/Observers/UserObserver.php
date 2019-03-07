<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the user "creating" event.
     *
     * @param User $user
     */
    public function creating(User $user)
    {
        $user->api_token = Str::random(32);
    }
}
