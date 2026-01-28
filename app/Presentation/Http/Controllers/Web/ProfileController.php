<?php

namespace App\Presentation\Http\Controllers\Web;

use App\Presentation\Http\Controllers\Controller;
use App\Domain\User\Models\User;

class ProfileController extends Controller
{
    /**
     * Show user profile.
     */
    public function show(User $user)
    {
        return view('profile', ['user' => $user]);
    }

    /**
     * Show edit profile form.
     */
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }
}
