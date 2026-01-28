<?php

namespace App\Presentation\Http\Controllers\Web;

use App\Presentation\Http\Controllers\Controller;

class NotificationsController extends Controller
{
    /**
     * Show notifications page.
     */
    public function index()
    {
        return view('notifications.index');
    }
}
