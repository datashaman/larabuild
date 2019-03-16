<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends AbstractPolicy
{
    /**
     * @param User $actingUser
     * @param User $user
     *
     * @return bool
     */
    protected function userIsTeamAdminInSameTeam(User $actingUser, User $user): bool
    {
        $adminTeamIds = $actingUser
            ->userRoles()
            ->where('user_roles.role', 'TEAM_ADMIN')
            ->pluck('user_roles.team_id');

        return $user
            ->teams()
            ->whereIn('teams.id', $adminTeamIds)
            ->exists();
    }

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
        return $this->userIsTeamAdminInSameTeam($actingUser, $user);
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
        return $this->userIsTeamAdminInSameTeam($actingUser, $user);
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
        return $this->userIsTeamAdminInSameTeam($actingUser, $user);
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

    /**
     * Determine whether the user can add a role to the user.
     *
     * @param User $actingUser
     * @param User $user
     * @return mixed
     */
    public function addRole(User $actingUser)
    {
        return $actingUser->hasRole('ADMIN');
    }

    /**
     * Determine whether the user can remove a role from the user.
     *
     * @param User $actingUser
     * @param User $user
     * @return mixed
     */
    public function removeRole(User $actingUser)
    {
        return $actingUser->hasRole('ADMIN');
    }
}
