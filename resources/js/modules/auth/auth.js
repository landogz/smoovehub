import axios from 'axios';
import { closeModal, initModalSystem, openModal, switchModal } from '../../shared/modal.js';
import { showToast } from '../landing/toast.js';

const AUTH_TOKEN_KEY = 'smoovehub_auth_token';
const AUTH_FINGERPRINT_KEY = 'smoovehub_device_fp';
const PENDING_CHECKOUT_KEY = 'smoovehub_pending_checkout';
const PENDING_CART_KEY = 'smoovehub_pending_cart';

let currentUser = null;

function getDeviceFingerprint() {
    let fingerprint = localStorage.getItem(AUTH_FINGERPRINT_KEY);

    if (!fingerprint) {
        fingerprint = crypto.randomUUID();
        localStorage.setItem(AUTH_FINGERPRINT_KEY, fingerprint);
    }

    return fingerprint;
}

function getDeviceMeta() {
    const platform = navigator.platform || null;
    const browser = navigator.userAgent?.slice(0, 80) || null;

    return {
        device_name: platform ? `${platform} Browser` : 'Web Browser',
        device_fingerprint: getDeviceFingerprint(),
        platform,
        browser,
    };
}

function setAuthToken(token) {
    localStorage.setItem(AUTH_TOKEN_KEY, token);
    axios.defaults.headers.common.Authorization = `Bearer ${token}`;
}

function clearAuthToken() {
    localStorage.removeItem(AUTH_TOKEN_KEY);
    delete axios.defaults.headers.common.Authorization;
}

function restoreAuthToken() {
    const token = localStorage.getItem(AUTH_TOKEN_KEY);

    if (token) {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    }
}

function setFieldErrors(form, errors = {}) {
    form.querySelectorAll('[data-field-error]').forEach((element) => {
        element.textContent = '';
        element.classList.add('hidden');
    });

    form.querySelectorAll('.sh-form-input-error').forEach((input) => {
        input.classList.remove('sh-form-input-error');
    });

    Object.entries(errors).forEach(([field, messages]) => {
        const errorEl = form.querySelector(`[data-field-error="${field}"]`);
        const input = form.querySelector(`[name="${field}"]`);

        if (errorEl) {
            errorEl.textContent = Array.isArray(messages) ? messages[0] : String(messages);
            errorEl.classList.remove('hidden');
        }

        input?.classList.add('sh-form-input-error');
    });
}

function setFormAlert(form, message = '') {
    const alert = form.querySelector('[data-form-alert]');

    if (!alert) {
        return;
    }

    if (!message) {
        alert.classList.add('hidden');
        alert.textContent = '';
        return;
    }

    alert.textContent = message;
    alert.classList.remove('hidden');
}

function setSubmitLoading(form, loading) {
    const button = form.querySelector('[type="submit"]');

    if (!button) {
        return;
    }

    button.toggleAttribute('disabled', loading);
    button.classList.toggle('opacity-60', loading);
}

function getRedirectPath(user) {
    const role = user?.primary_role ?? user?.roles?.[0];

    if (role === 'seller') {
        return '/dashboard';
    }

    if (role === 'admin' || role === 'super-admin') {
        return '/admin';
    }

    if (role === 'distribution-partner') {
        return '/distribute';
    }

    return '/account';
}

function userHasRole(user, role) {
    const roles = Array.isArray(user?.roles) ? user.roles : [];

    return user?.primary_role === role || roles.includes(role);
}

