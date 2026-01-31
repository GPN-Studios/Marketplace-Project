<nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
    <div class="container-fluid">

        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img 
                src="{{ asset('imgs/logo-verde-azulado.png') }}"
                alt="Ideal Marketplace"
                class="navbar-logo"
            >
        </a>

        <form class="d-flex custom-search" role="search">
            <input class="form-control custom-search-input" type="search" placeholder="Pesquisar...">
            <button class="btn search-btn" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center gap-2">

                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="{{route('cart.index')}}">
                        <img src="{{ asset('imgs/cart-icon.png') }}" class="cart-icon me-1" alt="Carrinho">
                    </a>
                </li>

                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3" data-bs-toggle="dropdown" href="#">
                        {{ Auth::user()->name }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile', Auth::id() ) }}">Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.create') }}">Anunciar produto</a></li>
                        <li><a class="dropdown-item" href="{{ route('user.products') }}">Meus Anúncios</a></li>
                        <li><a class="dropdown-item" href="{{ route('user.orders') }}">Meus Pedidos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger">Sair da conta</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item"><a class="nav-link px-3" href="#">Sobre</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#">Projeto</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="/login">Login</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
