@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection
@section('content')

@auth
<h6>  {{Auth::user()->name;}}  </h6>
@endauth

<h6 class="pb-1">Vendedor: {{ $product->user->name }}</h6>
<div class="row d-flex">
    <div class="image-box col-6">
        <img src="{{ asset('storage/' . $product->image) }}" alt="">
    </div>
    <div class="col-6">
        <h3 class="pb-4"> {{ $product->name }} </h3>
        <div class="col-6">
            <h5>Estoque: {{ $product->stock }}</h5>
        </div>
        <div class="col-6">
            <form class="d-flex align-items-center" action="{{route('cart.add', encrypt($product->id)) }}" method="POST">
                @csrf
                <input type="hidden" value="">

                <span class="fw-bold fs-5 text-nowrap me-4">
                    R$ {{ $product->price }}
                </span>
                <button class="btn btn-primary btn-lg" type="submit">
                    COMPRAR
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row d-flex mt-5">
    <div class="col-12">
        <h3>DESCRIÇÃO DO ANÚNCIO</h3>
    </div>
    <div class="col-12">
        <p class="fs-4">{{ $product->description }}</p>
    </div>

</div>




@endsection
