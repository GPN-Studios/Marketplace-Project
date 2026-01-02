<header>  
    <nav>
        <a id="navbar-logo" href="{{route('home')}}">Laravel Projeto</a>
        <div id="search">
            <form action="" class="d-flex align-items-center w-100">
            <input type="text" name="" id="" placeholder="Pesquisar...">
            <button class="search-btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <ul id="nav-list">
        @auth
            <!-- usuário logado -->
            <li><a href="{{ route('profile') }}">{{Auth::user()->name}}</a></li>
            <li><a href="{{ route('products.create') }}">Anunciar</a></li>
            <li><a href="{{ route('cart.index') }}">Carrinho</a></li>
            <li>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn p-0 text-white logout-btn">
                    Logout
                </button>
            </form>
            </li>
            @else
            <!-- usuário que não está logado -->
            <li><a href="">Sobre</a></li>
            <li><a href="">Projeto</a></li>
            <li><a href="{{'/login'}}">Login</a></li>
            @endauth
            </ul>

    </nav>
    
</header>
