<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function builds()
    {
        return $this->hasMany(Build::class);
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * @return string|array $role
     * @return Team|null    $team
     *
     * @return bool
     */
    public function hasRole($roles, Team $team = null): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        $teamId = is_null($team) ? null : $team->id;

        return $this
            ->userRoles()
            ->whereIn('role', $roles)
            ->where('team_id', $teamId)
            ->exists();
    }

    /**
     * @param  string   $role
     * @param Team|null $team
     */
    public function addRole(string $role, Team $team = null)
    {
        if (in_array($role, config('larabuild.roles'))) {
            $teamId = is_null($team) ? null : $team->id;

            $this->userRoles()->updateOrCreate(['role' => $role, 'team_id' => $teamId]);
        }
    }

    /**
     * @param string    $role
     * @param Team|null $team
     */
    public function removeRole(string $role, Team $team = null)
    {
        if (in_array($role, config('larabuild.roles'))) {
            $teamId = is_null($team) ? null : $team->id;

            $this->userRoles()
                ->where('role', $role)
                ->where('team_id', $teamId)
                ->delete();
        }
    }

    /**
     * @param Team $user
     *
     * @return Team
     */
    public function addTeam(Team $team): Team
    {
        $exists = $this
            ->teams()
            ->where('team_user.team_id', $team->id)
            ->exists();

        if (!$exists) {
            $this->teams()->attach($team);
        }

        return $team->refresh();
    }

    /**
     * @param Team $user
     *
     * @return Team
     */
    public function removeTeam(Team $team): Team
    {
        $exists = $this
            ->teams()
            ->where('team_user.team_id', $team->id)
            ->exists();

        if ($exists) {
            $this->teams()->detach($team);
        }

        return $team->refresh();
    }
}
