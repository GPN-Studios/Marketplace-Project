<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderAdress;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function index() : View
    {
        return view('cart.cart_index',[
            'cart' => Order::with('items.product')->where('user_id', Auth::id())->where('status', 'cart')->first()
        ]);
    }

    public function add(Request $request, string $productId) : RedirectResponse
    {
        $product = Product::with('user')->findOrFail(decrypt($productId));   // product, if not used, delete

        $order = Order::firstOrCreate([
            'user_id' => Auth::id(),
            'status' => 'cart',
        ],
        [
            'total' => 0,
        ]
        );

        $item = $order->items()->where('product_id', $product->id)->first();

        if(!$item) {
            $order->items()->updateOrCreate(
                ['product_id' => $product->id]
                ,
                [
                'product_name' => $product->name,
                'seller_id' => $product->user->id,
                'quantity' => 1,
                'price' => $product->price,
                'subtotal' => $product->price,
                ]
            );
        }

    return redirect()->back() ->with('success', 'Adicionado ao carrinho');
    }

    public function update(Request $request, OrderItem $item) : RedirectResponse
    {
        $product = Product::findOrFail($item->product_id);

        $request->validate([
            'action' => 'required|in:increase,decrease',
        ]);

        if($request->action == 'increase') {

            if($item->quantity + 1 > $product->stock) {
                return back()->withErrors('Estoque Insuficiente.'); 
            }

            $item->increment('quantity');
        }

        if($request->action == 'decrease') {

            if($item->quantity - 1 == 0) {
                return back()->withErrors('Não é possivel reduzir a quantia para menos que 1.');
            }

            $item->decrement('quantity');
        }

        $subtotalValue = $item->price * $item->quantity;

        $item->update([
            'subtotal' => $subtotalValue
        ]);

        return redirect()->route('cart.index')->with('success', 'Produto atualizado com suscesso.');
    }

    public function delete(OrderItem $item) : RedirectResponse
    {
        $item->delete();

        return redirect()->route('cart.index')->with('success', 'Item removido do carrinho.');
    }



}


