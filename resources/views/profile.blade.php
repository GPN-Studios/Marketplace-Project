@extends('layouts.main_layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')

@auth
<p> PERFIL DE {{ Auth::user()->name }} </p>
@endauth

<div class="container profile-page">

    <div class="row g-4">

        {{-- PERFIL --}}
        <div class="col-lg-4">
            <div class="profile-card">

                <div class="profile-avatar">
                    <img
                        src="https://via.placeholder.com/300"
                        alt="Foto de perfil">
                </div>

                <h3>Nome do Usuário</h3>
                <p>email@exemplo.com</p>

                {{-- PROTÓTIPO: sem ação --}}
                <label class="change-photo">
                    Alterar foto
                    <input type="file" name="profile_picture" hidden>
                </label>

            </div>
        </div>

        {{-- DADOS --}}
        <div class="col-lg-8">
            <div class="profile-box">
                <h4>Meus dados</h4>

                {{-- PROTÓTIPO: sem action --}}
                <form>

                    <div class="mb-3">
                        <label>Nome</label>
                        <input type="text" placeholder="Nome do usuário">
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" placeholder="email@exemplo.com">
                    </div>

                    <div class="mb-3">
                        <label>Nova senha</label>
                        <input type="password" placeholder="********">
                    </div>

                    <button type="button" class="btn-save">
                        Salvar alterações
                    </button>

                </form>
            </div>
        </div>

    </div>

    {{-- MEUS PEDIDOS / ANÚNCIOS --}}
    <div class="orders-box">
        <h4>Meus pedidos</h4>

        <div class="row g-3">

            {{-- CARD 1 --}}
            <div class="col-md-4">
                <div class="order-card">
                    <img src="https://via.placeholder.com/300x200" alt="">
                    <h5>Produto anunciado</h5>
                    <span>R$ 199,90</span>
                </div>
            </div>

            {{-- CARD 2 --}}
            <div class="col-md-4">
                <div class="order-card">
                    <img src="https://via.placeholder.com/300x200" alt="">
                    <h5>Outro produto</h5>
                    <span>R$ 89,90</span>
                </div>
            </div>

            {{-- CARD 3 --}}
            <div class="col-md-4">
                <div class="order-card">
                    <img src="https://via.placeholder.com/300x200" alt="">
                    <h5>Mais um anúncio</h5>
                    <span>R$ 349,00</span>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection
