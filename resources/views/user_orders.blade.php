@extends('layouts.main_layout')

@section('content')


@if ($orders->count())
@foreach ($orders as $order)
    <h3>Pedido #{{ $order->id }}</h3>

    <ul>
        @foreach ($order->items as $item)
            <li>
                {{ $item->product->name }} —
                Qtd: {{ $item->quantity }} —
                R$ {{ number_format($item->price / 100, 2, ',', '.') }}
            </li>
        @endforeach
    </ul>

    <strong>
        Total:
        R$ {{ number_format($order->total / 100, 2, ',', '.') }}
    </strong>

    @if ($order->status === 'pending')
        <form action="{{ route('orders.checkout', $order) }}" method="POST">
            @csrf
            <button type="submit">
                Finalizar compra
            </button>
        </form>
    @endif

    <hr>
@endforeach
{{ $orders->links() }}   

@else
Você não tem pedidos incompletos.
    
@endif

@endsection