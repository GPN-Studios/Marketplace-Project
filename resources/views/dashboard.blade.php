@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">    <!-- public/css/home.css -->
@endsection
@section('content')


@auth
<p>  {{Auth::user()->name;}}  </p>

@endauth
<div class="content d-flex">
@foreach ($products as $product)

    <a href="{{ route('products.show', encrypt($product->id)) }}" class="text-decoration-none text-reset">
         <div class="card me-4" style="width: 12rem;">
            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="...">
        <div class="card-body">
            <p class="card-title ">{{ $product->name }}</p>
            <p class="card-text">R$ {{ $product->price }}</p>
        </div>
        </a>
    </div>

@endforeach
</div>


@endsection