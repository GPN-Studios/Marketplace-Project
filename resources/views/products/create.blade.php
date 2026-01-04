@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">    <!-- public/css/home.css -->
@endsection
@section('content')


@auth

<div class="container">
    <form method="POST" enctype="multipart/form-data" action="{{ route('products.store') }}" class="row g-3 border rounded">
    @csrf
    <div class="col-6">
      <label for="product_name" class="form-label">Nome do Produto</label>
      <input type="name" class="form-control" name="name" id="product_name" placeholder="Digite o nome do produto..." value="{{ old('name') }}">

      @error('name')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div class="col-6">
      <label for="img" class="form-label">Escolha a imagem do produto</label>
      <input type="file" class="form-control" name="image" id="img">

      @error('image')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div class="col-12">
      <label for="description" class="form-label">Descrição</label>
      <input type="text" class="form-control" name="description" id="description" placeholder="Descrição..." value="{{ old('description') }}">

      @error('description')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div class="col-6 col-md-2">
      <label for="price" class="form-label">Preço</label>
      <input type="text" class="form-control" name="price" id="price" value="{{ old('price') }}" placeholder="0.00" step="0.01">

      @error('price')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div class="col-6 col-md-2">
      <label for="stock" class="form-label">Quantidade</label>
      <input type="number" name="stock" id="stock" class="form-control" min="0" max="999" value="{{ old('stock') }}" placeholder="0">

      @error('stock')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div class="col-12 text-center">
      <button type="submit" class="btn btn-primary mb-3 btn-lg">Criar</button>
    </div>
</form>
</div>





@endauth

@endsection
