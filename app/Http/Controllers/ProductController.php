<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Spatie\Tags\Tag;

class ProductController extends Controller
{
    public function create(): View
    {
        $tags = Tag::all();

        return view('products.create', compact('tags'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->safe()->merge([
            'image' => $request->file('image')->store('products', 'public'),
            'user_id' => Auth::id()
        ])->except('tags'));

        if($request->filled('tags')) {
            $product->syncTags($request->input('tags'));
        }

        return back()->with('success', 'Produto criado com sucesso.');
    }

    public function show(string $id): View
    {
        $tags = Tag::all();

        $productId = decrypt($id);

        $product = Product::findOrFail($productId);

        $product->load('user');

        return view('products.show', compact('product', 'tags'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|min:5|max:255',
            'image' => 'sometimes|image|max:2048',
            'description' => 'sometimes|string|max:1000',
            'price' => 'sometimes|numeric|min:1',
            'stock' => 'sometimes|integer|min:1',
        ]);

        $product->update($data);

        return redirect()->route('products.show', encrypt($product->id));
    }
}