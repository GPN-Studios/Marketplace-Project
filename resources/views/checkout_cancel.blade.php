@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/checkout_result.css') }}">
@endsection

@section('content')

<div class="checkout-result">
    <div class="checkout-result-icon cancelled">
        <i class="fa-solid fa-xmark"></i>
    </div>

    <h1>Pagamento cancelado</h1>
    <p class="subtitle">
        O pagamento do pedido #{{ $order->id }} não foi concluído e o estoque reservado foi devolvido.
        Você pode tentar novamente quando quiser.
    </p>

    <div class="checkout-result-card">
        <ul>
            @foreach ($order->items as $item)
                <li>
                    <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span>{{ config('shop.currency_symbol') }} {{ $item->subtotal_formatted }}</span>
                </li>
            @endforeach
        </ul>

        <div class="checkout-result-total">
            <span>Total</span>
            <span>{{ config('shop.currency_symbol') }} {{ $order->total_formatted }}</span>
        </div>
    </div>

    <div class="checkout-result-actions">
        <a href="{{ route('cart.index') }}" class="btn-site">Voltar ao carrinho</a>
        <a href="{{ route('home') }}" class="btn-site-outline">Voltar à loja</a>
    </div>
</div>

@endsection
