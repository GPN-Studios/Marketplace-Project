@extends('layouts.main_layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')

@auth
<div class="gg-profile-page">

    <aside class="gg-sidebar">

        <div class="gg-user-card">
            <img src="https://via.placeholder.com/120" class="gg-avatar" alt="Avatar">
            <h3>{{ Auth::user()->name }}</h3>

            <span class="gg-status">
                <span class="dot online"></span> Online
            </span>
        </div>

        <div class="gg-box">
            <h4>Detalhes</h4>
            <ul>
                <li><strong>Desde:</strong> 22/02/2024</li>
                <li><strong>Avaliações positivas:</strong> 96%</li>
                <li><strong>Nº de avaliações:</strong> 608</li>
                <li><strong>Último acesso:</strong> há 31 minutos</li>
            </ul>
        </div>

        <a href="#" class="gg-report">Denunciar</a>

    </aside>

    <main class="gg-main">

        <div class="gg-reputation-header">
            <h2>Reputação do usuário</h2>

            <select>
                <option>Recebida como: Ambos tipos</option>
            </select>
        </div>

        <div class="gg-reputation-cards">
            <div class="rep positive">
                <strong>581</strong>
                <span>Positivas</span>
            </div>

            <div class="rep negative">
                <strong>20</strong>
                <span>Negativas</span>
            </div>
        </div>

        <section class="gg-reviews">
            <h3>Últimas avaliações recebidas</h3>

            <div class="gg-review-card">
                <p class="review-text">"Atendimento demorado demais"</p>
                <a href="#">CONTAS VALORANT - UNRANKED</a>
                <div class="review-meta">
                    18/10/25 às 10:27 • Recebida como <strong>vendedor</strong><br>
                    Por <a href="#">allanbizzi</a>
                </div>
            </div>

            <div class="gg-review-card">
                <p class="review-text">"comprei unranked mas veio platina"</p>
                <a href="#">CONTAS VALORANT - UNRANKED</a>
                <div class="review-meta">
                    23/07/25 às 15:20 • Recebida como <strong>vendedor</strong><br>
                    Por <a href="#">nostaldias</a>
                </div>
            </div>

        </section>

    </main>

</div>
@endauth

@endsection
