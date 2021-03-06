<?php

namespace App\Models;

use File;
use Illuminate\Database\Eloquent\Model;

class Build extends Model
{
    /**
     * @var array
     */
    protected $dates = [
        'completed_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'commit',
        'number',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string
     */
    public function getOutputFile()
    {
        return storage_path("app/output/{$this->project->id}/{$this->number}.txt");
    }

    /**
     * @return string
     */
    public function getWorkspace()
    {
        return storage_path("app/workspace/{$this->project->id}/{$this->number}");
    }
}
