@extends('layouts.guest_layout')

@section('title', 'Confirme seu e-mail')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

    <div class="auth-wrapper">

        <a href="{{ route('home') }}" class="auth-brand">
            <img src="{{ asset('imgs/logo-verde-azulado.png') }}" alt="Ideal Marketplace">
        </a>

        <div class="login-box">
            <h2>Confirme seu e-mail</h2>
            <p class="auth-subtitle">
                Enviamos um link de confirmação para <strong>{{ auth()->user()->email }}</strong>.
                Verifique sua caixa de entrada para poder anunciar produtos, comprar e sacar saldo.
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success" role="alert">
                    Um novo link de confirmação foi enviado para o seu e-mail.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="btn-primary" type="submit">
                    Reenviar e-mail de confirmação
                </button>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-secondary" type="submit">
                    Sair da conta
                </button>
            </form>
        </div>

        <p class="auth-footer-link">
            <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left-long me-1"></i> Voltar à loja</a>
        </p>

    </div>

@endsection
