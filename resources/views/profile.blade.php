@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')

@auth

<!-- HEADER -->
<div class="container-fluid profile-header mb-4">
    <div class="container d-flex align-items-center gap-3 py-3">

        <!-- FOTO -->
        <form action="{{ route('user.pfp.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <label class="profile-avatar-wrapper">
                <img
                    src="{{ $user->profile_picture ? asset('storage/'.$user->profile_picture) : 'https://via.placeholder.com/160' }}"
                    class="profile-avatar"
                    alt="Foto de perfil"
                >

                <div class="profile-avatar-overlay">
                    <i class="bi bi-camera"></i>
                    Alterar foto
                </div>

                <input type="file" name="profile_picture" hidden onchange="this.form.submit()">
            </label>
        </form>

        <!-- NOME -->
        <div class="text-white profile-user-info">
            <h5 class="m-0">{{ $user->name }}</h5>

            <button class="profile-edit-btn" onclick="toggleEdit(true)">
                Editar perfil
            </button>
        </div>
    </div>
</div>

<!-- CONTEÚDO -->
<div class="container profile-content">

    <div class="row">
        <!-- DETALHES -->
        <aside class="col-md-4" id="profile-details">
            <div class="profile-card mb-3">
                <h6>Detalhes</h6>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        </aside>

        <!-- REPUTAÇÃO / PERFIL -->
        <main class="col-md-8">

            <div id="profile-view">

                <div class="profile-card mb-3">
                    <h5>Reputação do usuário</h5>

                    <div class="row text-center mt-3">
                        <div class="col">
                            <div class="profile-reputation positive">
                                581<br>Positivas
                            </div>
                        </div>
                        <div class="col">
                            <div class="profile-reputation negative">
                                20<br>Negativas
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- EDIÇÃO -->
            <div id="profile-edit" style="display: none;">
                <div class="profile-card text-center mx-auto" style="max-width: 500px;">
                    <h5>Editar perfil</h5>

                    <form action="{{ route('user.update', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nova senha</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar senha</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <button type="submit" class="btn btn-success">
                                Salvar
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="toggleEdit(false)">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- ÚLTIMAS AVALIAÇÕES -->
<div class="container mt-4">
    <div class="profile-card">
        <h5>Últimas avaliações</h5>

        <div class="profile-review positive">
            <p>"Atendimento demorado demais"</p>
            <div class="review-meta negative">Avaliação negativa</div>
            <small>Recebida como <strong>vendedor</strong></small>
        </div>

        <div class="profile-review negative">
            <p>"Comprei unranked mas veio platina"</p>
            <div class="review-meta positive">Avaliação positiva</div>
            <small>Recebida como <strong>vendedor</strong></small>
        </div>
    </div>
</div>

<!-- MEUS ANÚNCIOS -->
<div class="container mt-5 mb-5">
    <div class="orders-box">
        <h4>Meus Anúncios</h4>

        <div class="row g-3">
            @foreach ($user->products as $product)
                <div class="col-md-4">
                    <div class="order-card">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="">
                        <h5>{{ $product->name }}</h5>
                        <span>R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function toggleEdit(edit) {
    document.getElementById('profile-view').style.display = edit ? 'none' : 'block';
    document.getElementById('profile-edit').style.display = edit ? 'block' : 'none';
    document.getElementById('profile-details').style.display = edit ? 'none' : 'block';
}
</script>

@endauth
@endsection
