<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderAddressRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * --------------->  !!!!!! AVISO !!!!!!  <---------------
 * Este controller simula o fluxo de checkout e de saque de saldo.
 * O pagamento real é processado pelo Stripe (StripeController +
 * StripeWebhookController) — este controller nunca credita saldo
 * sem que o webhook do Stripe tenha confirmado o pagamento antes.
 * Projeto de estudo / uso pessoal.
 */
class CheckoutController extends Controller
{
    public function checkout(StoreOrderAddressRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('checkout', $order);

        if ($order->items->isEmpty()) {
            abort(400, 'Carrinho vazio.');
        }

        DB::transaction(function () use ($order, $request) {
            $total = 0;

            foreach ($order->items as $item) {
                // trava a linha do produto para evitar overselling em requisições concorrentes
                $product = $item->product()->lockForUpdate()->first();

                if ($product->stock < $item->quantity) {
                    abort(400, "Estoque insuficiente para \"{$product->name}\".");
                }

                $product->decrement('stock', $item->quantity);

                $total += $item->subtotal;
            }

            $order->address()->updateOrCreate([], $request->validated());

            $order->update([
                'status' => OrderStatus::Pending,
                'total' => $total,
                'checkout_expires_at' => now()->addMinutes((int) config('shop.checkout_expiration_minutes')),
            ]);
        });

        return redirect()
            ->route('user.orders')
            ->with('success', 'Pedido criado! Finalize o pagamento abaixo para confirmar a compra.');
    }

    // comprador confirma que recebeu os produtos — só alcançável depois que
    // o webhook do Stripe já confirmou o pagamento (status 'paid'). O saldo
    // do vendedor já foi creditado pelo webhook; aqui só fechamos o pedido.
    public function confirmDelivery(Order $order): RedirectResponse
    {
        $this->authorize('confirmDelivery', $order);

        $order->update(['status' => OrderStatus::Completed]);

        return back()->with('success', 'Entrega confirmada! Agora você pode avaliar os produtos.');
    }

    public function withdraw(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if($user->balance <= 0, 400, 'Saldo indisponível.');

        $user->update(['balance' => 0]);

        return back()->with('success', 'Saque realizado com sucesso.');
    }

    public function success(Order $order)
    {
        $this->authorize('view', $order);

        $order->load('items.product');

        return view('checkout_success', compact('order'));
    }

    public function cancel(Order $order)
    {
        $this->authorize('view', $order);

        DB::transaction(function () use ($order) {
            // idempotente: se o pedido já não estiver mais pendente (ex.: o
            // pagamento foi confirmado em outra aba antes do usuário clicar
            // em "cancelar"), não desfazemos nada.
            if ($order->status !== OrderStatus::Pending) {
                return;
            }

            foreach ($order->items as $item) {
                $item->product()->increment('stock', $item->quantity);
            }

            $order->update(['status' => OrderStatus::Cancelled]);
        });

        $order->load('items.product');

        return view('checkout_cancel', compact('order'));
    }
}
