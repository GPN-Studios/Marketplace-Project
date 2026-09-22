@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout_result.css') }}">
@endsection

@section('content')
    <div class="checkout-result">
        <div class="checkout-result-icon cancelled"><i class="fa-solid fa-lock"></i></div>
        <h1>Acesso não autorizado</h1>
        <p class="subtitle">Você não tem permissão para acessar esta página.</p>
        <div class="checkout-result-actions">
            <a href="{{ route('home') }}" class="btn-site">Voltar à loja</a>
        </div>
    </div>
@endsection
