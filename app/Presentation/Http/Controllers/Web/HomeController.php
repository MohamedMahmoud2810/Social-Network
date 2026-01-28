<?php

namespace App\Presentation\Http\Controllers\Web;

use App\Presentation\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the home page.
     */
    public function index()
    {
        return view('home');
    }
}
