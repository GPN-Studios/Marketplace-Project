@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">    <!-- public/css/home.css -->
@endsection
@section('content')


@auth
<p> PERFIL DE  {{Auth::user()->name;}}  </p>
@endauth

<!-- HTML AQUI  -->

@endsection
