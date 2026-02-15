<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * --------------->  !!!!!! WARNING !!!!!!  <---------------  
 * This controller simulates checkout and cashout flows.
 * No real payment, transfer or financial transaction is performed.
 * Intended for study / personal project purposes only. 
*/

class CheckoutController extends Controller
{
    public function checkout(Order $order) : RedirectResponse
    {
        $this->authorize('checkout', $order);

        // uses transactions incase anything fails
        DB::transaction(function () use ($order) {
            //use allows $order to be used inside the transaction
            $total = 0;

            foreach ($order->items as $item) {

                //product related to the current orderitem
                $product = $item->product()->lockForUpdate()->first();

                // user related to the current orderitem product
                $seller  = $product->user;

                //verify if product stock is enough
                if ($product->stock < $item->quantity) {
                    abort(400, 'Estoque insuficiente');
                }

                //reduce product stock based on selected quantity
                $product->stock -= $item->quantity;
                $product->save();

                // update the total price to pay
                $total += $item->subtotal;
            }

            // update order
            $order->update([
                'status' => 'pending',
                'total'  => $total,
            ]);

        });

        return redirect()->route('home');
    }


    public function confirmDelivery(Order $order): RedirectResponse
    {
        $this->authorize('confirmDelivery', $order);

        DB::transaction(function () use ($order) {

            foreach ($order->items as $item) {
                $seller = $item->product->user;
                $seller->balance += $item->subtotal;
                $seller->save();
            }

            $order->update([
                'status' => 'completed',
            ]);
        });

        return back()->with('success', 'Entrega confirmada! Agora você pode avaliar os produtos.');
    }

    /**
     * DISCLAIMER:
     * This code does NOT process real payments.
     * All checkout and cashout logic is simulated for learning purposes.
    */
    public function withdraw(Request $request)
    {
        $user = $request->user();
        abort_if($user->balance <= 0, 400, 'Saldo indisponível');

        $user->update([
            'balance' => 0,
        ]);

        return back()->with('success', 'Saque realizado com sucesso.');
    }

    public function success(Order $order)
    {
        return view('checkout_success', compact('order'));
    }

    public function cancel(Order $order)
    {
        return view('checkout_cancel', compact('order'));
    }

}