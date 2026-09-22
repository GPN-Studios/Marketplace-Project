@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/about.css') }}">
@endsection

@section('content')

<section class="about-hero">
    <div class="about-hero-container">
        <span class="about-eyebrow">Sobre a Ideal</span>
        <h1>Um marketplace feito para conectar<br class="d-none d-md-block"> quem vende com quem procura</h1>
        <p>Anuncie em minutos, compre com segurança e acompanhe tudo em um só lugar — do carrinho ao pagamento.</p>

        <div class="about-hero-actions">
            <a href="{{ route('home') }}" class="btn-site">Explorar produtos</a>
            @guest
                <a href="{{ route('signup') }}" class="btn-site-outline-white">Criar conta grátis</a>
            @else
                <a href="{{ route('products.create') }}" class="btn-site-outline-white">Anunciar um produto</a>
            @endguest
        </div>
    </div>
</section>

<section class="about-stats">
    <div class="about-stats-container">
        <div class="stat-card">
            <span class="stat-number">{{ $stats['products'] }}</span>
            <span class="stat-label">Produtos anunciados</span>
        </div>
        <div class="stat-card">
            <span class="stat-number">{{ $stats['sellers'] }}</span>
            <span class="stat-label">Vendedores ativos</span>
        </div>
        <div class="stat-card">
            <span class="stat-number">{{ $stats['categories'] }}</span>
            <span class="stat-label">Categorias</span>
        </div>
        <div class="stat-card">
            <span class="stat-number">{{ $stats['completedOrders'] }}</span>
            <span class="stat-label">Pedidos concluídos</span>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="about-content">
        <h2 class="section-title">Por que usar a Ideal</h2>
        <p class="section-subtitle">Tudo o que um marketplace moderno precisa ter, sem complicação.</p>

        <div class="values-grid">
            <div class="value-card">
                <span class="value-icon"><i class="fa-solid fa-shield-halved"></i></span>
                <h3>Compra protegida</h3>
                <p>Pagamentos processados via Stripe. O saldo só é liberado ao vendedor depois da confirmação real do pagamento.</p>
            </div>

            <div class="value-card">
                <span class="value-icon"><i class="fa-solid fa-bolt"></i></span>
                <h3>Cadastro rápido</h3>
                <p>Crie sua conta e comece a comprar ou anunciar produtos em poucos minutos.</p>
            </div>

            <div class="value-card">
                <span class="value-icon"><i class="fa-solid fa-star"></i></span>
                <h3>Avaliações reais</h3>
                <p>Compradores avaliam cada pedido concluído, construindo a reputação de cada vendedor ao longo do tempo.</p>
            </div>

            <div class="value-card">
                <span class="value-icon"><i class="fa-solid fa-tags"></i></span>
                <h3>Categorias organizadas</h3>
                <p>Navegue por categorias curadas para encontrar exatamente o que está procurando.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-section about-section--muted">
    <div class="about-content">
        <h2 class="section-title">Como funciona</h2>
        <p class="section-subtitle">Do anúncio à entrega, em três passos simples.</p>

        <div class="steps-grid">
            <div class="step-card">
                <span class="step-number">1</span>
                <h3>Anuncie ou explore</h3>
                <p>Publique seu produto com fotos e preço, ou navegue pelas categorias em busca do que precisa.</p>
            </div>

            <div class="step-card">
                <span class="step-number">2</span>
                <h3>Compre com segurança</h3>
                <p>Adicione ao carrinho e finalize o pagamento pela Stripe, com confirmação automática do pedido.</p>
            </div>

            <div class="step-card">
                <span class="step-number">3</span>
                <h3>Receba e avalie</h3>
                <p>Confirme o recebimento e avalie a experiência — sua avaliação ajuda toda a comunidade.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="about-content">
        <h2 class="section-title">Quem constrói a Ideal</h2>
        <p class="section-subtitle">Um projeto criado e mantido por esta equipe.</p>

        <div class="team-grid">
            <div class="team-card">
                <span class="team-avatar">RC</span>
                <h3>Renato de Azevedo Caldas</h3>
                <div class="team-links">
                    <a href="https://www.linkedin.com/in/rcaldas/" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="https://github.com/rencaldas" target="_blank" rel="noopener" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                </div>
            </div>

            <div class="team-card">
                <span class="team-avatar">RB</span>
                <h3>Renan Balter Pontes</h3>
                <div class="team-links">
                    <a href="https://www.linkedin.com/in/renan-balter-7b439b373/" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="https://github.com/renanbalter" target="_blank" rel="noopener" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                </div>
            </div>

            <div class="team-card">
                <span class="team-avatar">MG</span>
                <h3>Marco Gabriel</h3>
                <div class="team-links">
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-cta">
    <div class="about-cta-container">
        <h2>Pronto para começar?</h2>
        <p>Crie sua conta gratuita e explore tudo o que a Ideal tem pra oferecer.</p>

        <div class="about-hero-actions">
            @guest
                <a href="{{ route('signup') }}" class="btn-site">Criar conta grátis</a>
                <a href="{{ route('home') }}" class="btn-site-outline-white">Explorar produtos</a>
            @else
                <a href="{{ route('products.create') }}" class="btn-site">Anunciar um produto</a>
                <a href="{{ route('home') }}" class="btn-site-outline-white">Explorar produtos</a>
            @endguest
        </div>
    </div>
</section>

@endsection
