@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">    <!-- public/css/home.css -->
@endsection
@section('content')


@auth
<p> Carrinho de {{Auth::user()->name;}}  </p>
@endauth

@if($cart)
<div class="row">
    <div class="col-12 d-flex">

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Preço</th>
                    <th scope="col">Quantidade</th>
                </tr>
            </thead>
            @foreach ($cart->items as $item)
                <tr>
                    <th scope="row"></th>
                    <td>{{$item->product_name}}</td>
                    <td>{{$item->price}}</td>
                    <td>{{$item->quantity}}</td>
                    <td class="d-flex me-2">
                    <form action="{{ route('cart.update' , $item) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="action" value="increase">

                        <button type="submit" class="me-2"><i class="fa-solid fa-arrow-up p-1"></i></button>
                    
                    </form>
                    <form action="{{ route('cart.update' , $item) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="action" value="decrease">

                        <button type="submit"><i class="fa-solid fa-arrow-down p-1"></i></button>
                    </form>

                    <form action="{{ route('cart.delete', $item) }}" method="POST">
                        @csrf

                        <button type="submit" class="bg-danger ms-4"><i class="bg-danger p-1 pe-2">DELETE</i></button>

                    </form>
                    </td>

                </tr>
            @endforeach






    </div>

</div>
    
@else
    <p>Nada aqui por enquanto!</p>
@endif






@endsection