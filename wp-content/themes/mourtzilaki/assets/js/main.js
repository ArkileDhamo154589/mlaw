(function () {
    'use strict';

    var doc  = document;
    var body = doc.body;
    var win  = window;

    /* ---------- Slick carousel (hero) ---------- */
    if (win.jQuery && win.jQuery.fn && win.jQuery.fn.slick) {
        jQuery(function ($) {
            var $hero = $('.hero-slick');
            if (!$hero.length) { return; }
            var prevSvg = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>';
            var nextSvg = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>';
            $hero.slick({
                autoplay: true,
                autoplaySpeed: 6000,
                speed: 800,
                fade: true,
                cssEase: 'cubic-bezier(.22,.61,.36,1)',
                arrows: true,
                dots: true,
                pauseOnHover: false,
                pauseOnFocus: true,
                infinite: true,
                prevArrow: '<button type="button" class="slick-prev" aria-label="Προηγούμενο">' + prevSvg + '</button>',
                nextArrow: '<button type="button" class="slick-next" aria-label="Επόμενο">'   + nextSvg + '</button>'
            });
        });
    }

    /* ---------- Sticky header shadow on scroll ---------- */
    var header = doc.getElementById('site-header');
    if (header) {
        var lastY = -1;
        var onHeaderScroll = function () {
            var y = win.scrollY;
            if (y === lastY) { return; }
            lastY = y;
            header.classList.toggle('is-scrolled', y > 4);
        };
        onHeaderScroll();
        win.addEventListener('scroll', onHeaderScroll, { passive: true });
    }

    /* ---------- Mobile menu toggle (with proper scroll lock) ---------- */
    var toggle = doc.querySelector('.menu-toggle');
    var menu   = doc.getElementById('mobile-menu');
    if (toggle && menu) {
        var savedScrollY = 0;

        var setOpen = function (open) {
            if (open) {
                savedScrollY = win.scrollY;
                body.classList.add('menu-open');
                // Lock scroll without losing position (iOS-safe).
                body.style.position = 'fixed';
                body.style.top = '-' + savedScrollY + 'px';
                body.style.left = '0';
                body.style.right = '0';
                body.style.width = '100%';
            } else {
                body.classList.remove('menu-open');
                body.style.position = '';
                body.style.top = '';
                body.style.left = '';
                body.style.right = '';
                body.style.width = '';
                win.scrollTo(0, savedScrollY);
            }
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            menu.setAttribute('aria-hidden', open ? 'false' : 'true');
        };

        toggle.addEventListener('click', function () {
            setOpen(!body.classList.contains('menu-open'));
        });

        menu.addEventListener('click', function (e) {
            if (e.target.closest('a')) { setOpen(false); }
        });

        doc.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && body.classList.contains('menu-open')) {
                setOpen(false);
            }
        });

        var mq = win.matchMedia('(min-width: 901px)');
        var onMq = function (ev) {
            if (ev.matches && body.classList.contains('menu-open')) { setOpen(false); }
        };
        if (mq.addEventListener)      { mq.addEventListener('change', onMq); }
        else if (mq.addListener)       { mq.addListener(onMq); }
    }

    /* ---------- Reveal-on-scroll animations ---------- */
    var reveals = doc.querySelectorAll('.reveal');
    if (reveals.length) {
        if ('IntersectionObserver' in win) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('is-visible');
                        io.unobserve(e.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
            reveals.forEach(function (el) { io.observe(el); });
        } else {
            reveals.forEach(function (el) { el.classList.add('is-visible'); });
        }
    }

    /* ---------- Back to top ---------- */
    var btt = doc.getElementById('back-to-top');
    if (btt) {
        var onBttScroll = function () {
            btt.classList.toggle('is-visible', win.scrollY > 480);
        };
        onBttScroll();
        win.addEventListener('scroll', onBttScroll, { passive: true });
        btt.addEventListener('click', function () {
            if ('scrollBehavior' in doc.documentElement.style) {
                win.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                win.scrollTo(0, 0);
            }
        });
    }
})();
