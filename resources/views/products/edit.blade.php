@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')

@auth

<div class="container product-edit">

    <div class="edit-header mb-5">
        <h1>Editar produto</h1>
        <p>Atualize as informações do seu produto</p>
    </div>

    <form
        method="POST"
        enctype="multipart/form-data"
        action="{{ route('products.update', $product) }}"
    >
        @csrf
        @method('PATCH')

        <div class="row gx-5 gy-5">

            <div class="col-lg-8">

                {{-- Informações básicas --}}
                <div class="edit-card">
                    <h3 class="card-title">Informações do produto</h3>

                    <div class="row gy-4 gx-4">

                        <div class="col-md-8">
                            <label class="form-label">Nome do produto</label>
                            <input
                                type="text"
                                class="form-control"
                                name="name"
                                value="{{ old('name', $product->name) }}"
                            >
                            @error('name')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Quantidade</label>
                            <input
                                type="number"
                                class="form-control"
                                name="stock"
                                min="0"
                                value="{{ old('stock', $product->stock) }}"
                            >
                            @error('stock')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descrição</label>
                            <textarea
                                class="form-control"
                                name="description"
                                rows="4"
                            >{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                {{-- Imagem --}}
                <div class="edit-card">
                    <h3 class="card-title">Imagem do produto</h3>

                    <input
                        type="file"
                        class="form-control"
                        name="image"
                    >

                    @error('image')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror

                    @if($product->image)
                        <small class="text-muted d-block mt-2">
                            Imagem atual será mantida se não enviar outra
                        </small>
                    @endif
                </div>

                {{-- Preço --}}
                <div class="edit-card">
                    <h3 class="card-title">Preço</h3>

                    <div class="price-box">
                        <span class="currency">R$</span>
                        <input
                            type="text"
                            class="form-control price-input"
                            name="price"
                            value="{{ old('price', $product->price) }}"
                        >
                    </div>

                    @error('price')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Botão --}}
                <div class="publish-box">
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        Salvar alterações
                    </button>
                    <small class="text-muted d-block text-center mt-3">
                        As alterações serão aplicadas imediatamente
                    </small>
                </div>

            </div>

        </div>
    </form>

</div>

@endauth

@endsection
