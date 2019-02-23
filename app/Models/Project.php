<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
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
}
