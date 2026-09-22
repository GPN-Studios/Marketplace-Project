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
                    <td class="product-name">
                        <img
                            src="{{ $item->product?->image ? asset('storage/'.$item->product->image) : asset('imgs/logo-verde.png') }}"
                            class="product-image"
                            alt="{{ $item->product_name }}"
                        >
                        {{ $item->product_name }}
                    </td>

                    <td class="product-price">
                        {{ config('shop.currency_symbol') }} {{ $item->price_formatted }}
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

    <div class="cart-address">
        <h3 class="cart-address-title">Endereço de entrega</h3>

        <form action="{{ route('checkout', $cart) }}" method="POST" class="cart-address-form">
            @csrf

            <div class="cart-address-grid">
                <div class="field field-wide">
                    <label>Destinatário</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" required>
                    @error('recipient_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="field">
                    <label>CEP</label>
                    <input type="text" name="cep" maxlength="8" value="{{ old('cep') }}" required>
                    @error('cep') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="field">
                    <label>Estado</label>
                    <input type="text" name="state" maxlength="2" value="{{ old('state') }}" required>
                    @error('state') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="field field-wide">
                    <label>Cidade</label>
                    <input type="text" name="city" value="{{ old('city') }}" required>
                    @error('city') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="field field-wide">
                    <label>Bairro</label>
                    <input type="text" name="district" value="{{ old('district') }}" required>
                    @error('district') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="field field-wide">
                    <label>Rua</label>
                    <input type="text" name="street" value="{{ old('street') }}" required>
                    @error('street') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="field">
                    <label>Número</label>
                    <input type="text" name="number" value="{{ old('number') }}">
                </div>

                <div class="field field-wide">
                    <label>Complemento</label>
                    <input type="text" name="complement" value="{{ old('complement') }}">
                </div>
            </div>

            <button type="submit" class="btn-site cart-checkout-btn">Finalizar Compra</button>
        </form>
    </div>

    @else
        <div class="empty-cart">
            🛒 Seu carrinho está vazio
        </div>
    @endif

</div>

@endsection