function displayRoleLabel(user) {
    const role = user?.primary_role ?? 'customer';

    if (role === 'customer') {
        return 'Member';
    }

    return role.replace(/-/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
}

function displayNavName(user) {
    if (userHasRole(user, 'seller') && user.seller_profile?.store_name) {
        return user.seller_profile.store_name;
    }

    return user.username || user.name || 'Member';
}

function renderNavAvatar(avatar, user) {
    if (!avatar) {
        return;
    }

    const initials = user?.avatar_initials ?? 'U';

    if (user?.avatar_url) {
        avatar.innerHTML = `<img src="${user.avatar_url}" alt="" class="h-full w-full object-cover">`;
        return;
    }

    avatar.textContent = initials;
}

function updateNavbar(user) {
    const guest = document.getElementById('nav-guest');
    const userNav = document.getElementById('nav-user');
    const mobileGuest = document.getElementById('mobile-nav-guest');
    const mobileUser = document.getElementById('mobile-nav-user');
    const avatar = document.getElementById('nav-user-avatar');
    const name = document.getElementById('nav-user-name');
    const role = document.getElementById('nav-user-role');

    const isLoggedIn = Boolean(user);

    guest?.classList.toggle('hidden', isLoggedIn);
    guest?.classList.toggle('lg:flex', !isLoggedIn);
    guest?.classList.toggle('max-lg:hidden', !isLoggedIn);
    userNav?.classList.toggle('hidden', !isLoggedIn);
    userNav?.classList.toggle('flex', isLoggedIn);

    mobileGuest?.classList.toggle('hidden', isLoggedIn);
    mobileUser?.classList.toggle('hidden', !isLoggedIn);
    mobileUser?.classList.toggle('flex', isLoggedIn);

    if (user && avatar) {
        renderNavAvatar(avatar, user);
    } else if (avatar) {
        avatar.textContent = 'U';
    }

    if (user && name) {
        name.textContent = displayNavName(user);
    }

    if (user && role) {
        role.textContent = displayRoleLabel(user);
    }

    const isMember = userHasRole(user, 'customer');
    const isSeller = userHasRole(user, 'seller');
    toggleNavMenuLink('nav-link-account', isMember);
    toggleNavMenuLink('nav-link-seller', isSeller);
    document.getElementById('nav-link-account-mobile')?.classList.toggle('hidden', !isMember);
    document.getElementById('nav-link-seller-mobile')?.classList.toggle('hidden', !isSeller);

    document.getElementById('nav-notifications')?.classList.toggle('hidden', !isLoggedIn);
}

async function fetchCurrentUser() {
    const response = await axios.get('/api/v1/auth/me');
    currentUser = response.data?.data?.user ?? null;
    updateNavbar(currentUser);
    window.dispatchEvent(new CustomEvent('smoovehub:auth-updated', { detail: currentUser }));

    return currentUser;
}

async function restoreSession() {
    if (!localStorage.getItem(AUTH_TOKEN_KEY)) {
        updateNavbar(null);
        return;
    }

    try {
        await fetchCurrentUser();
    } catch {
        clearAuthToken();
        currentUser = null;
        updateNavbar(null);
    }
}

async function handleAuthSuccess(response, form, modalId, successTitle) {
    const payload = response.data?.data;
    const user = payload?.user;

    if (payload?.token) {
        setAuthToken(payload.token);
    }

    currentUser = user;
    updateNavbar(user);
    form.reset();
    setFieldErrors(form);
    setFormAlert(form);
    closeModal(modalId);

    showToast(
        successTitle,
        response.data?.message ?? `Welcome, ${user?.name ?? 'back'}!`,
        'success',
    );

    window.dispatchEvent(new CustomEvent('smoovehub:auth-updated', { detail: user }));

    const pendingCheckout = sessionStorage.getItem(PENDING_CHECKOUT_KEY) === '1';
    const pendingCart = sessionStorage.getItem(PENDING_CART_KEY) === '1';

    if (pendingCheckout) {
        sessionStorage.removeItem(PENDING_CHECKOUT_KEY);

        if (!userHasRole(user, 'customer')) {
            showToast('Member account required', 'Pay Now is for shopper accounts. Sign in as a member to continue.', 'error');
            return;
        }

        window.dispatchEvent(new CustomEvent('smoovehub:checkout-auth'));
        return;
    }

    if (pendingCart) {
        sessionStorage.removeItem(PENDING_CART_KEY);
        window.location.href = '/account#purchases';
        return;
    }

    const path = getRedirectPath(user);

    if (path) {
        window.location.href = path;
    }
}

function extractErrorMessage(error) {
    return error.response?.data?.message
        ?? error.response?.data?.errors?.email?.[0]
        ?? error.response?.data?.errors?.password?.[0]
        ?? 'Unable to complete the request. Please try again.';
}

function initLoginForm() {
    const form = document.getElementById('login-form');

    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        setFormAlert(form);
        setFieldErrors(form);
        setSubmitLoading(form, true);

        const formData = new FormData(form);

        try {
            const response = await axios.post('/api/v1/auth/login', {
                email: formData.get('email'),
                password: formData.get('password'),
                ...getDeviceMeta(),
            });

            if (response.data?.status) {
                await handleAuthSuccess(response, form, 'login-modal', 'Signed in');
            }
        } catch (error) {
            const errors = error.response?.data?.errors ?? {};

            if (Object.keys(errors).length) {
                setFieldErrors(form, errors);
            }

            setFormAlert(form, extractErrorMessage(error));
            showToast('Sign in failed', extractErrorMessage(error), 'error');
        } finally {
            setSubmitLoading(form, false);
        }
    });
}

