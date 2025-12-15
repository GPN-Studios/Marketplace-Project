<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MainController extends Controller
{
    
    public function home(): view 
    {
        return view('home');
    }

    public function login(): view
    {
        return view('auth.login');
    }

    public function signup(): view
    {
        return view('auth.signup');
    }


}
