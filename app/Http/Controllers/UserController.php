<?php

namespace App\Http\Controllers;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile(): View
    {
        return view('profile');
    }
    
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            
        ])

        return redirect()->route('profile');
    }








}
