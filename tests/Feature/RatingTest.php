<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\Rating;
use App\Models\User;

function completedOrderItem(): array
{
    $seller = User::factory()->create();
    $buyer = User::factory()->create();

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Produto avaliável',
        'description' => 'desc',
        'price' => 1500,
        'stock' => 5,
    ]);

    $order = Order::create(['user_id' => $buyer->id, 'status' => OrderStatus::Completed, 'total' => 1500]);

    $item = $order->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'seller_id' => $seller->id,
        'quantity' => 1,
        'price' => 1500,
        'subtotal' => 1500,
    ]);

    return [$buyer, $seller, $item];
}

test('a buyer can rate an item from a completed order', function () {
    [$buyer, $seller, $item] = completedOrderItem();

    $this->actingAs($buyer)
        ->post(route('ratings.store', $item), [
            'is_positive' => '1',
            'description' => 'Ótimo vendedor!',
        ])
        ->assertRedirect();

    $rating = Rating::first();

    expect($rating)->not->toBeNull()
        ->and($rating->order_item_id)->toBe($item->id)
        ->and($rating->buyer_id)->toBe($buyer->id)
        ->and($rating->seller_id)->toBe($seller->id)
        ->and($rating->is_positive)->toBeTrue()
        ->and($rating->description)->toBe('Ótimo vendedor!');
});

test('an order that is not completed cannot be rated', function () {
    [$buyer, , $item] = completedOrderItem();
    $item->order->update(['status' => OrderStatus::Paid]);

    $this->actingAs($buyer)
        ->post(route('ratings.store', $item), ['is_positive' => '1'])
        ->assertForbidden();

    expect(Rating::count())->toBe(0);
});

test('the same item cannot be rated twice', function () {
    [$buyer, , $item] = completedOrderItem();

    $this->actingAs($buyer)->post(route('ratings.store', $item), ['is_positive' => '1']);

    $this->actingAs($buyer)
        ->post(route('ratings.store', $item), ['is_positive' => '0'])
        ->assertForbidden();

    expect(Rating::count())->toBe(1);
});
