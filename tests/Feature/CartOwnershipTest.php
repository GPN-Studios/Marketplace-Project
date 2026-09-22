<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

function makeCartItem(User $buyer, ?int $stock = 10): OrderItem
{
    $seller = User::factory()->create();

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Produto de teste',
        'description' => 'desc',
        'price' => 1000,
        'stock' => $stock,
    ]);

    $order = Order::create([
        'user_id' => $buyer->id,
        'status' => OrderStatus::Cart,
        'total' => 0,
    ]);

    return $order->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'seller_id' => $seller->id,
        'quantity' => 1,
        'price' => $product->price,
        'subtotal' => $product->price,
    ]);
}

test('a user cannot update another user\'s cart item', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $item = makeCartItem($owner);

    $this->actingAs($attacker)
        ->patch(route('cart.update', $item), ['action' => 'increase'])
        ->assertForbidden();

    expect($item->fresh()->quantity)->toBe(1);
});

test('a user cannot delete another user\'s cart item', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $item = makeCartItem($owner);

    $this->actingAs($attacker)
        ->delete(route('cart.delete', $item))
        ->assertForbidden();

    expect(OrderItem::find($item->id))->not->toBeNull();
});

test('the owner can update and delete their own cart item', function () {
    $owner = User::factory()->create();
    $item = makeCartItem($owner);

    $this->actingAs($owner)
        ->patch(route('cart.update', $item), ['action' => 'increase'])
        ->assertRedirect(route('cart.index'));

    expect($item->fresh()->quantity)->toBe(2);

    $this->actingAs($owner)
        ->delete(route('cart.delete', $item))
        ->assertRedirect(route('cart.index'));

    expect(OrderItem::find($item->id))->toBeNull();
});
