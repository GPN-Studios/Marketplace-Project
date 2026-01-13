@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')

@auth

{{-- HEADER PERFIL --}}
<div class="profile-header-section">
    <div class="container profile-header-inner">

        <div class="profile-avatar-big">
            <img src="https://via.placeholder.com/300" alt="Foto de perfil">
        </div>

        <div class="profile-header-info">
            <h2>{{ Auth::user()->name }}</h2>

            {{-- TEXTO DA BIO --}}
            <p class="profile-bio" id="bioText">
                {{ Auth::user()->bio ?? 'Adicione uma descrição ao seu perfil.' }}
            </p>

            {{-- FORM BIO --}}
            <form
                action="{{ route('profile.bio.update') }}"
                method="POST"
                id="bioForm"
                style="display:none;"
            >
                @csrf

                <textarea
                    name="bio"
                    id="bioInput"
                    class="profile-bio-input"
                >{{ Auth::user()->bio ?? '' }}</textarea>

                <div class="bio-actions">
                    <button type="submit" class="bio-save">Salvar</button>
                    <button type="button" class="bio-cancel" onclick="cancelBioEdit()">Cancelar</button>
                </div>
            </form>

            <a class="edit-link" id="editBioBtn" onclick="enableBioEdit()">
                Editar biografia
            </a>
        </div>

    </div>
</div>

{{-- CONTEÚDO --}}
<div class="container profile-page">
    <div class="row g-4">

        {{-- INFO CONTA --}}
        <div class="col-lg-4">
            <div class="profile-card">

                <h4>Informações da conta</h4>

                <div class="profile-data-row">
                    <small>Nome</small>
                    <span>{{ Auth::user()->name }}</span>
                </div>

                <div class="profile-data-row">
                    <small>Email</small>
                    <span>{{ Auth::user()->email }}</span>
                </div>

                <div class="profile-data-row">
                    <small>Tipo de conta</small>
                    <span>Comprador / Vendedor</span>
                </div>

                <a href="{{ route('profile.edit') }}" class="edit-link edit-profile-link">
                    Editar perfil
                </a>

            </div>
        </div>

        {{-- PEDIDOS --}}
        <div class="col-lg-8">
            <div class="orders-box">

                <div class="orders-header">
                    <h4>Meus pedidos</h4>
                    <a href="#">Ver todos</a>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="order-card">
                            <img src="https://via.placeholder.com/300x200">
                            <h5>Produto anunciado</h5>
                            <span>R$ 199,90</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- JS --}}
<script>
let originalBio = document.getElementById('bioText').innerText;

function enableBioEdit() {
    document.getElementById('bioText').style.display = 'none';
    document.getElementById('editBioBtn').style.display = 'none';
    document.getElementById('bioForm').style.display = 'block';
}

function cancelBioEdit() {
    document.getElementById('bioInput').value = originalBio;
    document.getElementById('bioForm').style.display = 'none';
    document.getElementById('bioText').style.display = 'block';
    document.getElementById('editBioBtn').style.display = 'inline-block';
}
</script>

@endauth

@guest
<div class="container mt-5">
    <h3>Você precisa estar logado para acessar o perfil.</h3>
</div>
@endguest

@endsection