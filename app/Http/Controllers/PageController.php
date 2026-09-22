<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;
use Spatie\Tags\Tag;

class PageController extends Controller
{
    public function about(): View
    {
        return view('about', [
            'stats' => [
                'products' => Product::count(),
                'sellers' => User::has('products')->count(),
                'categories' => Tag::count(),
                'completedOrders' => Order::where('status', OrderStatus::Completed)->count(),
            ],
        ]);
    }

    public function project(): View
    {
        return view('project');
    }
}
