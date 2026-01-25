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
        const toast = document.querySelector('.custom-toast');
        if (toast) toast.classList.remove('show');
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

            <div class="rating">
                ⭐ 4.8 <span>(1574 avaliações)</span>
            </div>

            <div class="stock">
                Estoque disponível: {{ $product->stock }}
            </div>

            <div class="seller-info">
                Vendido e entregue por
                <a href="{{ route('profile', $product->user->id) }}" class="seller-link">
                    <strong>{{ $product->user->name }}</strong>
                </a>
            </div>

            <div class="tags">
                Tags:
                @foreach ($product->tags as $tag)
                    {{ $tag->name }}
                @endforeach
            </div>
        </div>

        {{-- CAIXA DE COMPRA --}}
        <div class="col-lg-3">
            <div class="buy-box">
                <div class="old-price">R$ 2.657,40</div>

                <div class="price">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </div>

                <div class="pix">
                    no Pix
                </div>

                <form action="{{ route('cart.add', encrypt($product->id)) }}" method="POST">
                    @csrf
                    <button class="btn btn-success w-100 mb-2">
                        Adicionar ao carrinho
                    </button>
                    <button class="btn btn-outline-primary w-100">
                        Comprar agora
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- DESCRIÇÃO --}}
    <div class="description-box">
        <h3>Descrição do produto</h3>
        <p>{{ $product->description }}</p>
    </div>  

</div>

@endsection
