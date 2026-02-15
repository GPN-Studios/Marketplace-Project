@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endsection

@section('content')

<div class="container cart-page">

    @auth
        <h2 class="cart-title">
            Carrinho de <span>{{ Auth::user()->name }}</span>
        </h2>
    @endauth

    @if($cart && $cart->items->count())

    <div class="cart-wrapper">

        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Qtd</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($cart->items as $item)
                <tr>
                    <!-- PRODUTO SEM IMAGEM, NOME SIMPLES -->
                    <td class="product-name">
                        {{ $item->product_name }}
                    </td>

                    <td class="product-price">
                        R$ {{ number_format($item->price, 2, ',', '.') }}
                    </td>

                    <td class="quantity">
                        {{ $item->quantity }}
                    </td>

                    <td class="actions d-flex gap-1">
                        <!-- Aumentar quantidade -->
                        <form action="{{ route('cart.update', $item) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="increase">
                            <button class="btn-icon increase">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </form>

                        <!-- Diminuir quantidade -->
                        <form action="{{ route('cart.update', $item) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="decrease">
                            <button class="btn-icon decrease">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                        </form>

                        <!-- Remover item -->
                        <form action="{{ route('cart.delete', $item) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn-icon delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    @else
        <div class="empty-cart">
            🛒 Seu carrinho está vazio
        </div>
    @endif

    @if ($cart)
        <form action="{{route('checkout', $cart)}}" method="POST">
            @csrf
            
            <button type="submit">Finalizar Compra</button>
        </form>
    @endif

</div>

@endsection
