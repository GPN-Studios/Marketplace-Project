<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MainController extends Controller
{
    
    public function viewTest(): view 
    {
        return view('layouts.main_layout');
    }

}
