$(function () {
    const config = window.AppConfig || {};
    const FAVORITES_KEY = 'carleasing:favorites';

    document.documentElement.classList.add('is-loaded');
    setTimeout(() => {
        document.body.classList.remove('preload');
    }, 1000);

    const $body = $('body');
    const getFavorites = () => {
        try {
            const parsed = JSON.parse(localStorage.getItem(FAVORITES_KEY) || '[]');
            return Array.isArray(parsed) ? parsed.filter(Boolean) : [];
        } catch (err) {
            return [];
        }
    };
    const renderFavoriteCounters = () => {
        const favorites = Array.from(new Set(getFavorites()));
        const count = favorites.length;
        $('[data-favorites-count-badge]').each(function () {
            if (count > 0) {
                $(this).removeClass('hidden').addClass('inline-flex').text(count > 99 ? '99+' : count);
            } else {
                $(this).removeClass('inline-flex').addClass('hidden').text('');
            }
        });
        $('[data-favorites-count-inline]').text(count > 0 ? `(${count})` : '');
    };
    renderFavoriteCounters();
    window.addEventListener('storage', renderFavoriteCounters);
    window.addEventListener('favorites:changed', renderFavoriteCounters);

    // Tema tercihini localStorage'dan yükle
    const savedTheme = localStorage.getItem('theme') || 'light';
    $body.attr('data-theme', savedTheme);
    $('[data-theme-icon="light"]').toggleClass('hidden', savedTheme !== 'light');
    $('[data-theme-icon="dark"]').toggleClass('hidden', savedTheme === 'light');
    $('.tv-ticker-tape').each(function () {
        $(this).attr('theme', savedTheme);
    });
    // Tema toggle fonksiyonu
    $('#themeToggle').on('click', function () {
        const currentTheme = $body.attr('data-theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';

        // Tema değişikliğini uygula
        $body.attr('data-theme', newTheme);
        $('[data-theme-icon="light"]').toggleClass('hidden', newTheme !== 'light');
        $('[data-theme-icon="dark"]').toggleClass('hidden', newTheme === 'light');
        $('.tv-ticker-tape').each(function () {
            $(this).attr('theme', newTheme);
        });
        // localStorage'a kaydet
        localStorage.setItem('theme', newTheme);
    });

    $('[data-tooltip]').each(function () {
        const $el = $(this);
        const text = $el.attr('data-tooltip');
        const position = $el.attr('data-tooltip-position') || 'top';

        if (!$el.find('.tooltip-bubble').length) {
            let positionClasses = '';
            switch (position) {
                case 'top':
                    positionClasses = 'left-1/2 -translate-x-1 bottom-full mb-2';
                    break;
                case 'bottom':
                    positionClasses = 'left-1/2 translate-x-0 top-full mt-2';
                    break;
                case 'left':
                    positionClasses = 'right-0 mr-2 top-1/2 -translate-y-1/2';
                    break;
                case 'right':
                    positionClasses = 'left-0 ml-2 top-1/2 -translate-y-1/2';
                    break;
                default:
                    positionClasses = 'left-1/2 -translate-x-1/2 bottom-full mb-2';
            }

            const tooltipEl = $('<span>')
                .addClass(`tooltip-bubble absolute ${positionClasses} px-3 py-1.5 rounded-lg text-xs text-white shadow-lg whitespace-nowrap z-50`)
                .text(text);
            $el
                .addClass('relative')
                .append(tooltipEl);
        }
    }).on('mouseenter focus', function () {
        const $el = $(this);
        const $tooltip = $el.find('.tooltip-bubble');
        if ($tooltip.length) {
            const isDark = $('body').attr('data-theme') === 'dark';
            const bgColor = isDark ? 'rgba(31, 41, 55, 0.95)' : 'rgba(15, 23, 42, 0.95)';
            $tooltip.css('background-color', bgColor).addClass('is-open');
        }
    }).on('mouseleave blur', function () {
        $(this).find('.tooltip-bubble').removeClass('is-open');
    });

    /* Offcanvas */
    const openNavcanvas = () => {
        const $off = $('#offNavcanvas');
        $off.removeClass('hidden');
        requestAnimationFrame(() => {
            $off.find('.offnavcanvas-overlay').addClass('is-open');
            $off.find('.offnavcanvas-panel').addClass('is-open');
        });
    };
    const closeNavcanvas = () => {
        const $off = $('#offNavcanvas');
        $off.find('.offnavcanvas-overlay, .offnavcanvas-panel').removeClass('is-open');
        setTimeout(() => $off.addClass('hidden'), 220);
    };
    $('#openNavcanvas').on('click', openNavcanvas);
    $('.offnavcanvas-close').on('click', closeNavcanvas);

    // Overlay'e tıklandığında kapat
    $('#offNavcanvas').on('click', function (e) {
        if (e.target === this || $(e.target).hasClass('offnavcanvas-overlay')) {
            closeNavcanvas();
        }
    });

    // Panel içindeki tıklamaların dışarı çıkmasını engelle
    $('#offNavcanvas .offnavcanvas-panel').on('click', function (e) {
        e.stopPropagation();
    });

    /* Sondakika Slider - Swiper.js (yatay, autoplay, pagination, hover durur) */
    if (typeof Swiper !== 'undefined' && document.querySelector('.sondakika-swiper')) {
        const swiperInstance = new Swiper('.sondakika-swiper', {
            direction: 'horizontal',
            loop: true,
            slidesPerView: 'auto',
            spaceBetween: 16,
            allowTouchMove: true,
            speed: 4000,
            autoplay: {
                delay: 1,
                disableOnInteraction: false,
            }
        });
        const wrapper = document.querySelector('.sondakika-swiper-wrapper');
        if (wrapper) {
            wrapper.addEventListener('mouseenter', () => swiperInstance.autoplay.stop());
            wrapper.addEventListener('mouseleave', () => swiperInstance.autoplay.start());
        }
    }

 

    /* Navbar Search Button */
    const $navbarSearchBox = $('#navbar-search-box');
    if ($navbarSearchBox.length) {
        $('.navbar-search-button').on('click', function () {
            $navbarSearchBox.toggleClass('translate-y-[115%] border-b');
        });
    }

    /* Modal */
    const openSearchModal = () => {
        const modal = $('#search-modal');
        modal.removeClass('hidden');
        requestAnimationFrame(() => {
            modal.find('.search-modal-overlay').addClass('is-open');
            modal.find('.search-modal-card').addClass('is-open');
        });
    };
    const closeSearchModal = () => {
        const modal = $('#search-modal');
        modal.find('.search-modal-overlay, .search-modal-card').removeClass('is-open');
        setTimeout(() => modal.addClass('hidden'), 220);
    };

    $('.navbar-search-button').on('click', openSearchModal);
    $('.search-modal-close').on('click', closeSearchModal);
    $('#search-modal').on('click', function (e) {
        if (e.target === this) closeSearchModal();
    });

    /* Modal */
    const openUserLoginModal = () => {
        const $modal = $('#user-login-modal');
        $modal.removeClass('hidden');
        requestAnimationFrame(() => {
            $modal.find('.user-login-modal-overlay').addClass('is-open');
            $modal.find('.user-login-modal-card').addClass('is-open');
        });
    };
    const closeUserLoginModal = () => {
        const $modal = $('#user-login-modal');
        $modal.find('.user-login-modal-overlay, .user-login-modal-card').removeClass('is-open');
        setTimeout(() => $modal.addClass('hidden'), 220);
    };

    $('.user-modal').on('click', openUserLoginModal);
    $('.user-login-modal-close').on('click', closeUserLoginModal);
    $('#user-login-modal').on('click', function (e) {
        if (e.target === this) closeUserLoginModal();
    });

    $('#redirectRegisterToLogin').on('click', function () {
        closeUserLoginModal();
        $('#user-register-modal').removeClass('hidden');
        requestAnimationFrame(() => {
            $('#user-register-modal').find('.user-register-modal-overlay').addClass('is-open');
            $('#user-register-modal').find('.user-register-modal-card').addClass('is-open');
        });
    });

    const openUserRegisterModal = () => {
        const $modal = $('#user-register-modal');
        $modal.removeClass('hidden');
        requestAnimationFrame(() => {
            $modal.find('.user-register-modal-overlay').addClass('is-open');
            $modal.find('.user-register-modal-card').addClass('is-open');
        });
    };
    const closeUserRegisterModal = () => {
        const $modal = $('#user-register-modal');
        $modal.find('.user-register-modal-overlay, .user-register-modal-card').removeClass('is-open');
        setTimeout(() => $modal.addClass('hidden'), 220);
    };


    $('.user-register-modal-close').on('click', closeUserRegisterModal);
    $('#user-register-modal').on('click', function (e) {
        if (e.target === this) closeUserRegisterModal();
    });

    $('#redirectLoginToRegister').on('click', function () {
        closeUserRegisterModal();
        $('#user-login-modal').removeClass('hidden');
        requestAnimationFrame(() => {
            $('#user-login-modal').find('.user-login-modal-overlay').addClass('is-open');
            $('#user-login-modal').find('.user-login-modal-card').addClass('is-open');
        });
    });

    $("#userlogin").on("submit", function (e) {
        $.ajax({
            url: config.routes.login,
            type: "POST",
            data: {
                "_token": config.csrfToken,
                usermail: $("input[name=usermail]").val(),
                userpassword: $("input[name=userpassword]").val()
            },
            success: function (response) {

                if (response === "ok") {

                    showToast('success', 'Giriş başarılı!');
                    window.location.href = "/profilim";
                } else {

                    showToast('error', 'Kullanıcı adı veya şifre hatalı!');
                }
            },
            error: function (response) {
                showToast('error', 'Kullanıcı adı veya şifre hatalı!');
            },
        });
        e.preventDefault();
    });
    $("#userregister").on("submit", function (e) {
        console.log(config.routes.register);
        $.ajax({
            url: config.routes.register,
            type: "POST",
            data: {
                "_token": config.csrfToken,
                usernamereg: $("input[name=usernamereg]").val(),
                usermailreg: $("input[name=usermailreg]").val(),
                userpasswordreg: $("input[name=userpasswordreg]").val()
            },
            success: function (response) {

                if (response === "ok") {
                    $(".loginerrorreg").addClass("d-none");
                    $(".loginsuccessreg").removeClass("d-none");
                    window.location.href = "/profilim";
                } else {
                    $(".loginerrorreg").removeClass("d-none");
                    $(".loginsuccessreg").addClass("d-none");
                }
            },
            error: function (response) { },
        });
        e.preventDefault();
    });


    /* Submenu Toggle */
    $('button[data-target]').on('click', function () {
        const target = $(this).data('target');

        if (!target) return;

        const $submenu = $(`#${target}`);
        console.log($submenu);
        const $icon = $(this).find('i');

        // Close other submenus
        $('button[data-target]').not(this).each(function () {
            const otherTarget = $(this).data('target');
            $(`#${otherTarget}`).addClass('hidden');
            $(this).find('i').removeClass('rotate-180');
        });

        // Toggle current submenu
        $submenu.toggleClass('hidden');
        $icon.toggleClass('rotate-180');
    });


    const showToast = (type = 'success', message = 'Başarılı!') => {
        const title = {
            success: {
                title: 'Başarılı',
                icon: 'ri-check-line text-[var(--color-success)]',
            },
            error: {
                title: 'Hata',
                icon: 'ri-close-large-line text-[var(--color-danger)]',
            },
            warning: {
                title: 'Uyarı',
                icon: 'ri-error-warning-fil text-[var(--color-warning)]',
            },
            info: {
                title: 'Bilgi',
                icon: 'ri-info-i text-[var(--color-info)]',
            }
        };

        const toastTitle = title[type].title || title.success.title;
        const toastIcon = title[type].icon || title.success.icon;


        const $toast = $('#toast');
        $toast.removeClass('hidden');
        $toast.find('#toast-title').text(toastTitle);
        $toast.find('#toast-message').text(message);
        $toast.find('#toast-icon').addClass(toastIcon);
        requestAnimationFrame(() => $toast.find('.toast-shell').addClass('is-open'));
        clearTimeout($toast.data('timer'));
        const t = setTimeout(() => {
            $toast.find('.toast-shell').removeClass('is-open');
            setTimeout(() => $toast.addClass('hidden'), 200);
        }, 3000);
        $toast.data('timer', t);
    };

    $('.toast-close').on('click', () => {
        const $toast = $('#toast');
        $toast.find('.toast-shell').removeClass('is-open');
        setTimeout(() => $toast.addClass('hidden'), 180);
    });

    /* Foto Galeri Carousel - Infinite & Autoplay */
    const initGalleryCarousel = () => {
        const $container = $('.lite-gallery-slider');
        const $track = $('.lite-gallery-container');
        const $items = $track.find('.lite-gallery-item');

        if (!$container.length || $items.length === 0) return;

        // Orijinal item sayısı
        const originalItemCount = $items.length;

        // Infinite loop için item'ları clone et (başa ve sona)
        $items.each(function () {
            $track.append($(this).clone().addClass('lite-gallery-clone'));
        });
        $items.each(function () {
            $track.prepend($(this).clone().addClass('lite-gallery-clone'));
        });

        // Responsive item genişliğini hesapla
        const calculateItemWidth = () => {
            const containerWidth = $container.width();
            // Mobilde 1, tablette 2, desktop'ta 3-4 item göster
            if (containerWidth < 640) {
                return containerWidth; // Mobil: tam genişlik
            } else if (containerWidth < 1024) {
                return containerWidth / 2; // Tablet: 2 item
            } else {
                return containerWidth / 3; // Desktop: 3 item
            }
        };

        // Item genişliklerini ayarla
        const updateItemWidths = () => {
            const itemWidth = calculateItemWidth();
            $track.find('.lite-gallery-item').css({
                'width': `${itemWidth}px`,
                'min-width': `${itemWidth}px`
            });
        };

        // Başlangıç pozisyonunu ayarla (clone'ların sonrasına)
        let currentIndex = originalItemCount;
        let isTransitioning = false;
        let autoplayInterval = null;

        const updateCarousel = (index, instant = false) => {
            if (isTransitioning && !instant) return;

            const itemWidth = calculateItemWidth();
            const translateX = -(index * itemWidth);

            if (instant) {
                $track.css('transition', 'none');
                $track.css('transform', `translateX(${translateX}px)`);
                setTimeout(() => {
                    $track.css('transition', 'transform 500ms ease-in-out');
                }, 50);
            } else {
                $track.css('transition', 'transform 500ms ease-in-out');
                requestAnimationFrame(() => {
                    $track.css('transform', `translateX(${translateX}px)`);
                });
            }
        };

        const nextSlide = () => {
            if (isTransitioning) return;
            isTransitioning = true;

            currentIndex++;

            $track.css('transition', 'transform 500ms ease-in-out');
            requestAnimationFrame(() => {
                const itemWidth = calculateItemWidth();
                const translateX = -(currentIndex * itemWidth);
                $track.css('transform', `translateX(${translateX}px)`);
            });

            // Eğer son clone'a geldiysek, başa dön (smooth reset)
            if (currentIndex >= originalItemCount * 2) {
                setTimeout(() => {
                    currentIndex = originalItemCount;
                    updateCarousel(currentIndex, true);
                    setTimeout(() => {
                        isTransitioning = false;
                    }, 50);
                }, 500);
            } else {
                setTimeout(() => {
                    isTransitioning = false;
                }, 500);
            }
        };

        // Autoplay
        const startAutoplay = () => {
            clearInterval(autoplayInterval);
            autoplayInterval = setInterval(() => {
                nextSlide();
            }, 4000); // 4 saniyede bir
        };

        // Hover'da pause
        $container.on('mouseenter', () => {
            clearInterval(autoplayInterval);
        }).on('mouseleave', () => {
            startAutoplay();
        });

        // İlk durumu ayarla
        updateItemWidths();
        updateCarousel(currentIndex, true);

        // Resize'da yeniden hesapla
        let resizeTimer;
        $(window).on('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                updateItemWidths();
                updateCarousel(currentIndex, true);
            }, 250);
        });

        // Autoplay'i başlat
        startAutoplay();
    };

    // Carousel'i başlat
    initGalleryCarousel();

    const setTab = (target, groupSelector, panelSelector, activeClasses, inactiveClasses) => {
        $(groupSelector).removeClass(activeClasses).addClass(inactiveClasses);
        $(panelSelector).addClass('hidden');
        $(target.btn).removeClass(inactiveClasses).addClass(activeClasses);
        $(target.panel).removeClass('hidden');
    };

    $('.tab-btn-vert').on('click', function () {
        const targetId = $(this).data('tab-target');

        setTab(
            { btn: this, panel: targetId },
            '.tab-btn-vert',
            '.tab-panel-vert',
            'bg-[var(--color-surface)] text-[var(--color-text)] border-[var(--color-primary)]',
            'text-[var(--color-primary)] border-[var(--color-border)]'
        );
    });

})


