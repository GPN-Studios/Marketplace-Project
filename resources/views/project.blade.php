@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/project.css') }}">
@endsection

@section('content')

<section class="project-hero">
    <div class="project-hero-container">
        <span class="project-badge"><i class="fa-solid fa-graduation-cap"></i> Projeto de estudo</span>
        <h1>Um marketplace completo, construído do zero</h1>
        <p>
            A Ideal é um projeto full-stack que simula o fluxo real de um e-commerce: catálogo por categorias,
            carrinho, checkout com pagamento via Stripe, avaliações e saldo de vendedor — tudo construído como
            exercício prático de engenharia de software.
        </p>
    </div>
</section>

<div class="project-content">

    <div class="project-disclaimer">
        <i class="fa-solid fa-circle-info"></i>
        <p>
            Este é um projeto de estudo / portfólio pessoal, não uma loja real. Os pagamentos rodam em
            <strong>modo de teste</strong> do Stripe e nenhuma cobrança real é processada.
        </p>
    </div>

    <section class="project-section">
        <h2 class="section-title">Stack utilizada</h2>
        <p class="section-subtitle">Tecnologias e pacotes que sustentam a aplicação.</p>

        <div class="stack-grid">
            <div class="stack-card">
                <span class="stack-icon"><i class="fa-solid fa-layer-group"></i></span>
                <h3>Laravel 12</h3>
                <p>Framework PHP que estrutura toda a aplicação (rotas, models, controllers, policies).</p>
            </div>

            <div class="stack-card">
                <span class="stack-icon"><i class="fa-brands fa-php"></i></span>
                <h3>PHP 8.2+</h3>
                <p>Linguagem base do backend, com enums, atributos tipados e recursos modernos.</p>
            </div>

            <div class="stack-card">
                <span class="stack-icon"><i class="fa-solid fa-database"></i></span>
                <h3>MySQL</h3>
                <p>Banco de dados relacional, acessado via Eloquent ORM.</p>
            </div>

            <div class="stack-card">
                <span class="stack-icon"><i class="fa-solid fa-credit-card"></i></span>
                <h3>Stripe</h3>
                <p>Processamento de pagamentos via Checkout Session e confirmação assíncrona por webhook assinado.</p>
            </div>

            <div class="stack-card">
                <span class="stack-icon"><i class="fa-solid fa-user-shield"></i></span>
                <h3>Laravel Fortify</h3>
                <p>Autenticação, registro e fluxo de segurança de contas.</p>
            </div>

            <div class="stack-card">
                <span class="stack-icon"><i class="fa-solid fa-tags"></i></span>
                <h3>Spatie Laravel-Tags</h3>
                <p>Sistema de categorias/tags traduzíveis usado no catálogo de produtos.</p>
            </div>

            <div class="stack-card">
                <span class="stack-icon"><i class="fa-brands fa-bootstrap"></i></span>
                <h3>Bootstrap 5</h3>
                <p>Base de layout responsivo, estilizada com CSS próprio por página.</p>
            </div>

            <div class="stack-card">
                <span class="stack-icon"><i class="fa-solid fa-vial"></i></span>
                <h3>Pest</h3>
                <p>Suíte de testes automatizados cobrindo carrinho, checkout, avaliações e rate limiting.</p>
            </div>
        </div>
    </section>

    <section class="project-section project-section--muted">
        <h2 class="section-title">Funcionalidades implementadas</h2>
        <p class="section-subtitle">O que já funciona de ponta a ponta.</p>

        <div class="features-grid">
            <div class="feature-item">
                <i class="fa-solid fa-tags"></i>
                <span>Catálogo organizado por categorias</span>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Carrinho e checkout completos</span>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-credit-card"></i>
                <span>Pagamento real via Stripe (modo teste)</span>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-star"></i>
                <span>Avaliações de pedidos concluídos</span>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-wallet"></i>
                <span>Saldo e saque para o vendedor</span>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Autenticação segura com Fortify</span>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Rate limiting contra abuso e scraping</span>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-image"></i>
                <span>Upload de imagens de produto e perfil</span>
            </div>
        </div>
    </section>

    <section class="project-section">
        <h2 class="section-title">Sobre o desenvolvimento</h2>
        <p class="section-subtitle">Como o projeto foi construído.</p>

        <div class="dev-notes">
            <p>
                A aplicação segue a arquitetura MVC padrão do Laravel, com autorização centralizada em Policies,
                validação em Form Requests e limites de requisição (rate limiting) configurados por rota para
                proteger ações sensíveis como checkout, saque de saldo e criação de anúncios.
            </p>
            <p>
                O frontend usa Blade + Bootstrap 5 com CSS próprio por página, sem depender de um framework
                JavaScript pesado. A cobertura de testes automatizados (Pest) valida os fluxos críticos: posse do
                carrinho, ciclo de vida do pedido, avaliações e limites de requisição.
            </p>
        </div>
    </section>

    <section class="project-cta">
        <h2>Quer ver o código?</h2>
        <p>O projeto foi desenvolvido pela equipe abaixo — os perfis públicos estão no rodapé do site.</p>
        <a href="{{ route('home') }}" class="btn-site">Explorar o marketplace</a>
    </section>

</div>

@endsection
