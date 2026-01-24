<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
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

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.show', encrypt($product->id));
    }
}