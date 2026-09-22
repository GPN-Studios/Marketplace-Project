<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;
use Spatie\Tags\Tag;

class DashboardController extends Controller
{
    public function home(): View
    {
        $tags = Tag::all();

        // Busca um lote único dos produtos mais recentes (com tags e vendedor
        // já carregados) e agrupa em memória, em vez de uma query por tag —
        // evita N+1. Para uma home "mais recentes por categoria", um lote
        // generoso dos últimos produtos é suficiente.
        $recentProducts = Product::query()
            ->with(['tags', 'user'])
            ->latest()
            ->take((int) config('shop.home.recent_pool'))
            ->get();

        $tags = $tags->map(function (Tag $tag) use ($recentProducts) {
            $tag->products = $recentProducts
                ->filter(fn (Product $product) => $product->tags->contains('id', $tag->id))
                ->take((int) config('shop.home.per_category'))
                ->values();

            return $tag;
        });

        return view('dashboard', compact('tags'));
    }
}
