import axios from 'axios';
import { showToast } from './toast.js';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

function initCookieBar() {
    const bar = document.getElementById('cookie-bar');

    if (!bar || localStorage.getItem('smoovehub_cookies') === 'accepted') {
        bar?.remove();
        return;
    }

    bar.querySelectorAll('[data-cookie-action]').forEach((button) => {
        button.addEventListener('click', () => {
            localStorage.setItem('smoovehub_cookies', 'accepted');
            bar.remove();
        });
    });
}

function initNewsletterForm() {
    const form = document.getElementById('newsletter-form');
    const success = document.getElementById('newsletter-success');
    const submitButton = document.getElementById('newsletter-submit');

    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const emailInput = form.querySelector('input[name="email"]');
        const email = emailInput?.value?.trim() ?? '';

        if (!email) {
            showToast('Email required', 'Please enter your email address.', 'warn');
            return;
        }

        submitButton?.setAttribute('disabled', 'disabled');
        submitButton?.classList.add('opacity-60');

        try {
            const response = await axios.post('/api/v1/landing/newsletter', { email });

            if (response.data?.status) {
                success?.classList.remove('hidden');
                form.reset();
                showToast(
                    'Subscribed!',
                    response.data.message ?? `Deal alerts will be sent to ${email}`,
                    'success',
                );
            }
        } catch (error) {
            const message = error.response?.data?.message
                ?? error.response?.data?.errors?.email?.[0]
                ?? 'Unable to subscribe right now. Please try again.';

            showToast('Subscription failed', message, 'error');
        } finally {
            submitButton?.removeAttribute('disabled');
            submitButton?.classList.remove('opacity-60');
        }
    });
}

function initSearchShortcut() {
    const searchInput = document.getElementById('nav-search');

    searchInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const target = searchInput.getAttribute('data-search-target') ?? '/shop';
            window.location.href = target;
        }
    });
}

function initMobileMenu() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');

    if (!toggle || !menu) {
        return;
    }

    const closeMenu = () => {
        menu.classList.remove('sh-mobile-menu-open');
        toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', () => {
        const isOpen = menu.classList.toggle('sh-mobile-menu-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    menu.querySelectorAll('a, button[data-modal-open]').forEach((element) => {
        element.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            closeMenu();
        }
    });
}

function initNavPortalsDropdown() {
    const toggle = document.getElementById('nav-portals-toggle');
    const menu = document.getElementById('nav-portals-menu');

    if (!toggle || !menu) {
        return;
    }

    const closeMenu = () => {
        menu.classList.remove('sh-nav-dropdown-open');
        toggle.setAttribute('aria-expanded', 'false');
    };

    const openMenu = () => {
        menu.classList.add('sh-nav-dropdown-open');
        toggle.setAttribute('aria-expanded', 'true');
    };

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        if (menu.classList.contains('sh-nav-dropdown-open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    menu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('click', (event) => {
        if (!toggle.contains(event.target) && !menu.contains(event.target)) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initCookieBar();
    initNewsletterForm();
    initSearchShortcut();
    initMobileMenu();
    initNavPortalsDropdown();
});
