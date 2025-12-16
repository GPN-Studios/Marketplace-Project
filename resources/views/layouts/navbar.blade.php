<header>  
    <nav>
        <a id="navbar-logo" href=>Laravel Projeto</a>
        <div id="search">
            <input type="text" name="" id="" placeholder="Pesquisar...">
            <button class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>

        <ul id="nav-list">
        @auth
            <!-- usuário logado -->
            <li><a href="{{route('profile')}}">{{Auth::user()->name}}</a></li>
            <li><a href="">Carrinho</a></li>
            <li>
                <form action="{{ route('logout') }}" method="POST" id="logoutform">
                @csrf
                <button type="submit" >Logout</button>
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
    <!-- js da navbar -->
    @push('scripts')
        <script src="{{ asset('js/navbar.js') }}" defer></script>
    @endpush
</header>
