<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $term = trim((string) $request->query('q', ''));

        $products = Product::query()
            ->with(['tags', 'user'])
            ->when($term !== '', function ($query) use ($term) {
                $like = '%'.addcslashes($term, '%_\\').'%';

                $query->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->latest()
            ->paginate((int) config('shop.pagination.search'))
            ->withQueryString();

        return view('search', [
            'term' => $term,
            'products' => $products,
        ]);
    }
}
