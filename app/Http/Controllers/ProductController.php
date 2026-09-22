<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Tags\Tag;

class ProductController extends Controller
{
    public function create(): View
    {
        $tags = Tag::all();

        return view('products.create', compact('tags'));
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('products.edit', compact('product'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->safe()->merge([
            'image' => $request->file('image')->store('products', 'public'),
            'user_id' => Auth::id(),
        ])->except('tags'));

        $product->syncTags($request->tags);

        return back()->with('success', 'Produto criado com sucesso.');
    }

    public function show(Product $product): View
    {
        $product->load(['user', 'tags']);

        return view('products.show', [
            'product' => $product,
            'sellerPositiveRatings' => $product->user->positiveRatingsCount(),
            'sellerNegativeRatings' => $product->user->negativeRatingsCount(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.show', $product)->with('success', 'Produto atualizado com sucesso.');
    }

    public function delete(Request $request, Product $product)
    {

        $this->authorize('delete', $product);

        $product->delete();

        return redirect()->route('home');
    }
}
