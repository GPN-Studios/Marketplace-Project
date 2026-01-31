<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Spatie\Tags\Tag;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function home(): View
    {
        $tags = Tag::all()->map(function ($tag) {

            $tag->products = Product::withAnyTags([$tag->name])
                ->latest()
                ->take(6)
                ->get();

            return $tag;
        });

        return view('dashboard', compact('tags'));
    }
}
