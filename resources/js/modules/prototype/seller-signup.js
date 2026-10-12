import axios from 'axios';
import { closeModal, openModal, switchModal } from '../../shared/modal.js';
import { showToast } from '../landing/toast.js';
import { getCurrentUser, setCurrentUser } from '../auth/auth.js';

let ssStep = 0;

function showStepError(id, message) {
    const el = document.getElementById(id);

    if (!el) {
        return;
    }

    el.textContent = message;
    el.classList.remove('hidden');
}

function hideStepErrors() {
    ['ss-err0', 'ss-err1'].forEach((id) => {
        document.getElementById(id)?.classList.add('hidden');
    });
}

function renderSSStep() {
    ['ss-s0', 'ss-s1', 'ss-s2'].forEach((id, index) => {
        document.getElementById(id)?.classList.toggle('hidden', index !== ssStep);
    });

    ['ss-t0', 'ss-t1', 'ss-t2'].forEach((id, index) => {
        const tab = document.getElementById(id);

        if (!tab) {
            return;
        }

        tab.classList.toggle('sh-step-tab-active', index === ssStep);
        tab.classList.toggle('sh-step-tab-done', index < ssStep);
    });

    const prog = document.getElementById('ss-prog');

    if (prog) {
        prog.style.width = `${[33, 66, 100][ssStep]}%`;
    }
}

function getDeviceMeta() {
    const fingerprint = localStorage.getItem('smoovehub_device_fp') ?? crypto.randomUUID();

    return {
        device_name: navigator.platform ? `${navigator.platform} Browser` : 'Web Browser',
        device_fingerprint: fingerprint,
        platform: navigator.platform || null,
        browser: navigator.userAgent?.slice(0, 80) || null,
    };
}

async function submitSellerRegistration() {
    const payload = {
        first_name: document.getElementById('ss-fn')?.value.trim(),
        last_name: document.getElementById('ss-ln')?.value.trim(),
        email: document.getElementById('ss-email')?.value.trim(),
        phone: document.getElementById('ss-phone')?.value.trim(),
        password: document.getElementById('ss-pw')?.value,
        password_confirmation: document.getElementById('ss-pw2')?.value,
        store_name: document.getElementById('ss-store')?.value.trim(),
        business_type: document.getElementById('ss-btype')?.value,
        category: document.getElementById('ss-cat')?.value,
        description: document.getElementById('ss-desc')?.value.trim() || null,
        catalog_size: document.getElementById('ss-size')?.value || null,
        tax_id: document.getElementById('ss-ein')?.value.trim() || null,
        ...getDeviceMeta(),
    };

    const response = await axios.post('/api/v1/auth/seller-register', payload);
    const user = response.data?.data?.user;
    const token = response.data?.data?.token;

    if (token) {
        localStorage.setItem('smoovehub_auth_token', token);
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    }

    setCurrentUser(user);

    document.getElementById('ss-confirm-email').textContent = payload.email;

    const slug = user?.seller_profile?.store_slug ?? 'my-store';
    document.getElementById('ss-store-url-display').textContent = `${window.location.host}/store/${slug}`;

    ssStep = 2;
    renderSSStep();

    showToast('Seller account created', response.data?.message ?? 'Welcome to SmooveHub!', 'success');

    window.dispatchEvent(new CustomEvent('smoovehub:auth-updated', { detail: user }));
}

function validateStep0() {
    const fn = document.getElementById('ss-fn')?.value.trim();
    const ln = document.getElementById('ss-ln')?.value.trim();
    const email = document.getElementById('ss-email')?.value.trim();
    const phone = document.getElementById('ss-phone')?.value.trim();
    const pw = document.getElementById('ss-pw')?.value;
    const pw2 = document.getElementById('ss-pw2')?.value;

    if (!fn || !ln || !email || !phone || !pw) {
        showStepError('ss-err0', 'Please fill in all required fields.');
        return false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showStepError('ss-err0', 'Please enter a valid email address.');
        return false;
    }

    if (pw.length < 8) {
        showStepError('ss-err0', 'Password must be at least 8 characters.');
        return false;
    }

    if (pw !== pw2) {
        showStepError('ss-err0', 'Passwords do not match.');
        return false;
    }

    return true;
}

async function validateStep1() {
    const store = document.getElementById('ss-store')?.value.trim();
    const btype = document.getElementById('ss-btype')?.value;
    const cat = document.getElementById('ss-cat')?.value;
    const terms = document.getElementById('ss-terms')?.checked;

    if (!store || !btype || !cat) {
        showStepError('ss-err1', 'Please complete all required fields.');
        return false;
    }

    if (!terms) {
        showStepError('ss-err1', 'You must agree to the Seller Terms of Service.');
        return false;
    }

    try {
        await submitSellerRegistration();
        return true;
    } catch (error) {
        const message = error.response?.data?.message
            ?? error.response?.data?.errors?.email?.[0]
            ?? 'Unable to create seller account. Please try again.';
        showStepError('ss-err1', message);
        showToast('Registration failed', message, 'error');
        return false;
    }
}

export function openSellerSignupModal() {
    ssStep = 0;
    hideStepErrors();
    renderSSStep();
    openModal('seller-signup-modal');
}

export function initSellerSignup() {
    document.querySelectorAll('[data-seller-signup]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            switchModal('login-modal', 'seller-signup-modal');
        });
    });

    document.querySelectorAll('[data-modal-open="seller-signup-modal"]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            ssStep = 0;
            hideStepErrors();
            renderSSStep();
        });
    });

    document.querySelectorAll('[data-ss-next]').forEach((button) => {
        button.addEventListener('click', async () => {
            hideStepErrors();
            const step = Number(button.dataset.ssNext);

            if (step === 0 && validateStep0()) {
                ssStep = 1;
                renderSSStep();
            } else if (step === 1) {
                await validateStep1();
            }
        });
    });

    document.querySelector('[data-ss-prev]')?.addEventListener('click', () => {
        if (ssStep > 0) {
            ssStep -= 1;
            hideStepErrors();
            renderSSStep();
        }
    });

    document.querySelector('[data-ss-go-dashboard]')?.addEventListener('click', () => {
        closeModal('seller-signup-modal');
        window.location.href = '/dashboard';
    });
}
