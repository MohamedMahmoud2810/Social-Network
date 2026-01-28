<?php

namespace App\Presentation\Http\Controllers\Web;


use Illuminate\Http\Request;

class AuthController
{
    public function showLogin()
    {
        return view('auth.login');
    }
}
