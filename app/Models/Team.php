<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function addUser(User $user): User
    {
        $exists = $this
            ->users()
            ->where('team_user.user_id', $user->id)
            ->exists();

        if (!$exists) {
            $this->users()->attach($user);
        }

        return $user->refresh();
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function removeUser(User $user): User
    {
        $exists = $this
            ->users()
            ->where('team_user.user_id', $user->id)
            ->exists();

        if ($exists) {
            $this->users()->detach($user);
        }

        return $user->refresh();
    }
}
