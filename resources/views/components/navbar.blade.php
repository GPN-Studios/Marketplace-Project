<nav class="navbar navbar-expand-xl navbar-light custom-navbar">
    <div class="container-fluid">

        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img
                src="{{ asset('imgs/logo-verde-azulado.png') }}"
                alt="Ideal Marketplace"
                class="navbar-logo"
            >
        </a>

        <form class="d-none d-xl-flex custom-search" role="search" action="{{ route('search') }}" method="GET">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input
                class="custom-search-input"
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Buscar produtos, marcas e mais..."
                aria-label="Buscar"
            >
            <button class="search-btn" type="submit">Buscar</button>
        </form>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarContent" aria-controls="navbarContent" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">

            <ul class="navbar-nav me-auto nav-left-links">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Sobre</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('project') ? 'active' : '' }}" href="{{ route('project') }}">Projeto</a></li>
            </ul>

            <form class="d-flex d-xl-none custom-search custom-search-mobile" role="search" action="{{ route('search') }}" method="GET">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input
                    class="custom-search-input"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Buscar produtos..."
                    aria-label="Buscar"
                >
                <button class="search-btn" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <ul class="navbar-nav ms-auto align-items-xl-center">

                <li class="nav-item">
                    <a class="nav-link cart-link" href="{{ route('cart.index') }}">
                        <span class="cart-icon-wrap">
                            <img src="{{ asset('imgs/cart-icon.png') }}" class="cart-icon" alt="">
                        </span>
                        <span>Carrinho</span>
                    </a>
                </li>

                @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('user.orders') }}">
                        <i class="fa-solid fa-box"></i>
                        <span>Meus Pedidos</span>
                    </a>
                </li>

                <li class="nav-item dropdown user-dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                        <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        <span class="user-name">{{ Auth::user()->name }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile', Auth::id() ) }}"><i class="fa-regular fa-user"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.create') }}"><i class="fa-solid fa-tag"></i>Anunciar produto</a></li>
                        <li><a class="dropdown-item" href="{{ route('user.products') }}"><i class="fa-solid fa-store"></i>Meus Anúncios</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket"></i>Sair da conta</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item">
                    <a class="btn-login" href="{{ route('login') }}">Entrar</a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
