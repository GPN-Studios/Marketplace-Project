<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('cart.cart_index', [
            'cart' => Order::with('items.product')
                ->where('user_id', Auth::id())
                ->where('status', OrderStatus::Cart)
                ->first(),
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->authorize('addToCart', $product);
        } catch (AuthorizationException $e) {
            return back()->with('error', 'Você não pode adicionar seu próprio produto ao carrinho.');
        }

        $order = Order::firstOrCreate([
            'user_id' => Auth::id(),
            'status' => OrderStatus::Cart,
        ],
            [
                'total' => 0,
            ]
        );

        $item = $order->items()->where('product_id', $product->id)->first();
        $requestedQuantity = (int) $request->validate([
            'quantity' => 'required|integer|min:1',
        ])['quantity'];

        $resultingQuantity = $requestedQuantity + ($item->quantity ?? 0);

        if ($resultingQuantity > $product->stock) {
            return back()->with('error', 'Estoque insuficiente para a quantidade solicitada.');
        }

        if ($item) {
            $item->quantity = $resultingQuantity;
            $item->subtotal = $item->quantity * $item->price;
            $item->save();
        } else {
            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'seller_id' => $product->user_id,
                'quantity' => $requestedQuantity,
                'price' => $product->price,
                'subtotal' => $product->price * $requestedQuantity,
            ]);
        }

        return redirect()->back()->with('success', 'Adicionado ao carrinho');
    }

    public function update(Request $request, OrderItem $item): RedirectResponse
    {
        $this->authorize('manage', $item);

        $product = $item->product;

        $request->validate([
            'action' => 'required|in:increase,decrease',
        ]);

        if ($request->action == 'increase') {

            if ($item->quantity + 1 > $product->stock) {
                return back()->withErrors('Estoque Insuficiente.');
            }

            $item->increment('quantity');
        }

        if ($request->action == 'decrease') {

            if ($item->quantity - 1 == 0) {
                return back()->withErrors('Não é possivel reduzir a quantia para menos que 1.');
            }

            $item->decrement('quantity');
        }

        $subtotalValue = $item->price * $item->quantity;

        $item->update([
            'subtotal' => $subtotalValue,
        ]);

        return redirect()->route('cart.index')->with('success', 'Produto atualizado com suscesso.');
    }

    public function delete(OrderItem $item): RedirectResponse
    {
        $this->authorize('manage', $item);

        $item->delete();

        return redirect()->route('cart.index')->with('success', 'Item removido do carrinho.');
    }

    public function myOrders(): View
    {
        $orders = Auth::user()
            ->orders()
            ->with('items.product')
            ->whereIn('status', [OrderStatus::Pending, OrderStatus::Paid, OrderStatus::Completed])
            ->latest()
            ->paginate((int) config('shop.pagination.orders'));

        return view('user_orders', compact('orders'));
    }
}
