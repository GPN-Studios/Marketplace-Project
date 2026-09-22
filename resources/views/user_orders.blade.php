@extends('layouts.main_layout')

@php use App\Enums\OrderStatus; @endphp

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user_orders.css') }}">
@endsection

@section('content')

<div class="container orders-page">

    <h1 class="orders-page-title">Meus Pedidos</h1>

    @if ($orders->count())
        <div class="orders-list">
            @foreach ($orders as $order)
                <div class="order-card">
                    <div class="order-card-header">
                        <div>
                            <span class="order-number">Pedido #{{ $order->id }}</span>
                            <span class="order-date">{{ $order->created_at->format('d/m/Y') }}</span>
                        </div>
                        <span class="order-status order-status-{{ $order->status->value }}">
                            {{ $order->status->label() }}
                        </span>
                    </div>

                    <ul class="order-items-list">
                        @foreach ($order->items as $item)
                            <li class="order-item-row">
                                <img
                                    src="{{ $item->product?->image ? asset('storage/'.$item->product->image) : asset('imgs/logo-verde.png') }}"
                                    class="order-item-thumb"
                                    alt="{{ $item->product_name }}"
                                >

                                <div class="order-item-info">
                                    <span class="order-item-name">{{ $item->product_name }}</span>
                                    <span class="order-item-meta">Qtd: {{ $item->quantity }} &middot; {{ config('shop.currency_symbol') }} {{ $item->price_formatted }}</span>
                                </div>

                                <span class="order-item-subtotal">{{ config('shop.currency_symbol') }} {{ $item->subtotal_formatted }}</span>

                                @if ($order->status === OrderStatus::Completed)
                                    <div class="order-item-rating">
                                        @if ($item->rating)
                                            <span class="rating-done">
                                                <i class="fa-solid fa-circle-check"></i>
                                                Avaliado ({{ $item->rating->is_positive ? 'positiva' : 'negativa' }})
                                            </span>
                                        @else
                                            <form action="{{ route('ratings.store', $item) }}" method="POST" class="rating-form">
                                                @csrf
                                                <div class="rating-form-choices">
                                                    <label>
                                                        <input type="radio" name="is_positive" value="1" required> Positiva
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="is_positive" value="0" required> Negativa
                                                    </label>
                                                </div>
                                                <textarea name="description" maxlength="1000" placeholder="Comentário (opcional)"></textarea>
                                                <button type="submit" class="btn-site-outline">Avaliar</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <div class="order-card-footer">
                        <span class="order-total">Total: <strong>{{ config('shop.currency_symbol') }} {{ $order->total_formatted }}</strong></span>

                        @if ($order->status === OrderStatus::Pending)
                            <form action="{{ route('orders.checkout', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-site">Finalizar compra</button>
                            </form>
                        @endif

                        @if ($order->status === OrderStatus::Paid)
                            <form action="{{ route('checkout.complete', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-site">Confirmar recebimento</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="empty-orders">
            <i class="fa-solid fa-box-open"></i>
            <p>Você não tem pedidos em andamento.</p>
            <a href="{{ route('home') }}" class="btn-site">Ver produtos</a>
        </div>
    @endif

</div>

@endsection
