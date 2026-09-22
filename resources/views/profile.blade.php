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
        @if ($isOwnProfile)
            <form action="{{ route('user.pfp.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <label class="profile-avatar-wrapper">
                    @if ($user->profile_picture)
                        <img src="{{ asset('storage/'.$user->profile_picture) }}" class="profile-avatar" alt="Foto de perfil">
                    @else
                        <div class="profile-avatar profile-avatar-fallback">{{ Str::upper(Str::substr($user->name, 0, 1)) }}</div>
                    @endif

                    <div class="profile-avatar-overlay">
                        <i class="fa-solid fa-camera"></i>
                        Alterar foto
                    </div>

                    <input type="file" name="profile_picture" hidden onchange="this.form.submit()">
                </label>
            </form>
        @else
            @if ($user->profile_picture)
                <img src="{{ asset('storage/'.$user->profile_picture) }}" class="profile-avatar" alt="Foto de perfil">
            @else
                <div class="profile-avatar profile-avatar-fallback">{{ Str::upper(Str::substr($user->name, 0, 1)) }}</div>
            @endif
        @endif

        <!-- NOME -->
        <div class="text-white profile-user-info">
            <h5 class="m-0">{{ $user->name }}</h5>

            @if ($isOwnProfile)
                <button class="profile-edit-btn" onclick="toggleEdit(true)">
                    Editar perfil
                </button>
            @endif
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

            @if ($isOwnProfile)
                <div class="profile-card">
                    <h6>Saldo</h6>
                    <p class="mb-3">
                        <strong>{{ config('shop.currency_symbol') }} {{ $user->balance_formatted }}</strong> disponíveis para saque
                    </p>

                    @if ($user->balance > 0)
                        <form action="{{ route('withdraw') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-site w-100">Sacar saldo</button>
                        </form>
                    @endif
                </div>
            @endif
        </aside>

        <!-- REPUTAÇÃO / PERFIL -->
        <main class="col-md-8">

            <div id="profile-view">

                <div class="profile-card mb-3">
                    <h5>Reputação do usuário</h5>

                    <div class="row text-center mt-3">
                        <div class="col">
                            <div class="profile-reputation positive">
                                {{ $positiveRatings }}<br>Positivas
                            </div>
                        </div>
                        <div class="col">
                            <div class="profile-reputation negative">
                                {{ $negativeRatings }}<br>Negativas
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            @if ($isOwnProfile)
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
            @endif

        </main>
    </div>
</div>

<!-- ÚLTIMAS AVALIAÇÕES -->
<div class="container mt-4">
    <div class="profile-card">
        <h5>Últimas avaliações</h5>

        @forelse ($latestRatings as $rating)
            <div class="profile-review {{ $rating->is_positive ? 'positive' : 'negative' }}">
                <p>{{ $rating->description ?: 'Sem comentário.' }}</p>
                <div class="review-meta {{ $rating->is_positive ? 'positive' : 'negative' }}">
                    Avaliação {{ $rating->is_positive ? 'positiva' : 'negativa' }}
                </div>
                <small>
                    Recebida como <strong>vendedor</strong>, de {{ $rating->buyer?->name ?? 'usuário removido' }}
                </small>
            </div>
        @empty
            <p class="text-muted mb-0">Este usuário ainda não recebeu avaliações.</p>
        @endforelse
    </div>
</div>

<!-- MEUS ANÚNCIOS -->
@if ($isOwnProfile)
<div class="container mt-5 mb-5">
    <div class="orders-box">
        <h4>Meus Anúncios</h4>

        <div class="products-grid">
            @forelse ($user->products as $product)
                <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                    <div class="order-card">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="">

                        <h5>{{ $product->name }}</h5>

                        <span>
                            {{ config('shop.currency_symbol') }} {{ $product->price_formatted }}
                        </span>
                    </div>
                </a>
            @empty
                <p class="text-muted mb-0">Você ainda não anunciou nenhum produto.</p>
            @endforelse
        </div>
    </div>
</div>
@endif

<script>
function toggleEdit(edit) {
    document.getElementById('profile-view').style.display = edit ? 'none' : 'block';
    document.getElementById('profile-edit').style.display = edit ? 'block' : 'none';
    document.getElementById('profile-details').style.display = edit ? 'none' : 'block';
}
</script>

@endauth
@endsection
