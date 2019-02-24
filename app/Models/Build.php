<?php

namespace App\Models;

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
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
