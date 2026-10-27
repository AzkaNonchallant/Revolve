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

    const siteHeader = document.querySelector('[data-site-header]');
    const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    const megaMenu = document.querySelector('[data-mega-menu]');
    const menuTriggers = [...document.querySelectorAll('[data-mega-trigger]')];
    const menuTitle = document.querySelector('[data-mega-menu-title]');
    const featuredHeading = document.querySelector('[data-featured-heading]');
    const allProductsLink = document.querySelector('[data-mega-all-link]');
    const featuredFirstLink = document.querySelector('[data-featured-first]');
    const trendingLink = document.querySelector('[data-trending-link]');
    const saleLink = document.querySelector('[data-sale-link]');
    const productsUrl = document.querySelector('[data-products-url]')?.dataset.productsUrl;
    let closeMenuTimer;
    let currentMenuKey = null;

    const closeMegaMenu = ({ restoreFocus = false } = {}) => {
        window.clearTimeout(closeMenuTimer);
        megaMenu?.classList.remove('is-open');
        megaMenu?.setAttribute('aria-hidden', 'true');

        if (megaMenu) {
            megaMenu.inert = true;
        }

        menuTriggers.forEach((trigger) => {
            trigger.setAttribute('aria-expanded', 'false');
        });

        if (restoreFocus && currentMenuKey) {
            menuTriggers.find((trigger) => trigger.dataset.megaTrigger === currentMenuKey)?.focus();
        }

        currentMenuKey = null;
    };

    const openMegaMenu = (key) => {
        if (!megaMenu || !productsUrl) {
            return;
        }

        window.clearTimeout(closeMenuTimer);
        const trigger = menuTriggers.find((item) => item.dataset.megaTrigger === key);

        if (!trigger) {
            return;
        }

        currentMenuKey = key;
        menuTitle.textContent = `Jelajahi ${trigger.textContent.trim()}`;
        featuredHeading.textContent = key === 'sale' ? 'DISKON' : key === 'trending' ? 'TERBARU & TERLARIS' : 'KOLEKSI';
        featuredFirstLink.textContent = key === 'sale' ? 'Diskon terbesar' : 'Koleksi terbaru';
        trendingLink.hidden = key === 'sale';
        saleLink.textContent = key === 'sale' ? 'Semua produk diskon' : 'Produk diskon';
        trigger.setAttribute('aria-expanded', 'true');
        menuTriggers.filter((item) => item !== trigger).forEach((item) => item.setAttribute('aria-expanded', 'false'));
        megaMenu.classList.add('is-open');
        megaMenu.setAttribute('aria-hidden', 'false');
        megaMenu.inert = false;

        const allProductsUrl = new URL(productsUrl, window.location.origin);
        if (key === 'sale') {
            allProductsUrl.searchParams.set('sort', 'discount');
        }
        allProductsLink.href = allProductsUrl.toString();
        featuredFirstLink.href = allProductsUrl.toString();
        saleLink.href = new URL(`${productsUrl}?sort=discount`, window.location.origin).toString();

        document.querySelectorAll('[data-category-id]').forEach((link) => {
            const categoryUrl = new URL(productsUrl, window.location.origin);
            categoryUrl.searchParams.set('category', link.dataset.categoryId);

            if (key === 'sale') {
                categoryUrl.searchParams.set('sort', 'discount');
            } else if (key === 'trending') {
                categoryUrl.searchParams.set('sort', 'terlaris');
            }

            link.href = categoryUrl.toString();
        });
    };

    if (megaMenu && menuTriggers.length > 0) {
        menuTriggers.forEach((trigger, index) => {
            trigger.addEventListener('mouseenter', () => {
                if (window.matchMedia('(min-width: 1200px)').matches) {
                    openMegaMenu(trigger.dataset.megaTrigger);
                }
            });
            trigger.addEventListener('focus', () => openMegaMenu(trigger.dataset.megaTrigger));
            trigger.addEventListener('click', () => {
                openMegaMenu(trigger.dataset.megaTrigger);
            });
            trigger.addEventListener('keydown', (event) => {
                if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) {
                    return;
                }

                event.preventDefault();
                const targetIndex = event.key === 'Home' ? 0 : event.key === 'End' ? menuTriggers.length - 1 : (index + (event.key === 'ArrowRight' ? 1 : -1) + menuTriggers.length) % menuTriggers.length;
                menuTriggers[targetIndex].focus();
            });
        });

        siteHeader?.addEventListener('mouseleave', () => {
            if (window.matchMedia('(min-width: 1200px)').matches) {
                closeMenuTimer = window.setTimeout(() => closeMegaMenu(), 150);
            }
        });

        siteHeader?.addEventListener('mouseenter', () => window.clearTimeout(closeMenuTimer));

        siteHeader?.addEventListener('focusout', () => {
            window.setTimeout(() => {
                const focusRemainsInMenu = document.activeElement.closest('.primary-navigation, .mega-menu');

                if (!focusRemainsInMenu) {
                    closeMegaMenu();
                }
            }, 0);
        });
    }

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', () => {
            const isOpen = mobileMenu.hasAttribute('hidden');
            mobileMenu.toggleAttribute('hidden', !isOpen);
            mobileMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            if (isOpen) {
                closeMegaMenu();
            }
        });

        mobileMenu.querySelectorAll('[data-mobile-navigation-group]').forEach((group) => {
            group.addEventListener('toggle', () => {
                if (group.open) {
                    mobileMenu.querySelectorAll('[data-mobile-navigation-group]').forEach((otherGroup) => {
                        if (otherGroup !== group) {
                            otherGroup.open = false;
                        }
                    });
                }
            });
        });
    }

    document.addEventListener('click', (event) => {
        if (siteHeader && !siteHeader.contains(event.target)) {
            closeMegaMenu();
            mobileMenu?.setAttribute('hidden', '');
            mobileMenuToggle?.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            const mobileWasOpen = mobileMenu && !mobileMenu.hasAttribute('hidden');
            const desktopWasOpen = megaMenu?.classList.contains('is-open');

            closeMegaMenu({ restoreFocus: desktopWasOpen });
            mobileMenu?.setAttribute('hidden', '');
            mobileMenuToggle?.setAttribute('aria-expanded', 'false');

            if (mobileWasOpen) {
                mobileMenuToggle?.focus();
            }
        }
    });

    const filterShell = document.querySelector('[data-filter-shell]');
    const filterDrawer = document.querySelector('[data-filter-drawer]');
    const filterOpen = document.querySelector('[data-filter-open]');
    const filterCloseButtons = document.querySelectorAll('[data-filter-close]');
    const filterForm = document.querySelector('#catalog-filter-form');
    let filterReturnFocus;

    if (filterShell && filterDrawer && filterOpen) {
        const closeFilterDrawer = ({ restoreFocus = true } = {}) => {
            filterShell.classList.remove('is-open');
            filterDrawer.setAttribute('aria-hidden', 'true');
            filterDrawer.inert = true;
            document.body.classList.remove('filter-drawer-open');

            if (restoreFocus) {
                (filterReturnFocus || filterOpen).focus();
            }
        };

        filterOpen.addEventListener('click', () => {
            filterReturnFocus = filterOpen;
            filterShell.classList.add('is-open');
            filterDrawer.setAttribute('aria-hidden', 'false');
            filterDrawer.inert = false;
            document.body.classList.add('filter-drawer-open');
            filterDrawer.querySelector('[data-filter-close]')?.focus();
        });

        filterCloseButtons.forEach((button) => {
            button.addEventListener('click', () => closeFilterDrawer());
        });

        filterDrawer.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                closeFilterDrawer();
                return;
            }

            if (event.key !== 'Tab') {
                return;
            }

            const focusableElements = [...filterDrawer.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), summary')]
                .filter((element) => element.getClientRects().length > 0);
            const firstElement = focusableElements[0];
            const lastElement = focusableElements.at(-1);

            if (event.shiftKey && document.activeElement === firstElement) {
                event.preventDefault();
                lastElement?.focus();
            } else if (!event.shiftKey && document.activeElement === lastElement) {
                event.preventDefault();
                firstElement?.focus();
            }
        });

        filterForm?.addEventListener('click', (event) => {
            const sizeButton = event.target.closest('[data-size-toggle]');
            if (sizeButton) {
                const sizeInput = filterForm.querySelector('[data-filter-size-value]');
                const isSelected = sizeInput.value === sizeButton.dataset.value;
                sizeInput.value = isSelected ? '' : sizeButton.dataset.value;
                filterForm.querySelectorAll('[data-size-toggle]').forEach((button) => {
                    const selected = button === sizeButton && !isSelected;
                    button.classList.toggle('is-selected', selected);
                    button.setAttribute('aria-pressed', selected ? 'true' : 'false');
                });
                return;
            }

            const filterTag = event.target.closest('[data-remove-filter]');
            if (!filterTag) {
                return;
            }

            const filterName = filterTag.dataset.removeFilter;
            const filterValue = filterTag.dataset.filterValue;

            if (filterName === 'price') {
                filterForm.querySelectorAll('[name="min_price"], [name="max_price"]').forEach((input) => input.value = '');
            } else if (filterName === 'brand[]') {
                const brandInput = [...filterForm.querySelectorAll('[name="brand[]"]')].find((input) => input.value === filterValue);
                if (brandInput) brandInput.checked = false;
            } else if (filterName === 'size') {
                filterForm.querySelector('[data-filter-size-value]').value = '';
            } else {
                filterForm.querySelectorAll(`[name="${CSS.escape(filterName)}"]`).forEach((input) => {
                    if (!filterValue || input.value === filterValue) input.checked = false;
                    if (input.type === 'hidden') input.value = '';
                });
            }

            filterForm.requestSubmit();
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
            slides[currentSlide].setAttribute('aria-hidden', 'true');
            dots[currentSlide]?.querySelector('span')?.classList.remove('is-active');
            dots[currentSlide]?.setAttribute('aria-pressed', 'false');
            currentSlide = (index + slides.length) % slides.length;
            slides[currentSlide].classList.remove('hidden');
            slides[currentSlide].setAttribute('aria-hidden', 'false');
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
