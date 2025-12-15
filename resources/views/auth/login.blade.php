@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}"> <!-- public/css/home.css -->
@endsection

@section('content')

    <div class="caixa-login">
        <h2>Entrar</h2>
        <form>
            <div class="caixa-usuario">
                <label>Usuário</label>
                <input type="text" required>
            </div>

            <div class="caixa-usuario">
                <label>Senha</label>
                <input type="password" required>
            </div>

            <button class="butao">
                entrar
            </button>
            <div class="divisor">
                <span>ou</span>
            </div>
            <button class="butao2">
                cadastrar
            </button>
            <button class="butao2">
                Esqueceu a senha?
            </button>
        </form>

    </div>














    @if($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif


@endsection