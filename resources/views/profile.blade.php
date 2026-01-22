@extends('layouts.main_layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')

<div class="container profile-page">
    <div class="row g-4">
        {{-- PERFIL --}}
        <div class="col-lg-4">
            <div class="profile-card">

                <div class="profile-avatar">
                    <img
                        @if ($user->profile_picture)
                            src="{{ asset('storage/' . $user->profile_picture) }}"
                        @else
                            src="https://via.placeholder.com/300"
                            alt="Foto de perfil">
                        @endif
                        >
                </div>

                <h3>{{$user->name}}</h3>

                {{-- PROTÓTIPO: sem ação --}}
                <form action="{{ route('user.pfp.update', $user) }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <label class="change-photo">
                        Alterar foto
                        <input type="file" name="profile_picture" hidden onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>

        {{-- DADOS --}}
        <div class="col-lg-8">
            <div class="profile-box">
                <h4>Alterar dados</h4>

                <form action="{{ route('user.update', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label>Usuário</label>
                        <input type="text" name="name" placeholder="{{ $user->name }}">
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" placeholder="{{ $user->email }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label>Nova senha</label>
                        <input type="password" name="password" placeholder="********">
                    </div>

                    <div class="mb-3">
                        <label>Nova senha</label>
                        <input type="password" name="password_confirmation" placeholder="Digite a nova senha novamente.">
                    </div>

                    <button type="submit"    class="btn-save">
                        Salvar alterações
                    </button>

                </form>
            </div>
        </div>

    </div>

    {{-- MEUS PEDIDOS / ANÚNCIOS --}}
    <div class="orders-box">
        <h4>Meus Anúncios</h4>

        <div class="row g-3">
            {{-- CARD --}}
            @foreach ($user->products as $product)
                <div class="col-md-4">
                    <div class="order-card">
                        <img src="{{ asset('storage/' . $product->image ) }}" alt="">
                        <h5>{{ $product->name }}</h5>
                        <span>R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
