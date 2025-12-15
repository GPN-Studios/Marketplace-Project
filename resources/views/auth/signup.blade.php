@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/signup.css') }}"> <!-- public/css/home.css -->
@endsection

@section('content')

<div class="caixa-login">
    <h2>Criar Conta</h2>
    <form>
        <div class="caixa-usuario">
            <label>Usuário</label>
            <input type="text" name="username" required>
        </div>
        <div class="caixa-usuario">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="caixa-usuario">
            <label>Senha</label>
            <input type="password" name="password" required>
        </div>
        <div class="caixa-usuario">
            <label>Confirmar Senha</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button class="butao">
            Cadastrar
        </button>
    </form>
</div>

@endsection