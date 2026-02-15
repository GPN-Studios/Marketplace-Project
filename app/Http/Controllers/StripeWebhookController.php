<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $secret
            );
        } catch (UnexpectedValueException $e) {
            return response()->json(['message' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        if ($event->type !== 'checkout.session.completed') {
            return response()->json([
                'status' => 'ignored',
                'event' => $event->type,
            ]);
        }

        \Log::info('Stripe event', ['type' => $event->type]);

        $session = $event->data->object;

        $orderId = $session->metadata->order_id;

        if(!$orderId) {
            \Log::warning('Stripe webhook without order_id', [
                'session_id' => $session->id,
            ]);

            return response()->json(['error' => 'missing order_id'], 400);
        }

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'order not found',
                'order_id' => $orderId,
            ], 404);
        }

        if ($order->status === 'paid') {
            return response()->json(['already_processed' => true]);
        }

        if ($session->payment_status !== 'paid') {
            return response()->json(['ignored' => true]);
        }

        DB::transaction(function() use ($order, $session)  {

            if ($order->status === 'paid') {
                return;
            }

            foreach($order->items as $item) {
                $item->seller->increment('balance', $item->subtotal);

                $order->update([
                    'status' => 'paid',
                    'stripe_session_id' => $session->id,
                ]);
            }
        });


        return response()->json([
            'status' => '200',
            'message' => 'success'
            ],200);   
    }
}
