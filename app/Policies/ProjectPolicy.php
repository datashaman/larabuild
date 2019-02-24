<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy extends AbstractPolicy
{
    /**
     * Determine whether the user can view the project index.
     *
     * @param User $user
     * @return mixed
     */
    public function index(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the project.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
        return $user
            ->teams()
            ->whereHas(
                'projects',
                function ($q) use ($project) {
                    return $q->where('id', $project->id);
                }
            )
            ->exists();
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // Authorization checked in mutation
        return true;
    }

    /**
     * Determine whether the user can update the project.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        // Authorization checked in mutation
        return true;
    }

    /**
     * Determine whether the user can delete the project.
     *
     * @param  User  $user
     * @param  Project  $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        // Authorization checked in mutation
        return true;
    }

    /**
     * Determine whether the user can restore the project.
     *
     * @param  User    $user
     * @param  Project $project
     * @return mixed
     */
    public function restore(User $user, Project $project)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the project.
     *
     * @param  User    $user
     * @param  Project $project
     * @return mixed
     */
    public function forceDelete(User $user, Project $project)
    {
        //
    }

    /**
     * Determine whether the user can build the project.
     *
     * @param  User    $user
     * @param  Project $project
     * @return mixed
     */
    public function build(User $user, Project $project)
    {
        return true;
    }
}
