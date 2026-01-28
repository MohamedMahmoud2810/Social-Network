<?php

namespace App\Presentation\Http\Controllers\Web;

use App\Presentation\Http\Controllers\Controller;

class FriendshipController extends Controller
{
    /**
     * Show friends list.
     */
    public function index()
    {
        return view('friends.index');
    }

    /**
     * Show pending friend requests.
     */
    public function pending()
    {
        return view('friends.pending');
    }

    /**
     * Show friend requests to accept.
     */
    public function requests()
    {
        return view('friends.requests');
    }

    /**
     * Show friend suggestions.
     */
    public function suggestions()
    {
        return view('friends.suggestions');
    }
}
