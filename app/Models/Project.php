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
        'repository',
        'team_id',
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
    public function build(string $commit)
    {
        return $this->builds()
            ->create(
                [
                    'commit' => $commit,
                ]
            );
    }
}
