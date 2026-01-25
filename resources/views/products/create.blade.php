@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endsection

@section('content')

@auth

<div class="container product-create">

    <div class="create-header mb-5">
        <h1>Anunciar produto</h1>
        <p>Preencha as informações abaixo para publicar seu anúncio</p>
    </div>

    <form
        method="POST"
        enctype="multipart/form-data"
        action="{{ route('products.store') }}"
    >
        @csrf

        <div class="row gx-5 gy-5">

            <div class="col-lg-8">

                {{-- Basic Info --}}
                <div class="create-card">
                    <h3 class="card-title">Informações do produto</h3>

                    <div class="row gy-4 gx-4">

                        <div class="col-md-8">
                            <label class="form-label">Nome do produto</label>
                            <input
                                type="text"
                                class="form-control"
                                name="name"
                                placeholder="Nomeie seu produto..."
                                value="{{ old('name') }}"
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
                                placeholder="0"
                                value="{{ old('stock') }}"
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
                                placeholder="Descreva seu produto..."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Tags --}}
                <div class="create-card">
                    <h3 class="card-title">Categorias e tags</h3>

                    <div class="tags-container">
                        @foreach ($tags as $tag)
                            <label class="tag-item">
                                <input
                                    type="checkbox"
                                    name="tags[]"
                                    value="{{ $tag->name }}"
                                    {{ in_array($tag->name, old('tags', [])) ? 'checked' : '' }}
                                >
                                <span>{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                {{-- Image --}}
                <div class="create-card">
                    <h3 class="card-title">Imagem do produto</h3>

                    <input
                        type="file"
                        class="form-control"
                        name="image"
                    >

                    @error('image')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Price --}}
                <div class="create-card">
                    <h3 class="card-title">Preço</h3>

                    <div class="price-box">
                        <span class="currency">R$</span>
                        <input
                            type="text"
                            class="form-control price-input"
                            name="price"
                            placeholder="0,00"
                            value="{{ old('price') }}"
                        >
                    </div>

                    @error('price')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Button --}}
                <div class="publish-box">
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        Publicar anúncio
                    </button>
                    <small class="text-muted d-block text-center mt-3">
                        Seu produto ficará visível imediatamente
                    </small>
                </div>

            </div>

        </div>
    </form>

</div>

@endauth

@endsection
