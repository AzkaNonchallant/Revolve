document.addEventListener('DOMContentLoaded', () => {
    const userMenu = document.querySelector('[data-user-menu]');
    const toggle = document.querySelector('[data-user-menu-toggle]');
    const panel = document.querySelector('[data-user-menu-panel]');

    if (userMenu && toggle && panel) {
        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const isHidden = panel.classList.toggle('hidden');
            toggle.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
        });

        document.addEventListener('click', (event) => {
            if (!userMenu.contains(event.target)) {
                panel.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !panel.classList.contains('hidden')) {
                panel.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.focus();
            }
        });
    }

    const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', () => {
            const isHidden = mobileMenu.classList.toggle('hidden');
            mobileMenuToggle.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
        });

        mobileMenu.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                mobileMenu.classList.add('hidden');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
                mobileMenuToggle.focus();
            }
        });
    }

    document.querySelectorAll('[data-hero-slider]').forEach((slider) => {
        const slides = [...slider.querySelectorAll('[data-hero-slide]')];
        const dots = [...slider.querySelectorAll('[data-hero-dot]')];
        const previous = slider.querySelector('[data-hero-prev]');
        const next = slider.querySelector('[data-hero-next]');

        if (slides.length < 2) {
            return;
        }

        let currentSlide = 0;

        const showSlide = (index) => {
            slides[currentSlide].classList.add('hidden');
            dots[currentSlide]?.querySelector('span')?.classList.remove('is-active');
            dots[currentSlide]?.setAttribute('aria-pressed', 'false');
            currentSlide = (index + slides.length) % slides.length;
            slides[currentSlide].classList.remove('hidden');
            dots[currentSlide]?.querySelector('span')?.classList.add('is-active');
            dots[currentSlide]?.setAttribute('aria-pressed', 'true');
        };

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => showSlide(index));
        });
        previous?.addEventListener('click', () => showSlide(currentSlide - 1));
        next?.addEventListener('click', () => showSlide(currentSlide + 1));
        slider.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') {
                showSlide(currentSlide - 1);
            } else if (event.key === 'ArrowRight') {
                showSlide(currentSlide + 1);
            }
        });
    });

    const sidebarToggle = document.querySelector('[data-dashboard-sidebar-toggle]');
    const sidebar = document.querySelector('[data-dashboard-sidebar]');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-open');
        });
    }
});
