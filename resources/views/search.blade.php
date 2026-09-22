@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')

<div class="content d-flex flex-column gap-4">

    <div class="tag-section-header container">
        <h1 class="tag-title mb-0">
            @if ($term !== '')
                Resultados para "{{ $term }}"
            @else
                Todos os produtos
            @endif
        </h1>
    </div>

    @if ($products->count())
        <div class="tag-products container">
            @foreach ($products as $product)
                <a href="{{ route('products.show', $product) }}"
                   class="product-card text-decoration-none text-reset">

                    <div class="card">
                        <div class="product-image-wrapper">
                            <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('imgs/logo-verde.png') }}" alt="{{ $product->name }}">
                        </div>

                        <div class="card-body">
                            <p class="card-title">{{ $product->name }}</p>
                            <p class="card-text">{{ config('shop.currency_symbol') }} {{ $product->price_formatted }}</p>
                            <p class="seller-name">Criado por {{ $product->user->name }}</p>
                        </div>
                    </div>

                </a>
            @endforeach
        </div>

        <div class="container my-4 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="container">
            <p class="text-muted">Nenhum produto encontrado para essa busca.</p>
        </div>
    @endif

</div>

@endsection
