<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;
use Spatie\Tags\Tag;

class TagController extends Controller
{
    public function show(Tag $tag): View
    {
        $products = Product::withAnyTags([$tag->name])
            ->with(['tags', 'user'])
            ->latest()
            ->paginate((int) config('shop.pagination.tag_products'));

        return view('tags.show', compact('tag', 'products'));
    }
}
