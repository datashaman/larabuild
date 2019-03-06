<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'private_key',
        'repository',
        'team_id',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'private_key',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function builds()
    {
        return $this->hasMany(Build::class);
    }

    public function getLatestBuildAttribute()
    {
        return $this->builds()
            ->latest()
            ->first();
    }

    /**
     * @param string $commit
     */
    public function createBuild(string $commit)
    {
        return $this->builds()
            ->create(
                [
                    'commit' => $commit,
                ]
            );
    }

    /**
     * @return string
     */
    public function getPrivateKeyAttribute()
    {
        return decrypt($this->attributes['private_key']);
    }
}
