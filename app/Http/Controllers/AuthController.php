<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @return User
     */
    public function me()
    {
        return auth()->user();
    }
}
