<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
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
        return $this
            ->hasMany(Build::class)
            ->orderBy('number', 'desc');
    }

    public function getLatestBuildAttribute()
    {
        return $this->builds()
            ->latest()
            ->first();
    }

    /**
     * @param string $commit
     *
     * @return Build
     */
    public function createBuild(string $commit): Build
    {
        return DB::transaction(
            function () use ($commit) {
                $number = $this->builds()->max('number') ?: 0;

                return $this->builds()
                    ->create(
                        [
                            'commit' => $commit,
                            'number' => $number + 1,
                        ]
                    );
            }
        );
    }

    /**
     * @return string
     */
    public function getPrivateKeyAttribute()
    {
        return decrypt($this->attributes['private_key']);
    }

    /**
     * @return string
     */
    public function getNpmCache()
    {
        return storage_path("app/cache/{$this->id}/npm");
    }

    /**
     * @return string
     */
    public function getComposerCache()
    {
        return storage_path("app/cache/{$this->id}/composer");
    }

    /**
     * @return string
     */
    public function getOwnerRepoAttribute(): string
    {
        if (preg_match('#^https?://github\.com/([^/]*)/([^/]*)#', $this->repository, $match)) {
            return $match[1] . '/'. $match[2];
        }

        if (preg_match('#^git@github\.com:([^/]*)/([^/]*)#', $this->repository, $match)) {
            return $match[1] . '/'. $match[2];
        }
    }
}
