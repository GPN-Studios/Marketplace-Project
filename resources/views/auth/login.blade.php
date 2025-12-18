@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}"> <!-- public/css/home.css -->
@endsection

@section('content')
    @include('layouts.navbar')

    <div class="login-box">
        <h2>Login</h2>
        <form method="POST" action="{{route('login')}}">
            @csrf

            <div class="input-group">
                <label>E-mail</label>
                <input type="text" name="email" id="email" value="{{ old("email") }}">
                @error('email')
                    <small class="error-message">{{ $message }}</small>
                @enderror
            </div>

            <div class="input-group">
                <label>Senha</label>
                <input type="password" name="password" id="password">
                @error('password')
                    <small class="error-message">{{ $message }}</small>
                @enderror

            </div>

            <button class="btn-primary" type="submit">
                Entrar
            </button>

            <div class="divider">
                <span>ou</span>
            </div>

            <a href="{{ route('signup') }}" class="btn-secondary">
                Cadastrar
            </a>

            <a href="#" class="btn-secondary">
                Esqueceu a senha?
            </a>
        </form>
    </div>

@endsection