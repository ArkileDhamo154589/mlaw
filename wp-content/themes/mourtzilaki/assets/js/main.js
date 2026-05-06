(function () {
    'use strict';

    var doc  = document;
    var body = doc.body;
    var win  = window;

    /* ---------- Slick carousels ---------- */
    if (win.jQuery && win.jQuery.fn && win.jQuery.fn.slick) {
        jQuery(function ($) {
            var prevSvg = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>';
            var nextSvg = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>';

            var $hero = $('.hero-slick');
            if ($hero.length) {
                $hero.slick({
                    autoplay: true, autoplaySpeed: 6000, speed: 800,
                    fade: true, cssEase: 'cubic-bezier(.22,.61,.36,1)',
                    arrows: true, dots: true, pauseOnHover: false, pauseOnFocus: true, infinite: true,
                    prevArrow: '<button type="button" class="slick-prev" aria-label="Προηγούμενο">' + prevSvg + '</button>',
                    nextArrow: '<button type="button" class="slick-next" aria-label="Επόμενο">' + nextSvg + '</button>'
                });
            }

            // Testimonials (homepage) — 1 per slide, fade
            var $ts = $('.testimonials-slick');
            if ($ts.length) {
                $ts.slick({
                    autoplay: true, autoplaySpeed: 7000, speed: 700,
                    fade: true, arrows: false, dots: true, infinite: true, pauseOnHover: true,
                });
            }

            // Reviews page carousel — 3 visible, scroll
            var $rv = $('.reviews-slick');
            if ($rv.length) {
                $rv.slick({
                    slidesToShow: 3, slidesToScroll: 1, speed: 600,
                    autoplay: true, autoplaySpeed: 6000, pauseOnHover: true,
                    arrows: true, dots: true, infinite: true,
                    prevArrow: '<button type="button" class="slick-prev" aria-label="Προηγούμενο">' + prevSvg + '</button>',
                    nextArrow: '<button type="button" class="slick-next" aria-label="Επόμενο">' + nextSvg + '</button>',
                    responsive: [
                        { breakpoint: 1100, settings: { slidesToShow: 2 } },
                        { breakpoint: 760,  settings: { slidesToShow: 1, arrows: false } }
                    ]
                });
            }
        });
    }

    /* ---------- Cases filter ---------- */
    var caseFilters = doc.querySelectorAll('.cases-filter');
    var caseGrid    = doc.querySelector('.cases-grid');
    var casesEmpty  = doc.getElementById('cases-empty');
    if (caseFilters.length && caseGrid) {
        var caseCards = caseGrid.querySelectorAll('.case-card');
        caseFilters.forEach(function (btn) {
            btn.addEventListener('click', function () {
                caseFilters.forEach(function (b) { b.classList.remove('is-active'); });
                btn.classList.add('is-active');
                var f = btn.dataset.filter || '*';
                var visible = 0;
                caseCards.forEach(function (card) {
                    var match = (f === '*') || (card.dataset.svc === f);
                    card.style.display = match ? '' : 'none';
                    if (match) { visible++; }
                });
                if (casesEmpty) { casesEmpty.hidden = visible > 0; }
            });
        });
    }

    /* ---------- Review form: rich text editor + character counter ---------- */
    (function () {
        var rte = doc.querySelector('.rsw-rte');
        if (!rte) { return; }
        var area    = rte.querySelector('.rsw-rte-area');
        var hidden  = rte.querySelector('.rsw-rte-hidden');
        var bar     = rte.querySelector('.rsw-rte-bar');
        var counter = doc.querySelector('.rsw-counter');
        var max     = parseInt(rte.getAttribute('data-max'), 10) || 1500;

        // Sync hidden textarea with editor HTML and update counter (counts plain text).
        function sync() {
            hidden.value = area.innerHTML.trim() === '<br>' ? '' : area.innerHTML;
            if (!counter) { return; }
            var plain = (area.innerText || '').replace(/\s+/g, ' ').trim();
            counter.textContent = plain.length + ' / ' + max;
            counter.classList.toggle('is-near', plain.length > max * 0.9);
            // Required state for native form validation.
            if (plain.length === 0) {
                hidden.setCustomValidity('Συμπληρώστε την εμπειρία σας.');
            } else {
                hidden.setCustomValidity('');
            }
        }
        area.addEventListener('input', sync);
        area.addEventListener('blur', sync);

        // Toolbar.
        bar.addEventListener('mousedown', function (e) {
            var btn = e.target.closest('button[data-cmd]');
            if (!btn) { return; }
            e.preventDefault();
            var cmd = btn.getAttribute('data-cmd');
            area.focus();
            if (cmd === 'createLink') {
                var url = win.prompt('URL συνδέσμου:', 'https://');
                if (url) { doc.execCommand('createLink', false, url); }
            } else {
                doc.execCommand(cmd, false, null);
            }
            sync();
        });

        // Strip pasted styling — keep plain text and basic structure.
        area.addEventListener('paste', function (e) {
            e.preventDefault();
            var text = (e.clipboardData || win.clipboardData).getData('text/plain');
            doc.execCommand('insertText', false, text);
        });

        // Hard cap by plain-text length.
        area.addEventListener('keydown', function (e) {
            if (e.metaKey || e.ctrlKey || e.altKey) { return; }
            var plain = (area.innerText || '').replace(/\s+/g, ' ').trim();
            if (plain.length >= max && e.key.length === 1) {
                e.preventDefault();
            }
        });

        // Ensure links have safe attrs on submit.
        var form = rte.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                area.querySelectorAll('a[href]').forEach(function (a) {
                    a.setAttribute('rel', 'nofollow noopener ugc');
                    a.setAttribute('target', '_blank');
                });
                sync();
            });
        }

        sync();
    })();

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

    /* ---------- FAQ search + filter ---------- */
    var faqSearch  = doc.getElementById('faq-search');
    var faqClear   = doc.getElementById('faq-clear');
    var faqEmpty   = doc.getElementById('faq-empty');
    var faqContent = doc.getElementById('faq-content');
    var faqPills   = doc.querySelectorAll('.faq-pill');

    if (faqSearch && faqContent) {
        var allItems  = faqContent.querySelectorAll('.faq-item');
        var allBlocks = faqContent.querySelectorAll('.faq-block');
        var activeCat = '*';

        var applyFilters = function () {
            var q = (faqSearch.value || '').trim().toLowerCase();
            faqClear.style.display = q ? '' : 'none';

            var visibleByBlock = {};
            allItems.forEach(function (item) {
                var hay = item.dataset.q || '';
                var blockEl = item.closest('.faq-block');
                var cat = blockEl ? blockEl.dataset.cat : '';
                var matchCat = (activeCat === '*' || cat === activeCat);
                var matchQ = !q || hay.indexOf(q) !== -1;
                var visible = matchCat && matchQ;
                item.style.display = visible ? '' : 'none';
                if (visible) {
                    visibleByBlock[cat] = (visibleByBlock[cat] || 0) + 1;
                }
            });

            var anyVisible = false;
            allBlocks.forEach(function (block) {
                var cat = block.dataset.cat;
                var count = visibleByBlock[cat] || 0;
                block.style.display = count > 0 ? '' : 'none';
                if (count > 0) { anyVisible = true; }
            });

            if (faqEmpty) { faqEmpty.hidden = anyVisible; }
        };

        faqSearch.addEventListener('input', applyFilters);
        if (faqClear) {
            faqClear.style.display = 'none';
            faqClear.addEventListener('click', function () {
                faqSearch.value = '';
                applyFilters();
                faqSearch.focus();
            });
        }
        faqPills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                faqPills.forEach(function (p) { p.classList.remove('is-active'); });
                pill.classList.add('is-active');
                activeCat = pill.dataset.cat || '*';
                applyFilters();
            });
        });
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
