@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')

{{-- HERO COM DEGRADE --}}
<section class="hero-gradient">
    <div class="hero-content">
        <h1 class="hero-title">
            Ofertas imperdíveis pra você
        </h1>
        <p class="hero-subtitle">
            Produtos selecionados com o melhor preço e entrega rápida.
        </p>
    </div>
</section>

{{-- TAGS / CATEGORIAS --}}
<section class="tag-boxes">
    <div class="tag-boxes-container">

        <h2 class="tag-boxes-title">
            Categorias
        </h2>

        <div class="tag-boxes-grid">
            @foreach ($tags as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}"
                   class="tag-box">

                    <p class="tag-box-name">
                        {{ $tag->name }}
                    </p>

                    <div class="tag-box-image">
                        <img src="{{ asset('imgs/tags/' . $tag->slug . '.png') }}"
                             alt="{{ $tag->name }}">
                    </div>

                    <span class="tag-box-action">
                        Ver produtos →
                    </span>

                </a>
            @endforeach
        </div>

    </div>
</section>

{{-- CONTEÚDO ORIGINAL --}}
<div class="content d-flex flex-column gap-5">

@foreach ($tags as $tag)

@if ($tag->products->count())
<div class="tag-section">

    <h3 class="tag-title">{{ $tag->name }}</h3>

    <div class="tag-products">
        @foreach ($tag->products as $product)
        <a href="{{ route('products.show', encrypt($product->id)) }}"
           class="product-card text-decoration-none text-reset">

            <div class="card">
                <div class="product-image-wrapper">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="">
                </div>

                <div class="card-body">
                    <p class="card-title">
                        {{ $product->name }}
                    </p>

                    <p class="card-text">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </p>

                    <p class="seller-name">
                        Criado por {{ $product->user->name }}
                    </p>
                </div>
            </div>

        </a>
        @endforeach
    </div>

    <div class="tag-more">
        <a href="{{ route('tags.show', $tag->slug) }}">
            Ver mais
        </a>
    </div>

</div>
@endif

@endforeach

</div>

@endsection
