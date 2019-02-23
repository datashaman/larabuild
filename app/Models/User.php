<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
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
     *
     * @return bool
     */
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return $this
            ->userRoles()
            ->whereIn('role', $roles)
            ->exists();
    }

    /**
     * @param string $role
     */
    public function addRole(string $role)
    {
        if (in_array($role, config('larabuild.roles'))) {
            $this->userRoles()->create(['role' => $role]);
        }
    }

    /**
     * @param string $role
     */
    public function removeRole(string $role)
    {
        if (in_array($role, config('larabuild.roles'))) {
            $this->userRoles()
                ->where('role', $role)
                ->delete();
        }
    }

    /**
     * @return array
     */
    public function getRolesAttribute()
    {
        return $this
            ->userRoles()
            ->pluck('role')
            ->all();
    }
}
