@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')

@if (session('success'))
<div class="toast-fixed-wrapper">
    <div class="toast custom-toast show">
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
</div>
<script>
    setTimeout(() => {
        document.querySelector('.custom-toast')?.classList.remove('show');
    }, 3000);
</script>
@endif

<div class="container product-show">

    <div class="row g-4">

        {{-- GALERIA --}}
        <div class="col-lg-5">
            <div class="product-gallery">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            </div>
        </div>

        {{-- INFO PRODUTO --}}
        <div class="col-lg-4">
            <h1 class="product-title">{{ $product->name }}</h1>

            <div class="stock">
                Estoque disponível: {{ $product->stock }}
            </div>

            <div class="seller-info">
                Vendido por
                <a href="{{ route('profile', $product->user->id) }}" class="seller-link">
                    <strong>{{ $product->user->name }}</strong>
                </a>
                <span class="seller-reputation">
                    ({{ $sellerPositiveRatings }} 👍 / {{ $sellerNegativeRatings }} 👎)
                </span>
            </div>

            {{-- TAGS --}}
            <div class="tags">
                @foreach ($product->tags as $tag)
                    <span>{{ $tag->name }}</span>
                @endforeach
            </div>

            {{-- SOBRE O ITEM --}}
            <div class="product-about">
                <h3>Sobre este item</h3>
                <p>{{ $product->description }}</p>
            </div>
        </div>

        {{-- COMPRA + AÇÕES --}}
        <div class="col-lg-3">

            {{-- CAIXA DE COMPRA --}}
            <div class="buy-box">

                <div class="price">
                    {{ config('shop.currency_symbol') }} {{ $product->price_formatted }}
                </div>

                <div class="pix">no Pix</div>

                @if ($product->stock > 0)
                    <form action="{{ route('cart.add', $product) }}" method="POST">
                        @csrf

                        <div class="product-quantity">
                            <div class="quantity-header">
                                <label for="quantity">Quantidade</label>
                                <span class="available-stock">
                                    Disponível: {{ $product->stock }}
                                </span>
                            </div>

                            <select name="quantity" id="quantity">
                                @for ($i = 1; $i <= min(10, $product->stock); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <button class="btn btn-success w-100">
                            Adicionar ao carrinho
                        </button>
                    </form>
                @else
                    <div class="text-muted text-center py-2">Produto esgotado</div>
                @endif
            </div>

            {{-- BOTÕES EDITAR / EXCLUIR (FORA DA CAIXA) --}}
            @can('update', $product)
            <div class="owner-actions">

                <a href="{{ route('products.edit', $product) }}"
                   class="btn btn-outline-primary w-100">
                    Editar produto
                </a>

                @can('delete', $product)
                <form action="{{ route('products.delete', $product) }}"
                      method="POST"
                      onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-outline-danger w-100">
                        Excluir produto
                    </button>
                </form>
                @endcan

            </div>
            @endcan

        </div>

    </div>
</div>

@endsection
