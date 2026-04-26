$(function () {




    /* Dortlu Manset Slider - Swiper.js (yatay, autoplay, pagination, hover durur) */
    if (typeof Swiper !== 'undefined' && document.querySelector('.manset-swiper')) {
        new Swiper('.manset-swiper', {
            slidesPerView: 1,
            spaceBetween: 12,
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
        });
    }

    if (typeof Swiper !== 'undefined' && document.querySelector('.ana-manset-swiper')) {

        const numBtns = document.querySelectorAll('.ana-num-btn');

        function updateNumBtns(activeIndex) {
            numBtns.forEach((btn, i) => {
                const isActive = i === activeIndex;
                btn.classList.toggle('bg-[var(--color-primary)]', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('border-[var(--color-primary)]', isActive);
            });
        }

        numBtns.forEach((btn, i) => {
            btn.addEventListener('click', function () {
                anaSwiper.slideToLoop(i);
            });
            btn.addEventListener('mouseenter', function () {
                anaSwiper.slideToLoop(i);
            });
        });

        updateNumBtns(0);
        /* Image Carousel - Numbered Dots */
        // ── Desktop Swiper ──────────────────────────────────────────
        const anaSwiper = new Swiper('.ana-manset-swiper', {
            slidesPerView: 1,
            loop: true,
            speed: 500,
            navigation: {
                prevEl: '.ana-manset-prev',
                nextEl: '.ana-manset-next',
            },
            on: {
                slideChange: function () {
                    updateNumBtns(this.realIndex);
                }
            }
        });


    }



    if (typeof Swiper !== 'undefined' && document.querySelector('.ana-manset-mobile-swiper')) {


        const mobileDots = document.querySelectorAll('.ana-mob-dot');

        function updateMobileDots(activeIndex) {
            mobileDots.forEach((dot, i) => {
                const isActive = i === activeIndex;
                dot.classList.toggle('bg-[var(--color-primary)]', isActive);
                dot.classList.toggle('border-[var(--color-primary)]', isActive);
            });
        }

        mobileDots.forEach((dot, i) => {
            dot.addEventListener('click', function () {
                anaMobileSwiper.slideToLoop(i);
            });
        });

        updateMobileDots(0);
        // ── Mobil Swiper ────────────────────────────────────────────
        const anaMobileSwiper = new Swiper('.ana-manset-mobile-swiper', {
            slidesPerView: 1,
            loop: true,
            speed: 500,
            navigation: {
                prevEl: '.ana-manset-mobile-prev',
                nextEl: '.ana-manset-mobile-next',
            },
            on: {
                slideChange: function () {
                    updateMobileDots(this.realIndex);
                }
            }
        });

    }


    if (typeof Swiper !== 'undefined' && document.querySelector('.authors-swiper')) {
        new Swiper('.authors-swiper', {
            slidesPerView: 1,
            spaceBetween: 16,
            loop: true,
            navigation: {
                prevEl: '.authors-prev',
                nextEl: '.authors-next',
            },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            }
        });
    }
    $(document).ready(function () {
        // Initialize Twitter Trends


        /* Horizontal Cards Carousel - Mobile */
        const initHorizontalCardsCarousel = () => {
            const $carousel = $('.horizontal-cards-carousel');
            const $track = $('.horizontal-cards-track');
            const $prevBtn = $('.horizontal-cards-prev');
            const $nextBtn = $('.horizontal-cards-next');
            const $dots = $('.horizontal-cards-dots');

            if (!$carousel.length || $(window).width() >= 1024) {
                // Desktop'ta carousel'i temizle
                $track.empty();
                return;
            }

            // Eğer zaten yüklenmişse tekrar yükleme
            if ($track.children().length > 0) return;

            // Desktop'taki card'ları al (hidden.lg:grid içindeki)
            const $desktopGrid = $('.hidden.lg\\:grid');
            const $desktopCards = $desktopGrid.find('a');

            if ($desktopCards.length === 0) return;

            // Card'ları 3'er 3'er grupla
            const cardGroups = [];
            for (let i = 0; i < $desktopCards.length; i += 3) {
                const group = [];
                for (let j = 0; j < 3 && (i + j) < $desktopCards.length; j++) {
                    group.push($desktopCards.eq(i + j));
                }
                if (group.length > 0) {
                    cardGroups.push(group);
                }
            }

            // Her grup için bir slide oluştur
            cardGroups.forEach((group, groupIndex) => {
                let groupHtml = '<div class="horizontal-cards-group min-w-full"><div class="space-y-4">';

                group.forEach(($card) => {
                    const href = $card.attr('href') || '#';
                    const $img = $card.find('img');
                    const $span = $card.find('span');
                    const $h4 = $card.find('h4');

                    groupHtml += `
                    <a href="${href}"
                        class="flex flex-row rounded-xl border border-[var(--color-border)] bg-[var(--color-card)] overflow-hidden ui-transition ui-lift group">
                        <div class="w-1/3 h-[86px] overflow-hidden">
                            <img src="${$img.attr('src')}" alt="${$img.attr('alt')}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300 ease-in-out">
                        </div>
                        <div class="flex-1 flex flex-col items-start justify-center p-4 space-y-2">
                            ${$span.prop('outerHTML')}
                            ${$h4.prop('outerHTML')}
                        </div>
                    </a>
                `;
                });

                groupHtml += '</div></div>';
                $track.append(groupHtml);
            });

            // Dots oluştur
            $dots.empty();
            for (let i = 0; i < cardGroups.length; i++) {
                const dot = $('<button>')
                    .addClass('horizontal-cards-dot h-2 w-2 rounded-full border border-[var(--color-border)] transition-all')
                    .attr('data-index', i);

                if (i === 0) {
                    dot.addClass('bg-[var(--color-primary)] border-[var(--color-primary)]');
                } else {
                    dot.addClass('bg-transparent');
                }

                $dots.append(dot);
            }

            // Carousel kontrolü
            let currentIndex = 0;
            const totalGroups = cardGroups.length;

            const updateCarousel = (index) => {
                currentIndex = index;
                $track.css('transform', `translateX(-${index * 100}%)`);

                // Dots güncelle
                $('.horizontal-cards-dot').removeClass('bg-[var(--color-primary)] border-[var(--color-primary)]').addClass('bg-transparent');
                $(`.horizontal-cards-dot[data-index="${index}"]`).removeClass('bg-transparent').addClass('bg-[var(--color-primary)] border-[var(--color-primary)]');

                // Buton durumları
                $prevBtn.prop('disabled', index === 0);
                $nextBtn.prop('disabled', index === totalGroups - 1);
            };

            // Navigation event handlers (önceki event listener'ları kaldır)
            $prevBtn.off('click').on('click', function () {
                if (currentIndex > 0) {
                    updateCarousel(currentIndex - 1);
                }
            });

            $nextBtn.off('click').on('click', function () {
                if (currentIndex < totalGroups - 1) {
                    updateCarousel(currentIndex + 1);
                }
            });

            // Dot click handlers
            $('.horizontal-cards-dot').off('click').on('click', function () {
                const targetIndex = parseInt($(this).attr('data-index'));
                updateCarousel(targetIndex);
            });

            // İlk durumu ayarla
            updateCarousel(0);
        };

        // Initialize horizontal cards carousel
        initHorizontalCardsCarousel();

        // Resize'da yeniden kontrol et
        let resizeTimer;
        $(window).on('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                initHorizontalCardsCarousel();
            }, 250);
        });
    });


    /* Mini Carousel - Infinite, Autoplay & Drag */
    const initMiniCarousel = () => {
        const $container = $('.carousel-container');
        const $track = $('.carousel-track');
        const $items = $track.find('.carousel-item');
        const $prevBtn = $('.carousel-prev');
        const $nextBtn = $('.carousel-next');

        if (!$container.length || !$items.length) return;

        const originalItemCount = $items.length;

        // Clone (infinite)
        $items.each(function () {
            $track.append($(this).clone().addClass('carousel-clone'));
        });
        $items.each(function () {
            $track.prepend($(this).clone().addClass('carousel-clone'));
        });

        const isMobile = () => $(window).width() < 768;
        const isTablet = () => $(window).width() < 1024;

        const calculateItemWidth = () => {
            const containerWidth = $container.width();
            const gap = isMobile() ? 0 : 16;

            if (isTablet() && !isMobile()) {
                return (containerWidth - gap * 2) / 2;
            }

            if (isMobile()) {
                return containerWidth;
            }

            return (containerWidth - gap * 2) / 3;
        };

        const updateItemWidths = () => {
            const w = calculateItemWidth();
            $track.find('.carousel-item').css('width', `${w}px`);
        };

        let currentIndex = originalItemCount;
        let isAnimating = false;
        let autoplay;

        const move = (instant = false) => {
            const gap = isMobile() ? 0 : 16;
            const x = -(currentIndex * (calculateItemWidth() + gap));

            $track.css({
                transition: instant ? 'none' : 'transform 500ms ease',
                transform: `translateX(${x}px)`
            });
        };

        const normalizeIndex = () => {
            if (currentIndex >= originalItemCount * 2) {
                currentIndex -= originalItemCount;
                move(true);
            }
            if (currentIndex < originalItemCount) {
                currentIndex += originalItemCount;
                move(true);
            }
        };

        const next = () => {
            if (isAnimating) return;
            isAnimating = true;

            currentIndex++;
            move();

            setTimeout(() => {
                normalizeIndex();
                isAnimating = false;
            }, 500);
        };

        const prev = () => {
            if (isAnimating) return;
            isAnimating = true;

            currentIndex--;
            move();

            setTimeout(() => {
                normalizeIndex();
                isAnimating = false;
            }, 500);
        };

        /* =====================
           TOUCH / DRAG
        ====================== */
        let startX = 0;
        let currentX = 0;
        let dragging = false;

        $container.on('touchstart', (e) => {
            clearInterval(autoplay);
            dragging = true;
            startX = e.originalEvent.touches[0].clientX;
            currentX = startX;
            $track.css('transition', 'none');
        });

        $container.on('touchmove', (e) => {
            if (!dragging) return;

            currentX = e.originalEvent.touches[0].clientX;
            const diff = currentX - startX;
            const gap = isMobile() ? 0 : 16;
            const x = -(currentIndex * (calculateItemWidth() + gap)) + diff;

            $track.css('transform', `translateX(${x}px)`);
        });

        $container.on('touchend', () => {
            if (!dragging) return;
            dragging = false;

            const diff = currentX - startX;
            const threshold = calculateItemWidth() / 4;

            if (diff < -threshold) {
                next();
            } else if (diff > threshold) {
                prev();
            } else {
                move();
            }

            startAutoplay();
        });

        /* =====================
           BUTTONS
        ====================== */
        $nextBtn.on('click', () => {
            clearInterval(autoplay);
            next();
            startAutoplay();
        });

        $prevBtn.on('click', () => {
            clearInterval(autoplay);
            prev();
            startAutoplay();
        });

        /* =====================
           AUTOPLAY
        ====================== */
        const startAutoplay = () => {
            clearInterval(autoplay);
            autoplay = setInterval(next, 5000);
        };

        $container
            .on('mouseenter', () => clearInterval(autoplay))
            .on('mouseleave', startAutoplay);

        /* =====================
           INIT
        ====================== */
        updateItemWidths();
        move(true);
        startAutoplay();

        $(window).on('resize', () => {
            updateItemWidths();
            move(true);
        });
    };


    // Initialize mini carousel
    initMiniCarousel();

    /* Mobile Cards Carousel - Infinite, Autoplay & Smooth Drag */
    const initMobileCardsCarousel = () => {
        const $container = $('.mobile-cards-carousel-container');
        const $track = $('.mobile-cards-carousel-track');
        const $items = $track.find('.mobile-cards-carousel-item');

        if (!$container.length || !$items.length) return;

        const itemsPerView = 2;
        const originalCount = $items.length;

        // Clone (infinite için)
        $items.clone().appendTo($track);
        $items.clone().prependTo($track);

        let currentIndex = originalCount;
        let isAnimating = false;
        let autoplay;

        const itemWidth = () => $container.width() / itemsPerView;

        const updateWidths = () => {
            const w = itemWidth();
            $track.find('.mobile-cards-carousel-item').css('width', `${w}px`);
        };

        const move = (instant = false) => {
            const x = -(currentIndex * itemWidth());
            $track.css({
                transition: instant ? 'none' : 'transform 500ms ease',
                transform: `translateX(${x}px)`
            });
        };

        const normalizeIndex = () => {
            if (currentIndex >= originalCount * 2) {
                currentIndex -= originalCount;
                move(true);
            }

            if (currentIndex < originalCount) {
                currentIndex += originalCount;
                move(true);
            }
        };

        const next = () => {
            if (isAnimating) return;
            isAnimating = true;

            currentIndex += itemsPerView;
            move();

            setTimeout(() => {
                normalizeIndex();
                isAnimating = false;
            }, 500);
        };

        const prev = () => {
            if (isAnimating) return;
            isAnimating = true;

            currentIndex -= itemsPerView;
            move();

            setTimeout(() => {
                normalizeIndex();
                isAnimating = false;
            }, 500);
        };

        const start = () => {
            clearInterval(autoplay);
            autoplay = setInterval(next, 5000);
        };

        /* =====================
           TOUCH / DRAG
        ====================== */
        let startX = 0;
        let currentX = 0;
        let isDragging = false;

        $container.on('touchstart', (e) => {
            clearInterval(autoplay);
            isDragging = true;
            startX = e.originalEvent.touches[0].clientX;
            currentX = startX;

            $track.css('transition', 'none');
        });

        $container.on('touchmove', (e) => {
            if (!isDragging) return;

            currentX = e.originalEvent.touches[0].clientX;
            const diff = currentX - startX;
            const x = -(currentIndex * itemWidth()) + diff;

            $track.css('transform', `translateX(${x}px)`);
        });

        $container.on('touchend', () => {
            if (!isDragging) return;
            isDragging = false;

            const diff = currentX - startX;
            const threshold = itemWidth() / 4;

            if (diff < -threshold) {
                // sola swipe → ileri
                next();
            } else if (diff > threshold) {
                // sağa swipe → geri
                prev();
            } else {
                // snap back
                move();
            }

            start();
        });

        // Init
        updateWidths();
        move(true);
        start();

        $(window).on('resize', () => {
            updateWidths();
            move(true);
        });
    };



    // Initialize mobile cards carousel
    initMobileCardsCarousel();


});



