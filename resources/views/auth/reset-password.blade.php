@extends('layouts.guest_layout')

@section('title', 'Redefinir senha')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

    <div class="auth-wrapper">

        <a href="{{ route('home') }}" class="auth-brand">
            <img src="{{ asset('imgs/logo-verde-azulado.png') }}" alt="Ideal Marketplace">
        </a>

        <div class="login-box">
            <h2>Redefinir senha</h2>
            <p class="auth-subtitle">Escolha uma nova senha para sua conta</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="input-group">
                    <label for="email">E-mail</label>
                    <div class="input-icon-group">
                        <i class="fa-solid fa-envelope field-icon"></i>
                        <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}" autocomplete="username" autofocus required>
                    </div>
                    <div class="error-box">
                        @error('email')
                            <small class="error-message text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Nova senha</label>
                    <div class="input-icon-group has-toggle">
                        <i class="fa-solid fa-lock field-icon"></i>
                        <input type="password" name="password" id="password" autocomplete="new-password" required>
                        <button type="button" class="password-toggle" data-target="password" aria-label="Mostrar senha">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-box">
                        @error('password')
                            <small class="error-message text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="input-group">
                    <label for="password_confirmation">Confirmar nova senha</label>
                    <div class="input-icon-group has-toggle">
                        <i class="fa-solid fa-lock field-icon"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" required>
                        <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Mostrar senha">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button class="btn-primary" type="submit">
                    Redefinir senha
                </button>
            </form>
        </div>

        <p class="auth-footer-link">
            <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left-long me-1"></i> Voltar à loja</a>
        </p>

    </div>

@endsection

@section('scripts')
    <script>
        document.querySelectorAll('.password-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.target);
                var icon = btn.querySelector('i');

                if (!input) return;

                var isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';

                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        });
    </script>
@endsection
