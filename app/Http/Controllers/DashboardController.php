<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;



class DashboardController extends Controller
{
    
    public function home(): view
    {
        // show view with all the products
        return view('dashboard', [
            'products' => Product::with('tags')->latest()->paginate(10)
        ]);
    }







}
