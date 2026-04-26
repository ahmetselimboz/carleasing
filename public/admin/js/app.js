/**
 * Admin UI Kit - jQuery App
 * Sidebar, Dark/Light mode, Tooltip, Dropdown, Animasyonlar
 */

(function($) {
    'use strict';

    var DARK_KEY = 'admin-ui-dark-mode';

    /* ============================================================
       DARK MODE
       ============================================================ */
    function applyDarkMode(isDark) {
        $('html').toggleClass('dark-mode', isDark);
        $('body').toggleClass('dark-mode', isDark);
        $('.dark-mode-toggle i').removeClass('ri-moon-line ri-sun-line')
            .addClass(isDark ? 'ri-sun-line' : 'ri-moon-line');
    }

    function initDarkMode() {
        applyDarkMode(localStorage.getItem(DARK_KEY) === 'true');
    }

    $(document).on('click', '.dark-mode-toggle', function(e) {
        e.stopPropagation();
        var newMode = !$('body').hasClass('dark-mode');
        localStorage.setItem(DARK_KEY, newMode);
        applyDarkMode(newMode);
    });

    /* ============================================================
       SIDEBAR
       ============================================================ */
    $(document).off('click.sidebar').on('click.sidebar', '.sidebar-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $sidebar = $('.sidebar');
        if ($sidebar.hasClass('sidebar-open')) {
            $sidebar.removeClass('sidebar-open');
            $('.sidebar-overlay').stop().fadeOut(250);
        } else {
            $sidebar.addClass('sidebar-open');
            $('.sidebar-overlay').stop().fadeIn(250);
        }
    });

    $(document).on('click', '.sidebar-overlay', function() {
        $('.sidebar').removeClass('sidebar-open');
        $(this).stop().fadeOut(250);
    });

    /* ============================================================
       SUBMENU
       ============================================================ */
    $(document).on('click', '.has-submenu > a', function(e) {
        var href = $(this).attr('href');
        if (href && href !== '#') {
            return;
        }
        e.preventDefault();
        var $parent = $(this).parent();
        $parent.toggleClass('open');
        $parent.find('.submenu').stop().slideToggle(280);
    });

    /* ============================================================
       TOOLTIP
       ============================================================ */
    $(document).on('mouseenter', '[data-tooltip]', function() {
        var $el = $(this);
        var text = $el.data('tooltip');
        var pos = $el.attr('data-tooltip-position') || $el.attr('data-tooltip-pos') || 'top';
        if (!$('#ui-tooltip').length) $('body').append('<div id="ui-tooltip"></div>');
        var $tip = $('#ui-tooltip');
        $tip.text(text).attr('data-pos', pos).addClass('tooltip-visible');
        var off = $el.offset(), w = $el.outerWidth(), h = $el.outerHeight();
        var tipW = $tip.outerWidth(), tipH = $tip.outerHeight(), gap = 8;
        var top, left;
        if (pos === 'top')         { top = off.top - tipH - gap; left = off.left + (w - tipW) / 2; }
        else if (pos === 'bottom') { top = off.top + h + gap;    left = off.left + (w - tipW) / 2; }
        else if (pos === 'left')   { top = off.top + (h - tipH) / 2; left = off.left - tipW - gap; }
        else                       { top = off.top + (h - tipH) / 2; left = off.left + w + gap; }
        $tip.css({ top: top, left: left });
    });
    $(document).on('mouseleave', '[data-tooltip]', function() {
        $('#ui-tooltip').removeClass('tooltip-visible');
    });

    /* ============================================================
       DROPDOWN (data-dropdown)
       ============================================================ */
    $(document).on('click', '[data-dropdown-toggle]', function(e) {
        e.stopPropagation();
        var target = $($(this).data('dropdown-toggle'));
        if (target.is(':visible')) target.fadeOut(200);
        else { $('[data-dropdown]').fadeOut(200); target.fadeIn(200); }
    });
    $(document).on('click', function() { $('[data-dropdown]').fadeOut(200); });
    $(document).on('click', '[data-dropdown]', function(e) { e.stopPropagation(); });

    /* ============================================================
       BUTTON RIPPLE EFFECT
       ============================================================ */
    $(document).on('click', 'button:not(.dark-mode-toggle):not(.sidebar-toggle), a.btn, .btn', function(e) {
        var $btn = $(this);
        if ($btn.css('position') === 'static') $btn.css('position', 'relative');
        $btn.css('overflow', 'hidden');
        var rect = this.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        var $ripple = $('<span class="ripple-effect"></span>').css({ left: x, top: y });
        $btn.append($ripple);
        setTimeout(function() { $ripple.remove(); }, 600);
    });

    /* ============================================================
       SCROLL REVEAL - Elemanlar görünür alanda belirince animasyon
       ============================================================ */
    function initScrollReveal() {
        var $reveals = $('[data-reveal]');
        if (!$reveals.length) return;

        $reveals.css({ opacity: 0, transform: 'translateY(20px)', transition: 'opacity 0.5s ease, transform 0.5s ease' });

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var $el = $(entry.target);
                    var delay = ($el.data('reveal-delay') || 0) * 1;
                    setTimeout(function() {
                        $el.css({ opacity: 1, transform: 'translateY(0)' });
                    }, delay);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        $reveals.each(function() { observer.observe(this); });
    }

    /* ============================================================
       COUNTER ANIMATION - Stat kartlarındaki rakamları animasyonlu sayar
       ============================================================ */
    function animateCounters() {
        $('[data-count-to]').each(function() {
            var $el = $(this);
            if ($el.data('counted')) return;
            $el.data('counted', true);
            var target = parseFloat($el.data('count-to'));
            var prefix = $el.data('count-prefix') || '';
            var suffix = $el.data('count-suffix') || '';
            var decimals = ($el.data('count-decimals') || 0) * 1;
            var duration = ($el.data('count-duration') || 1000) * 1;
            $({ val: 0 }).animate({ val: target }, {
                duration: duration,
                easing: 'swing',
                step: function() {
                    $el.text(prefix + this.val.toFixed(decimals) + suffix);
                },
                complete: function() {
                    $el.text(prefix + target.toFixed(decimals) + suffix);
                }
            });
        });
    }

    /* ============================================================
       CARD HOVER LIFT
       ============================================================ */
    $(document).on('mouseenter', '.card, .stat-card, .comp-card, .quick-card', function() {
        $(this).css({ transition: 'transform 150ms ease-in-out', transform: 'translateY(-2px)', boxShadow: '0 8px 24px rgba(55, 0, 138, 0.08)' });
    }).on('mouseleave', '.card, .stat-card, .comp-card, .quick-card', function() {
        $(this).css({ transform: 'translateY(0)', boxShadow: 'none' });
    });

    /* ============================================================
       SIDEBAR NAV LINK HOVER - ikon animasyonu
       ============================================================ */
    $(document).on('mouseenter', '.sidebar nav a, aside nav a', function() {
        $(this).find('i').stop().animate({ marginRight: '2px' }, 120);
    }).on('mouseleave', '.sidebar nav a, aside nav a', function() {
        $(this).find('i').stop().animate({ marginRight: '0px' }, 120);
    });

    /* ============================================================
       ALERT DISMISS
       ============================================================ */
    $(document).on('click', '[data-dismiss="alert"]', function() {
        $(this).closest('.alert, [role="alert"]').slideUp(300, function() { $(this).remove(); });
    });

    /* ============================================================
       SMOOTH SCROLL TO TOP
       ============================================================ */
    function initScrollTop() {
        var $btn = $('<button class="scroll-top-btn" title="Yukarı"><i class="ri-arrow-up-line"></i></button>');
        $('body').append($btn);

        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 400) $btn.addClass('visible');
            else $btn.removeClass('visible');
        });

        $btn.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 500);
        });
    }

    /* ============================================================
       PROGRESS BAR ANIMATION
       ============================================================ */
    function animateProgressBars() {
        $('[data-progress]').each(function() {
            var $bar = $(this);
            if ($bar.data('animated')) return;
            $bar.data('animated', true);
            var target = $bar.data('progress');
            $bar.css('width', '0%').animate({ width: target + '%' }, 800, 'swing');
        });
    }

    /* ============================================================
       TABLE ROW STAGGER - Tablo satırları sıralı fade-in
       ============================================================ */
    function animateTableRows() {
        $('table tbody tr').each(function(i) {
            $(this).css('opacity', 0).delay(i * 30).animate({ opacity: 1 }, 300);
        });
    }

    /* ============================================================
       INIT
       ============================================================ */
    $(function() {
        initDarkMode();
        initScrollTop();
        initScrollReveal();
        animateCounters();
        animateProgressBars();
        animateTableRows();

        $('.stat-card, .card, .comp-card, .quick-card').each(function(i) {
            $(this).css({ opacity: 0, transform: 'translateY(16px)' })
                .delay(i * 60)
                .queue(function(next) {
                    $(this).css({ transition: 'opacity 0.4s ease, transform 0.4s ease', opacity: 1, transform: 'translateY(0)' });
                    next();
                });
        });

        $('section h2').each(function(i) {
            var $h = $(this);
            $h.css({ opacity: 0 }).delay(200 + i * 80).queue(function(next) {
                $h.css({ transition: 'opacity 0.4s ease', opacity: 1 });
                next();
            });
        });
    });

})(jQuery);
