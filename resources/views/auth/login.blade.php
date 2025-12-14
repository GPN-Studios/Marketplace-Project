@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">    <!-- public/css/home.css -->
@endsection

@section('content')
<!-- HTML AQUI  -->



<div class="form-container">
    <div class="login-title">
        <h1>Login</h1>
    </div>

    <form action="{{route('login')}}" method="POST">
        @csrf

        <div class="input-container">
        <label for="email">Email</label>
        <input type="text" name="email" id="email" value="{{ old('email') }}">
        </div>

        <div class="input-container">
        <label for="password">Senha</label>
        <input type="password" name="password" id="password">
        </div>

        <div class="submit-btn">
        <button type="submit" >Enviar</button>
        </div>

        <div>
        <a href="{{'register'}}">Não possui uma conta?</a>
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
        
    </form>
</div>
@endsection