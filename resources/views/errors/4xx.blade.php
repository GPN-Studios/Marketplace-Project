@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout_result.css') }}">
@endsection

@section('content')
    <div class="checkout-result">
        <div class="checkout-result-icon cancelled"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h1>Não foi possível continuar</h1>
        <p class="subtitle">{{ $exception->getMessage() ?: 'Não foi possível concluir sua solicitação.' }}</p>
        <div class="checkout-result-actions">
            <a href="{{ url()->previous() }}" class="btn-site">Voltar</a>
            <a href="{{ route('home') }}" class="btn-site-outline">Voltar à loja</a>
        </div>
    </div>
@endsection
