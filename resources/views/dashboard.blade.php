@extends('layouts.main_layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')

{{-- ================= TOAST DE SUCESSO ================= --}}
@if (session('success'))
<div class="toast-container position-fixed start-50 translate-middle-x"
     style="
        top: 0;
        margin-top: 80px; /* ajuste conforme a altura da navbar */
        z-index: 3000;
        pointer-events: none;
     ">

    <div id="successToast"
         class="toast show align-items-center text-bg-success border-0 shadow"
         role="alert"
         aria-live="assertive"
         aria-atomic="true"
         style="pointer-events: auto;">

        <div class="d-flex">
            <div class="toast-body text-center">
                ✅ {{ session('success') }}
            </div>
            <button type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"
                    aria-label="Close">
            </button>
        </div>

    </div>
</div>
@endif
{{-- ==================================================== --}}

@auth
<p>{{ Auth::user()->name }}</p>
@endauth

<div class="content d-flex flex-column gap-5">

    @for ($i = 1; $i <= 3; $i++)
    {{-- ===== carousel {{ $i }} ===== --}}
    <div class="dashboard-carousel-wrapper position-relative">

        {{-- page indicators --}}
        <div class="carousel-indicators"></div>

        <button class="carousel-btn left" onclick="scrollCarousel(this, -1)">
            <i class="fas fa-chevron-left"></i>
        </button>

        <div class="dashboard-carousel">
            @foreach ($products as $product)
                <a href="{{ route('products.show', encrypt($product->id)) }}"
                   class="product-card text-decoration-none text-reset">

                    <div class="card">
                        <div class="product-image-wrapper">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="">
                        </div>

                        <div class="card-body text-center">
                            <p class="card-title">{{ $product->name }}</p>
                            <p class="card-text fw-bold">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>

                </a>
            @endforeach
        </div>

        <button class="carousel-btn right" onclick="scrollCarousel(this, 1)">
            <i class="fas fa-chevron-right"></i>
        </button>

    </div>
    @endfor

</div>

{{-- ================= js ================= --}}
<script>

/* ===== scroll carrossel ===== */
function scrollCarousel(button, direction) {
    const wrapper = button.closest('.dashboard-carousel-wrapper');
    const carousel = wrapper.querySelector('.dashboard-carousel');

    const cardWidth = 220;
    const gap = 20;

    const visibleCards = Math.round(carousel.offsetWidth / (cardWidth + gap));
    const distance = (cardWidth + gap) * visibleCards;

    animateScrollWithSoftEnd(carousel, direction * distance, 250);

    setTimeout(() => {
        updateCarouselButtons(wrapper);
        updateCarouselIndicators(wrapper);
    }, 260);
}

/* ===== animation ===== */
function animateScrollWithSoftEnd(element, distance, duration) {
    const start = element.scrollLeft;
    const startTime = performance.now();

    function frame(currentTime) {
        const elapsed = currentTime - startTime;
        const rawProgress = Math.min(elapsed / duration, 1);

        let progress;

        if (rawProgress < 0.85) {
            progress = rawProgress;
        } else {
            const t = (rawProgress - 0.85) / 0.15;
            progress = 0.85 + t * (2 - t) * 0.15;
        }

        element.scrollLeft = start + distance * progress;

        if (rawProgress < 1) {
            requestAnimationFrame(frame);
        }
    }

    requestAnimationFrame(frame);
}

/* ===== arrows ===== */
function updateCarouselButtons(wrapper) {
    const carousel = wrapper.querySelector('.dashboard-carousel');
    const btnLeft = wrapper.querySelector('.carousel-btn.left');
    const btnRight = wrapper.querySelector('.carousel-btn.right');

    btnLeft?.classList.toggle('hidden', carousel.scrollLeft <= 0);

    const reachedEnd =
        Math.ceil(carousel.scrollLeft + carousel.offsetWidth) >= carousel.scrollWidth;

    btnRight?.classList.toggle('hidden', reachedEnd);
}

/* ===== indicators ===== */
function setupCarouselIndicators(wrapper) {
    const carousel = wrapper.querySelector('.dashboard-carousel');
    const indicators = wrapper.querySelector('.carousel-indicators');

    indicators.innerHTML = '';

    const pageWidth = carousel.offsetWidth;
    const totalPages = Math.ceil(carousel.scrollWidth / pageWidth);

    for (let i = 0; i < totalPages; i++) {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (i === 0) dot.classList.add('active');
        indicators.appendChild(dot);
    }
}

function updateCarouselIndicators(wrapper) {
    const carousel = wrapper.querySelector('.dashboard-carousel');
    const dots = wrapper.querySelectorAll('.carousel-indicators .dot');

    const pageWidth = carousel.offsetWidth;
    const currentPage = Math.round(carousel.scrollLeft / pageWidth);

    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentPage);
    });
}

/* ===== init ===== */
document.querySelectorAll('.dashboard-carousel-wrapper').forEach(wrapper => {
    const carousel = wrapper.querySelector('.dashboard-carousel');

    setupCarouselIndicators(wrapper);
    updateCarouselButtons(wrapper);
    updateCarouselIndicators(wrapper);

    carousel.addEventListener('scroll', () => {
        updateCarouselButtons(wrapper);
        updateCarouselIndicators(wrapper);
    });

    window.addEventListener('resize', () => {
        setupCarouselIndicators(wrapper);
        updateCarouselButtons(wrapper);
        updateCarouselIndicators(wrapper);
    });
});
</script>

@endsection
