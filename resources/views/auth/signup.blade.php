@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/signup.css') }}"> <!-- public/css/home.css -->
@endsection

@section('content')
@include('layouts.navbar')

<div class="login-box">
    <h2>Create Account</h2>

    <form action="{{'/register'}}" method="POST">
        @csrf
        <div class="input-group">
            <label>Usuário</label>
            <input type="text" name="name" id="name" value="{{old('name')}}">
            @error('name')
                <small class="error-message">{{ $message }}</small>
            @enderror
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" id="email" value="{{old('email')}}">
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

        <div class="input-group">
            <label>Confirmar Senha</label>
            <input type="password" name="password_confirmation" id="password_confirmation">
            @error('password_confirmation')
                <small class="error-message">{{ $message }}</small>
            @enderror
        </div>

        <button class="btn-primary" type="submit">
            Cadastrar
        </button>                                                   

    </form>
</div>


@endsection