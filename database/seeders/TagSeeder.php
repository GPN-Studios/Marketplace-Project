<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Tags\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Livros',
            'Casa',
            'Eletrônicos',
            'Computadores',
            'Games',
            'Brinquedos',
            'Beleza',
            'Alimentos',
        ];

        foreach ($tags as $tag) {
            Tag::findOrCreate($tag);
        }
    }
}