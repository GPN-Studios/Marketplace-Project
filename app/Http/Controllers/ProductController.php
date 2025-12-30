<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ProductController extends Controller
{
    
    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {

        $data = $request->validate([
            'name'  => 'required|string|min:5|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ],
        [
            'price.required' => 'O preço é obrigatório.',
            'price.numeric'  => 'O preço deve ser um número.',
            'stock.required' => 'E necessário inserir a quantidade.',
            'stock.integer' => 'A quantidade deve ser um número.',
            'image' => 'Insira a imagem.'
        ]
        );

        if($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        
        $data['user_id'] = Auth::id();

        Product::create($data);

        return redirect()->route('products.create')->with('success', 'Produto criado com sucesso.');
    }

    public function show(string $id): View
    {
        $productId = decrypt($id);

        $product = Product::findOrFail($productId);

        $product->load('user');

        return view('products.show', compact('product'));
    }




}
