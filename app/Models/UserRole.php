<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
