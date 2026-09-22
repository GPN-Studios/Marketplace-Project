<?php

use App\Models\Product;
use App\Models\User;

test('searching by name returns matching products only', function () {
    $seller = User::factory()->create();

    $match = Product::create([
        'user_id' => $seller->id,
        'name' => 'Fone Bluetooth XPTO',
        'description' => 'desc',
        'price' => 1000,
        'stock' => 5,
    ]);

    $noMatch = Product::create([
        'user_id' => $seller->id,
        'name' => 'Livro Clean Code',
        'description' => 'desc',
        'price' => 1000,
        'stock' => 5,
    ]);

    $this->get(route('search', ['q' => 'bluetooth']))
        ->assertOk()
        ->assertSee($match->name)
        ->assertDontSee($noMatch->name);
});

test('an empty search term lists all products', function () {
    $seller = User::factory()->create();

    Product::create([
        'user_id' => $seller->id,
        'name' => 'Produto A',
        'description' => 'desc',
        'price' => 1000,
        'stock' => 5,
    ]);

    $this->get(route('search'))
        ->assertOk()
        ->assertSee('Produto A');
});

test('a search term with no matches shows an empty state', function () {
    $this->get(route('search', ['q' => 'inexistente123']))
        ->assertOk()
        ->assertSee('Nenhum produto encontrado');
});
