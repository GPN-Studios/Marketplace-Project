(function () {
    document.querySelectorAll('.product-carousel').forEach(function (carousel) {
        var track = carousel.querySelector('.tag-products');
        var prevBtn = carousel.querySelector('.carousel-btn--prev');
        var nextBtn = carousel.querySelector('.carousel-btn--next');
        if (!track) return;

        function scrollByPage(direction) {
            track.scrollBy({ left: track.clientWidth * 0.9 * direction, behavior: 'smooth' });
        }

        if (prevBtn) prevBtn.addEventListener('click', function () { scrollByPage(-1); });
        if (nextBtn) nextBtn.addEventListener('click', function () { scrollByPage(1); });

        // Arrasto com o mouse (Pointer Events cobre mouse, caneta e touch)
        var isDragging = false;
        var dragMoved = false;
        var startX = 0;
        var startScrollLeft = 0;
        var pointerId = null;

        track.addEventListener('pointerdown', function (event) {
            if (event.pointerType === 'mouse' && event.button !== 0) return;

            isDragging = true;
            dragMoved = false;
            startX = event.clientX;
            startScrollLeft = track.scrollLeft;
            pointerId = event.pointerId;
        });

        track.addEventListener('pointermove', function (event) {
            if (!isDragging) return;

            var delta = event.clientX - startX;

            // Só captura o ponteiro (e assume o arrasto) depois de um movimento
            // real: chamar setPointerCapture no pointerdown redireciona o alvo
            // do evento "click" seguinte para a track, impedindo a navegação
            // nativa do link do produto em um clique normal (sem arrasto).
            if (!dragMoved && Math.abs(delta) > 5) {
                dragMoved = true;
                track.classList.add('is-dragging');
                track.setPointerCapture(pointerId);
            }

            if (dragMoved) {
                track.scrollLeft = startScrollLeft - delta;
            }
        });

        function endDrag() {
            isDragging = false;
            track.classList.remove('is-dragging');
        }

        track.addEventListener('pointerup', endDrag);
        track.addEventListener('pointercancel', endDrag);

        // Evita abrir o produto quando o clique foi na verdade um arrasto
        track.addEventListener('click', function (event) {
            if (dragMoved) {
                event.preventDefault();
                dragMoved = false;
            }
        });
    });
})();
