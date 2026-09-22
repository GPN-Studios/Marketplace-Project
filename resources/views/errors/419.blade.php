@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout_result.css') }}">
@endsection

@section('content')
    <div class="checkout-result">
        <div class="checkout-result-icon cancelled"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <h1>Sessão expirada</h1>
        <p class="subtitle">Sua página ficou aberta por muito tempo. Volte e tente novamente.</p>
        <div class="checkout-result-actions">
            <a href="{{ url()->previous() }}" class="btn-site">Voltar</a>
        </div>
    </div>
@endsection
