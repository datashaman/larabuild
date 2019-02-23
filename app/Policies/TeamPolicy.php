<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Team;

class TeamPolicy extends AbstractPolicy
{
    protected function userIsInTeam(User $user, Team $team)
    {
        return $user
            ->teams()
            ->where('teams.id', $team->id)
            ->exists();
    }

    /**
     * Determine whether the user can view the team index.
     *
     * @param User $user
     * @return mixed
     */
    public function index(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the team.
     *
     * @param User $user
     * @param Team $team
     * @return mixed
     */
    public function view(User $user, Team $team)
    {
        return $this->userIsInTeam($user, $team);
    }

    /**
     * Determine whether the user can create teams.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the team.
     *
     * @param User $user
     * @param Team $team
     * @return mixed
     */
    public function update(User $user, Team $team)
    {
        return $this->userIsInTeam($user, $team)
            && $user->hasRole('team-admin', $team);
    }

    /**
     * Determine whether the user can delete the team.
     *
     * @param User $user
     * @param Team $team
     * @return mixed
     */
    public function delete(User $user, Team $team)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the team.
     *
     * @param User $user
     * @param Team $team
     * @return mixed
     */
    public function restore(User $user, Team $team)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the team.
     *
     * @param User $user
     * @param Team $team
     * @return mixed
     */
    public function forceDelete(User $user, Team $team)
    {
        return false;
    }
}
