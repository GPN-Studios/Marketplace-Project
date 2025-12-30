@extends('layouts.guest_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/signup.css') }}"> <!-- public/css/home.css -->
@endsection

@section('content')

<div class="login-box">
    <h2>Create Account</h2>

    <form action="{{'/register'}}" method="POST">
        @csrf
        <div class="input-group">
            <label for="name">Usuário</label>
            <input type="text" name="name" id="name" value="{{old('name')}}">
            <div class="error-box d-block">
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
            </div>
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{old('email')}}">
            <div class="error-box d-block">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="input-group">
            <label for="password">Senha</label>
            <input type="password" name="password" id="password">
            <div class="error-box d-block">
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <div class="input-group">
            <label for="password_confirmation">Confirmar Senha</label>
            <input type="password" name="password_confirmation" id="password_confirmation">
        </div>

        <button class="btn-primary" type="submit">
            Cadastrar
        </button>

        <div class="d-flex justify-content-center align-items-center mt-3">
        <a href="{{ route('login' )}}" class="">Já possui uma conta?</a>  
        </div>                                                 

    </form>
</div>


@endsection