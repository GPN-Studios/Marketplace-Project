@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout_result.css') }}">
@endsection

@section('content')
    <div class="checkout-result">
        <div class="checkout-result-icon cancelled"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h1>Algo deu errado</h1>
        <p class="subtitle">Não foi possível concluir sua solicitação. Tente novamente em instantes.</p>
        <div class="checkout-result-actions">
            <a href="{{ route('home') }}" class="btn-site">Voltar à loja</a>
        </div>
    </div>
@endsection
