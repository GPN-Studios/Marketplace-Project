@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user_products.css') }}">
@endsection

@section('content')

<div class="my-products-container">

    <h2 class="page-title">Meus Produtos</h2>

    <div class="products-grid">

        @forelse ($user->products as $product)

        <div class="product-card">

            <div class="product-image">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            </div>

            <div class="product-info">

                <h3 class="product-name">{{ $product->name }}</h3>

                <p class="product-description">
                    <strong>Descrição:</strong><br>
                    {{ $product->description }}
                </p>

                <div class="product-meta">
                    <span class="price">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </span>

                    <span class="stock">
                        Estoque: {{ $product->stock }}
                    </span>
                </div>

            </div>

            <div class="product-actions">

                <a href="{{ route('products.edit', $product) }}" class="btn-edit">
                    <i class="fa-solid fa-pen"></i> Editar
                </a>

                <form action="{{ route('products.delete', $product) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn-delete">
                        <i class="fa-solid fa-trash"></i> Excluir
                    </button>
                </form>

            </div>

        </div>

        @empty
            <p class="empty-text">Você ainda não cadastrou nenhum produto.</p>
        @endforelse

    </div>

</div>

@endsection
