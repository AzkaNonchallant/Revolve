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
    }

    const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
});