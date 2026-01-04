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

        $data = $request->validate(
        [
            'name'        => 'required|string|min:5|max:255',
            'description' => 'required|string|max:1000',
            'price'       => 'required|numeric|min:1',
            'stock'       => 'required|integer|min:1',
            'image' => [
            'required',
            'file',
            'max:2048',
            'mimetypes:image/jpeg,image/png,image/webp',
        ],
        ],
        [
            'name.required' => 'O nome do produto é obrigatório.',
            'name.string'   => 'O nome do produto deve ser um texto válido.',
            'name.min'      => 'O nome do produto deve conter no mínimo :min caracteres.',
            'name.max'      => 'O nome do produto não pode ultrapassar :max caracteres.',

            'description.required' => 'A descrição do produto é obrigatória.',
            'description.string'   => 'A descrição deve ser um texto válido.',
            'description.max'      => 'A descrição não pode ultrapassar :max caracteres.',

            'price.required' => 'O preço do produto é obrigatório.',
            'price.numeric'  => 'O preço deve ser um valor numérico.',
            'price.min'      => 'O preço deve ser no mínimo R$ :min.',

            'stock.required' => 'É necessário informar a quantidade em estoque.',
            'stock.integer'  => 'A quantidade deve ser um número inteiro.',
            'stock.min'      => 'A quantidade mínima em estoque é :min unidade.',

            'image.required' => 'É obrigatório inserir uma imagem do produto.',
            'image.image'    => 'O arquivo enviado deve ser uma imagem válida.',
            'image.max'      => 'A imagem não pode ultrapassar 2MB.',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
        
            if (! getimagesize($file->getPathname())) {
                return back()->withErrors([
                    'image' => 'O arquivo enviado não é uma imagem válida.',
                ]);
            }   

            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
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
