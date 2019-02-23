<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Build;

class BuildPolicy extends AbstractPolicy
{
    /**
     * @param User  $user
     * @param Build $build
     *
     * @return bool
     */
    protected function userIsInBuildTeam(User $user, Build $build): bool
    {
        return $user
            ->whereHas(
                'teams',
                function ($q) use ($build) {
                    $q->whereHas(
                        'projects',
                        function ($q1) use ($build) {
                            $q1->where('id', $build->project->id);
                        }
                    );
                }
            )
            ->exists();
    }

    /**
     * Determine whether the user can view the build index.
     *
     * @param User $user
     * @return mixed
     */
    public function index(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the build.
     *
     * @param  User  $user
     * @param  Build  $build
     * @return mixed
     */
    public function view(User $user, Build $build)
    {
        return $this->userIsInBuildTeam($user, $build);
    }

    /**
     * Determine whether the user can create builds.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the build.
     *
     * @param  User  $user
     * @param  Build  $build
     * @return mixed
     */
    public function update(User $user, Build $build)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the build.
     *
     * @param  User  $user
     * @param  Build  $build
     * @return mixed
     */
    public function delete(User $user, Build $build)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the build.
     *
     * @param  User  $user
     * @param  Build  $build
     * @return mixed
     */
    public function restore(User $user, Build $build)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the build.
     *
     * @param  User  $user
     * @param  Build  $build
     * @return mixed
     */
    public function forceDelete(User $user, Build $build)
    {
        return false;
    }
}
