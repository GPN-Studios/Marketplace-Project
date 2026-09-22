@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')

<div class="content d-flex flex-column gap-4">

    <div class="tag-section-header container">
        <h1 class="tag-title mb-0">{{ $tag->name }}</h1>
    </div>

    @if ($products->count())
        <div class="tag-products container">
            @foreach ($products as $product)
                <a href="{{ route('products.show', $product) }}"
                   class="product-card text-decoration-none text-reset">

                    <div class="card">
                        <div class="product-image-wrapper">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
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
            <p class="text-muted">Ainda não há produtos nesta categoria.</p>
        </div>
    @endif

</div>

@endsection
