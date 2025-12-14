@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/signup.css') }}">    <!-- public/css/home.css -->
@endsection

@section('content')
<!-- HTML AQUI  -->

<h1>Marketplaceholdertitle</h1>

<div class="login-form">
    <form action="{{route('register')}}" method="POST">
        @csrf

        <div>
        <label for="email">Email</label>
        <input type="text" name="email" id="email" value="{{ old('email') }}">
        </div>

        <div>
        <label for="name">Nome</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}">
        </div>

        <div>
        <label for="password">Senha</label>
        <input type="password" name="password" id="password">
        </div>

        <div>
        <label for="password">Confirme a senha</label>
        <input type="password" name="password_confirmation" id="password">
        </div>  

        <div>
        <button type="submit">Enviar</button>
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