function initSignupForm() {
    const form = document.getElementById('signup-form');

    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        setFormAlert(form);
        setFieldErrors(form);
        setSubmitLoading(form, true);

        const formData = new FormData(form);

        try {
            const response = await axios.post('/api/v1/auth/register', {
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone') || null,
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation'),
                ...getDeviceMeta(),
            });

            if (response.data?.status) {
                await handleAuthSuccess(response, form, 'signup-modal', 'Account created');
            }
        } catch (error) {
            const errors = error.response?.data?.errors ?? {};

            if (Object.keys(errors).length) {
                setFieldErrors(form, errors);
            }

            setFormAlert(form, extractErrorMessage(error));
            showToast('Sign up failed', extractErrorMessage(error), 'error');
        } finally {
            setSubmitLoading(form, false);
        }
    });
}

function initLogoutActions() {
    document.querySelectorAll('[data-auth-logout]').forEach((button) => {
        button.addEventListener('click', async () => {
            try {
                if (currentUser) {
                    await axios.post('/api/v1/auth/logout');
                }
            } catch {
                // Clear local session even if API fails.
            } finally {
                clearAuthToken();
                currentUser = null;
                updateNavbar(null);
                showToast('Signed out', 'See you next time!', 'success');

                if (window.location.pathname.startsWith('/dashboard')) {
                    window.location.replace('/');
                }
            }
        });
    });

    document.querySelectorAll('[data-auth-logout-other-devices]').forEach((button) => {
        button.addEventListener('click', async () => {
            try {
                const response = await axios.post('/api/v1/auth/logout-other-devices');
                const count = response.data?.data?.revoked_count ?? 0;

                showToast(
                    'Other devices signed out',
                    count > 0 ? `${count} session(s) revoked.` : 'No other active sessions found.',
                    'success',
                );
            } catch (error) {
                showToast('Action failed', extractErrorMessage(error), 'error');
            }
        });
    });
}

function initModalLinks() {
    document.querySelectorAll('[data-switch-modal]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const from = trigger.getAttribute('data-switch-from');
            const to = trigger.getAttribute('data-switch-modal');

            if (from && to) {
                switchModal(from, to);
            }
        });
    });
}

function toggleNavMenuLink(id, visible) {
    const link = document.getElementById(id);

    if (!link) {
        return;
    }

    link.classList.toggle('hidden', !visible);
    link.classList.toggle('block', visible);
}

function initCartLink() {
    document.querySelectorAll('[data-cart-link]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (userHasRole(currentUser, 'customer')) {
                return;
            }

            event.preventDefault();
            sessionStorage.setItem(PENDING_CART_KEY, '1');
            showToast('Member login required', 'Sign in to view your cart and purchases.', 'warn');
            openAuthModal('login');
        });
    });
}

function initUserMenu() {
    const toggle = document.getElementById('nav-user-toggle');
    const menu = document.getElementById('nav-user-menu');

    if (!toggle || !menu) {
        return;
    }

    toggle.addEventListener('click', () => {
        menu.classList.toggle('hidden');
        toggle.setAttribute(
            'aria-expanded',
            menu.classList.contains('hidden') ? 'false' : 'true',
        );
    });

    document.addEventListener('click', (event) => {
        if (!menu.contains(event.target) && !toggle.contains(event.target)) {
            menu.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}

export function initAuth() {
    restoreAuthToken();
    initModalSystem();
    initLoginForm();
    initSignupForm();
    initLogoutActions();
    initModalLinks();
    initUserMenu();
    initCartLink();
    restoreSession();
}

export async function refreshCurrentUser() {
    try {
        return await fetchCurrentUser();
    } catch {
        return getCurrentUser();
    }
}

export function getCurrentUser() {
    return currentUser;
}

export function setCurrentUser(user) {
    currentUser = user;
    updateNavbar(user);
}

export function openAuthModal(type = 'login') {
    openModal(type === 'signup' ? 'signup-modal' : 'login-modal');
}

export function userIsMember(user = currentUser) {
    return userHasRole(user, 'customer');
}

export function rememberPendingCheckout() {
    sessionStorage.setItem(PENDING_CHECKOUT_KEY, '1');
}
