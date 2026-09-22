@extends('layouts.guest_layout')

@section('title', 'Recuperar senha')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

    <div class="auth-wrapper">

        <a href="{{ route('home') }}" class="auth-brand">
            <img src="{{ asset('imgs/logo-verde-azulado.png') }}" alt="Ideal Marketplace">
        </a>

        <div class="login-box">
            <h2>Recuperar senha</h2>
            <p class="auth-subtitle">Informe seu e-mail e enviaremos um link para redefinir sua senha</p>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="input-group">
                    <label for="email">E-mail</label>
                    <div class="input-icon-group">
                        <i class="fa-solid fa-envelope field-icon"></i>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="username" autofocus required>
                    </div>
                    <div class="error-box">
                        @error('email')
                            <small class="error-message text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <button class="btn-primary" type="submit">
                    Enviar link de recuperação
                </button>

                <div class="divider">
                    <span>ou</span>
                </div>

                <a href="{{ route('login') }}" class="btn-secondary">
                    Voltar ao login
                </a>
            </form>
        </div>

        <p class="auth-footer-link">
            <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left-long me-1"></i> Voltar à loja</a>
        </p>

    </div>

@endsection
