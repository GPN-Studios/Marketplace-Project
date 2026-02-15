<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Rating;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RatingController extends Controller
{
    public function store(Request $request, OrderItem $orderItem) : RedirectResponse
    {
        $this->authorize('create', $orderItem);

        $validated = $request->validate([
            'is_positive' => ['required', 'boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Rating::create([
            'order_item_id' => $orderItem->id,
            'buyer_id'      => auth()->id(),
            'seller_id'     => $orderItem->product->user_id,
            'is_positive'   => $validated['is_positive'],
            'description'  => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Avaliação enviada com sucesso!');
    }
}
