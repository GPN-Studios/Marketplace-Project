<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

function stripeSignedPayload(array $payload, string $secret): array
{
    $payloadJson = json_encode($payload);
    $timestamp = time();
    $signature = hash_hmac('sha256', "{$timestamp}.{$payloadJson}", $secret);

    return [$payloadJson, "t={$timestamp},v1={$signature}"];
}

beforeEach(function () {
    $this->seller = User::factory()->create();
    $this->buyer = User::factory()->create();

    $this->product = Product::create([
        'user_id' => $this->seller->id,
        'name' => 'Produto de teste',
        'description' => 'desc',
        'price' => 1000, // R$ 10,00
        'stock' => 5,
    ]);
});

function addressPayload(): array
{
    return [
        'recipient_name' => 'João da Silva',
        'cep' => '01001000',
        'state' => 'SP',
        'city' => 'São Paulo',
        'district' => 'Centro',
        'street' => 'Praça da Sé',
        'number' => '100',
        'complement' => '',
    ];
}

test('checkout reserves stock, saves the address and marks the order pending', function () {
    $order = Order::create(['user_id' => $this->buyer->id, 'status' => OrderStatus::Cart, 'total' => 0]);
    $order->items()->create([
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'seller_id' => $this->seller->id,
        'quantity' => 2,
        'price' => $this->product->price,
        'subtotal' => $this->product->price * 2,
    ]);

    $this->actingAs($this->buyer)
        ->post(route('checkout', $order), addressPayload())
        ->assertRedirect(route('user.orders'));

    $order->refresh();

    expect($order->status)->toBe(OrderStatus::Pending)
        ->and($order->total)->toBe(2000)
        ->and($this->product->fresh()->stock)->toBe(3)
        ->and($order->address)->not->toBeNull()
        ->and($order->address->city)->toBe('São Paulo');
});

test('a paid webhook credits the seller once and confirmDelivery does not credit again', function () {
    $order = Order::create(['user_id' => $this->buyer->id, 'status' => OrderStatus::Pending, 'total' => 1000]);
    $order->items()->create([
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'seller_id' => $this->seller->id,
        'quantity' => 1,
        'price' => 1000,
        'subtotal' => 1000,
    ]);

    $secret = 'whsec_test_secret';
    config(['services.stripe.webhook_secret' => $secret]);

    [$payloadJson, $signatureHeader] = stripeSignedPayload([
        'id' => 'evt_test_1',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_1',
                'payment_status' => 'paid',
                'metadata' => ['order_id' => (string) $order->id],
            ],
        ],
    ], $secret);

    $this->call('POST', '/webhook/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => $signatureHeader,
        'CONTENT_TYPE' => 'application/json',
    ], $payloadJson)->assertOk();

    $order->refresh();
    $this->seller->refresh();

    expect($order->status)->toBe(OrderStatus::Paid)
        ->and($this->seller->balance)->toBe(1000);

    // antes do pagamento real (status 'paid'), confirmDelivery era alcançável
    // com o pedido ainda 'pending' e permitia creditar o vendedor sem
    // pagamento nenhum — isso não pode mais acontecer.
    $this->actingAs($this->buyer)
        ->post(route('checkout.complete', $order))
        ->assertRedirect();

    $order->refresh();
    $this->seller->refresh();

    expect($order->status)->toBe(OrderStatus::Completed)
        ->and($this->seller->balance)->toBe(1000); // não dobrou
});

test('confirmDelivery is not reachable before a real payment', function () {
    $order = Order::create(['user_id' => $this->buyer->id, 'status' => OrderStatus::Pending, 'total' => 1000]);
    $order->items()->create([
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'seller_id' => $this->seller->id,
        'quantity' => 1,
        'price' => 1000,
        'subtotal' => 1000,
    ]);

    $this->actingAs($this->buyer)
        ->post(route('checkout.complete', $order))
        ->assertForbidden();

    expect($this->seller->fresh()->balance)->toBe(0);
});

test('cancelling a pending order restores stock', function () {
    $this->product->update(['stock' => 3]);

    $order = Order::create(['user_id' => $this->buyer->id, 'status' => OrderStatus::Pending, 'total' => 1000]);
    $order->items()->create([
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'seller_id' => $this->seller->id,
        'quantity' => 2,
        'price' => 1000,
        'subtotal' => 2000,
    ]);

    $this->actingAs($this->buyer)
        ->get(route('checkout.cancel', $order))
        ->assertOk();

    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled)
        ->and($this->product->fresh()->stock)->toBe(5);
});
