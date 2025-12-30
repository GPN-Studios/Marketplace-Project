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
         <div class="card me-4" style="width: 18rem;">
            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text">{{ $product->description }}</p>
        </div>
        </a>
    </div>
@endforeach
</div>


@endsection