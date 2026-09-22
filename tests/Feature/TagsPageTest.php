<?php

use App\Models\Product;
use App\Models\User;
use Spatie\Tags\Tag;

test('the tag page renders and lists tagged products', function () {
    $seller = User::factory()->create();
    $tag = Tag::findOrCreate('Livros');

    $product = Product::create([
        'user_id' => $seller->id,
        'name' => 'Dom Casmurro',
        'description' => 'Livro clássico',
        'price' => 2990,
        'stock' => 10,
    ]);
    $product->attachTag($tag);

    $this->get(route('tags.show', $tag->slug))
        ->assertOk()
        ->assertSee('Dom Casmurro');
});

test('a tag with no products still renders with an empty state', function () {
    $tag = Tag::findOrCreate('Sem Produtos');

    $this->get(route('tags.show', $tag->slug))
        ->assertOk();
});
