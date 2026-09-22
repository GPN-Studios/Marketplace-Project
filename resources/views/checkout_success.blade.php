@extends('layouts.main_layout')

@php use App\Enums\OrderStatus; @endphp

@section('styles')
<link rel="stylesheet" href="{{ asset('css/checkout_result.css') }}">
@endsection

@section('content')

<div class="checkout-result">
    <div class="checkout-result-icon success">
        <i class="fa-solid fa-check"></i>
    </div>

    @if ($order->status === OrderStatus::Pending)
        <h1>Pagamento em processamento</h1>
        <p class="subtitle">
            Recebemos a confirmação do Stripe e estamos finalizando seu pedido. Isso costuma levar só alguns segundos.
        </p>
    @else
        <h1>Pagamento confirmado!</h1>
        <p class="subtitle">Pedido #{{ $order->id }} — obrigado pela compra.</p>
    @endif

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
        <a href="{{ route('user.orders') }}" class="btn-site">Ver meus pedidos</a>
        <a href="{{ route('home') }}" class="btn-site-outline">Voltar à loja</a>
    </div>
</div>

@endsection
