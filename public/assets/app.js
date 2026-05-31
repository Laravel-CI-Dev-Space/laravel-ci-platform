/* ============================================================
   app.js — JavaScript commun Laravel CI Platform
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ── Hamburger Menu ── */
    (function () {
        var btn  = document.getElementById('hamburger-btn');
        var menu = document.getElementById('mobile-menu');
        var l1   = document.getElementById('ham-line-1');
        var l2   = document.getElementById('ham-line-2');
        var l3   = document.getElementById('ham-line-3');

        if (!btn || !menu) return;

        var open = false;

        function setOpen(value) {
            open = value;
            btn.setAttribute('aria-expanded', open);
            menu.classList.toggle('open', open);
            if (l1) l1.style.transform = open ? 'translateY(7px) rotate(45deg)' : '';
            if (l2) l2.style.opacity   = open ? '0' : '1';
            if (l3) l3.style.transform = open ? 'translateY(-7px) rotate(-45deg)' : '';
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            setOpen(!open);
        });

        document.addEventListener('click', function (e) {
            if (open && !menu.contains(e.target) && !btn.contains(e.target)) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && open) {
                setOpen(false);
            }
        });
    })();

    /* ── Tabs Interactifs ── */
    (function () {
        var tabContainers = document.querySelectorAll('.tabs-scroll');
        tabContainers.forEach(function (container) {
            var tabs = container.querySelectorAll('button');
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    tabs.forEach(function (t) {
                        t.classList.remove('border-[#FF6600]', 'text-[#FF6600]');
                        t.classList.add('border-transparent', 'text-on-surface-variant');
                    });
                    tab.classList.remove('border-transparent', 'text-on-surface-variant');
                    tab.classList.add('border-[#FF6600]', 'text-[#FF6600]');
                });
            });
        });
    })();

    /* ── Scroll-to-top Button ── */
    (function () {
        var btn = document.getElementById('scroll-top-btn');
        if (!btn) return;

        window.addEventListener('scroll', function () {
            if (window.scrollY > 400) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        });

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    })();

});
