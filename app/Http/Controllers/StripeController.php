<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StripeController extends Controller
{
    public function checkout(Order $order): RedirectResponse
    {
        $this->authorize('pay', $order);

        if ($order->items->isEmpty()) {
            abort(400, 'Pedido sem itens');
        }

        $lineitems = $order->items->map(function ($item) {
        return [
            'price_data' => [
                'currency' => 'brl',
                'product_data' => [
                'name' => $item->product_name,
                ],
                'unit_amount' => $item->price,
            ],
            'quantity' => $item->quantity,
          ];
        })->toArray();

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $lineitems,
            'metadata'=> [
                'order_id' => $order->id,
            ],
            'success_url' => route('checkout.success', $order),
            'cancel_url' => route('checkout.cancel', $order),
        ]);

        return redirect($session->url);
    }
}
