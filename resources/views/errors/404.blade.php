@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout_result.css') }}">
@endsection

@section('content')
    <div class="checkout-result">
        <div class="checkout-result-icon cancelled"><i class="fa-solid fa-magnifying-glass"></i></div>
        <h1>Página não encontrada</h1>
        <p class="subtitle">O endereço que você tentou acessar não existe ou foi removido.</p>
        <div class="checkout-result-actions">
            <a href="{{ route('home') }}" class="btn-site">Voltar à loja</a>
        </div>
    </div>
@endsection
