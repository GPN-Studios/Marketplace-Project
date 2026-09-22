<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('the owner can update their product, including replacing the image', function () {
    Storage::fake('public');

    $owner = User::factory()->create();

    $product = Product::create([
        'user_id' => $owner->id,
        'name' => 'Produto original',
        'description' => 'desc',
        'price' => 1000,
        'stock' => 5,
        'image' => 'products/old.jpg',
    ]);

    $newImage = UploadedFile::fake()->image('novo.jpg');

    $this->actingAs($owner)
        ->patch(route('products.update', $product), [
            'name' => 'Produto atualizado',
            'image' => $newImage,
        ])
        ->assertRedirect(route('products.show', $product));

    $product->refresh();

    expect($product->name)->toBe('Produto atualizado');
    Storage::disk('public')->assertExists($product->image);
    expect($product->image)->not->toBe('products/old.jpg');
});

test('a user who is not the owner cannot update the product', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $product = Product::create([
        'user_id' => $owner->id,
        'name' => 'Produto original',
        'description' => 'desc',
        'price' => 1000,
        'stock' => 5,
    ]);

    $this->actingAs($attacker)
        ->patch(route('products.update', $product), ['name' => 'Hackeado'])
        ->assertForbidden();

    expect($product->fresh()->name)->toBe('Produto original');
});
