<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends AbstractPolicy
{
    /**
     * Determine whether the user can view the user index.
     *
     * @param User $actingUser
     * @return mixed
     */
    public function index(User $actingUser)
    {
        return false;
    }

    /**
     * Determine whether the user can view the user.
     *
     * @param User $actingUser
     * @param User $user
     * @return mixed
     */
    public function view(User $actingUser, User $user)
    {
        return $actingUser->id === $user->id;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param User $actingUser
     * @return mixed
     */
    public function create(User $actingUser)
    {
        return false;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param User $actingUser
     * @param User $user
     * @return mixed
     */
    public function update(User $actingUser, User $user)
    {
        return $actingUser->id === $user->id;
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param User $actingUser
     * @param User $user
     * @return mixed
     */
    public function delete(User $actingUser, User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the user.
     *
     * @param User $actingUser
     * @param User $user
     * @return mixed
     */
    public function restore(User $actingUser, User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the user.
     *
     * @param User $actingUser
     * @param User $user
     * @return mixed
     */
    public function forceDelete(User $actingUser, User $user)
    {
        return false;
    }
}
