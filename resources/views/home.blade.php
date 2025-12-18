@extends('layouts.main_layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}"> <!-- public/css/home.css -->
@endsection
@section('content')
    @include('layouts.navbar')

    @auth
        <p> {{ Auth::user()->name }} </p>
    @endauth

    <section class="carousel-section">
        <h2 class="section-title">Ofertas</h2>

        <div class="carousel-container">
            <button class="carousel-btn left" id="carouselLeft">❮</button>

            <div class="carousel-track" id="carouselTrack">
                <div class="carousel-card">
                    <div class="card-image">
                        <img src="imgs/testimg1.webp" alt="Baki Vol. 29">
                    </div>
                    <div class="card-info">
                        <p class="item-title">celular</p>
                        <p class="item-author">samsung</p>

                        <p class="item-price">R$ 27,20</p>
                    </div>
                </div>

                <div class="carousel-card">
                    <div class="card-image">
                        <img src="imgs/testimg2.webp" alt="Baki Vol. 28">
                    </div>
                    <div class="card-info">
                        <p class="item-title">celular</p>
                        <p class="item-author">samsung</p>

                        <p class="item-price">R$ 27,20</p>
                    </div>
                </div>

                <div class="carousel-card">
                    <div class="card-image">
                        <img src="imgs/testimg2.webp" alt="Baki Vol. 28">
                    </div>
                    <div class="card-info">
                        <p class="item-title">celular</p>
                        <p class="item-author">samsung</p>

                        <p class="item-price">R$ 27,20</p>
                    </div>
                </div>

                <div class="carousel-card">
                    <div class="card-image">
                        <img src="imgs/testimg2.webp" alt="Baki Vol. 28">
                    </div>
                    <div class="card-info">
                        <p class="item-title">celular</p>
                        <p class="item-author">samsung</p>

                        <p class="item-price">R$ 27,20</p>
                    </div>
                </div>

                <div class="carousel-card">
                    <div class="card-image">
                        <img src="imgs/testimg2.webp" alt="Baki Vol. 28">
                    </div>
                    <div class="card-info">
                        <p class="item-title">celular</p>
                        <p class="item-author">samsung</p>

                        <p class="item-price">R$ 27,20</p>
                    </div>
                </div>

                <div class="carousel-card">
                    <div class="card-image">
                        <img src="imgs/testimg2.webp" alt="Baki Vol. 28">
                    </div>
                    <div class="card-info">
                        <p class="item-title">celular</p>
                        <p class="item-author">samsung</p>

                        <p class="item-price">R$ 27,20</p>
                    </div>
                </div>

                <div class="carousel-card">
                    <div class="card-image">
                        <img src="imgs/testimg2.webp" alt="Baki Vol. 28">
                    </div>
                    <div class="card-info">
                        <p class="item-title">celular</p>
                        <p class="item-author">samsung</p>

                        <p class="item-price">R$ 27,20</p>
                    </div>
                </div>

                <div class="carousel-card">
                    <div class="card-image">
                        <img src="imgs/testimg2.webp" alt="Baki Vol. 28">
                    </div>
                    <div class="card-info">
                        <p class="item-title">celular</p>
                        <p class="item-author">samsung</p>

                        <p class="item-price">R$ 27,20</p>
                    </div>
                </div>


                <!-- renan novos cards aqui -->
            </div>

            <button class="carousel-btn right" id="carouselRight">❯</button>
        </div>
    </section>
    <script>
        const track = document.getElementById('carouselTrack');
        const btnLeft = document.getElementById('carouselLeft');
        const btnRight = document.getElementById('carouselRight');

        function getScrollAmount() {
            const card = track.querySelector('.carousel-card');
            const gap = parseInt(getComputedStyle(track).gap);
            return card.offsetWidth + gap;
        }

        btnLeft.addEventListener('click', () => {
            track.scrollBy({
                left: -getScrollAmount(),
                behavior: 'smooth'
            });
        });

        btnRight.addEventListener('click', () => {
            track.scrollBy({
                left: getScrollAmount(),
                behavior: 'smooth'
            });
        });
    </script>

@endsection