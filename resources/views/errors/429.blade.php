@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout_result.css') }}">
@endsection

@section('content')
    <div class="checkout-result">
        <div class="checkout-result-icon cancelled"><i class="fa-solid fa-hourglass-half"></i></div>
        <h1>Muitas tentativas</h1>
        <p class="subtitle">Você fez muitas tentativas em pouco tempo. Aguarde um minuto e tente novamente.</p>
        <div class="checkout-result-actions">
            <a href="{{ route('home') }}" class="btn-site">Voltar à loja</a>
        </div>
    </div>
@endsection
