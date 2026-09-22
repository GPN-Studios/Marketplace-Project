@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')

{{-- HERO COM BANNER CLICÁVEL --}}
<a href="{{ route('tags.show', 'eletronicos') }}" class="hero-banner"></a>

{{-- TAGS / CATEGORIAS --}}
<section class="tag-boxes">
    <div class="tag-boxes-container">

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

    <div class="product-carousel">

        <button type="button" class="carousel-btn carousel-btn--prev" aria-label="Produtos anteriores">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="tag-products">
            @foreach ($tag->products as $product)
            <a href="{{ route('products.show', $product ) }}"
               class="product-card text-decoration-none text-reset"
               draggable="false">

                <div class="card">
                    <div class="product-image-wrapper">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="" draggable="false">
                    </div>

                    <div class="card-body">
                        <p class="card-title">
                            {{ $product->name }}
                        </p>

                        <p class="card-text">
                            {{ config('shop.currency_symbol') }} {{ $product->price_formatted }}
                        </p>

                        <p class="seller-name">
                            Criado por {{ $product->user->name }}
                        </p>
                    </div>
                </div>

            </a>
            @endforeach
        </div>

        <button type="button" class="carousel-btn carousel-btn--next" aria-label="Próximos produtos">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

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

@section('scripts')
<script src="{{ asset('js/product-carousel.js') }}"></script>
@endsection