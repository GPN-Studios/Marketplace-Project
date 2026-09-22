<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StripeController extends Controller
{
    public function checkout(Order $order): RedirectResponse
    {
        $this->authorize('pay', $order);

        if ($order->items->isEmpty()) {
            abort(400, 'Pedido sem itens');
        }

        $currency = config('shop.currency');

        $lineitems = $order->items->map(function ($item) use ($currency) {
            return [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();

        Stripe::setApiKey(config('services.stripe.secret'));

        // O Stripe exige no mínimo 30 minutos entre a criação e o expires_at
        // de uma Checkout Session, então o piso é aplicado aqui mesmo que
        // 'checkout_expiration_minutes' esteja configurado com um valor
        // menor — evita que a chamada à API falhe.
        $expirationMinutes = max(30, (int) config('shop.checkout_expiration_minutes'));

        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $lineitems,
            'metadata' => [
                'order_id' => $order->id,
            ],
            'success_url' => route('checkout.success', $order),
            'cancel_url' => route('checkout.cancel', $order),
            'expires_at' => now()->addMinutes($expirationMinutes)->timestamp,
        ]);

        return redirect($session->url);
    }
}
