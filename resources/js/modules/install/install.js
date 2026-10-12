import axios from 'axios';
import { showToast } from '../landing/toast.js';

function readBootstrap() {
    const el = document.getElementById('install-bootstrap');

    if (!el) {
        return null;
    }

    try {
        return JSON.parse(el.textContent || '{}');
    } catch {
        return null;
    }
}

function renderRequirements(checks = [], ready = false) {
    const list = document.getElementById('requirements-list');
    const badge = document.getElementById('requirements-badge');

    if (!list || !badge) {
        return;
    }

    list.innerHTML = checks
        .map((check) => {
            const color = check.passed
                ? 'border-sh-green/30 bg-sh-green-bg text-sh-green'
                : 'border-red-200 bg-red-50 text-red-700';
            const mark = check.passed ? 'OK' : 'Fail';

            return `
                <li class="flex items-start justify-between gap-3 rounded-xl border px-3.5 py-2.5 text-sm ${color}">
                    <div>
                        <div class="font-semibold">${check.label}</div>
                        <div class="mt-0.5 text-xs opacity-80">${check.detail}</div>
                    </div>
                    <span class="shrink-0 text-xs font-bold uppercase tracking-wide">${mark}</span>
                </li>
            `;
        })
        .join('');

    badge.textContent = ready ? 'Ready' : 'Action needed';
    badge.className = ready
        ? 'rounded-full bg-sh-green-bg px-3 py-1 text-xs font-semibold text-sh-green'
        : 'rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700';
}

function setInstalling(isInstalling) {
    const form = document.getElementById('install-form');
    const submit = document.getElementById('install-submit');
    const progress = document.getElementById('install-progress');

    if (submit) {
        submit.disabled = isInstalling;
        submit.textContent = isInstalling ? 'Installing…' : 'Install SmooveHub';
    }

    if (progress) {
        progress.classList.toggle('hidden', !isInstalling);
    }

    if (form) {
        form.querySelectorAll('input, button').forEach((el) => {
            if (el.id === 'install-submit') {
                return;
            }
            el.disabled = isInstalling;
        });
    }
}

function firstValidationError(errors = {}) {
    const values = Object.values(errors);

    if (!values.length) {
        return null;
    }

    const first = values[0];

    return Array.isArray(first) ? first[0] : String(first);
}

async function loadStatus(fallback) {
    try {
        const response = await axios.get('/api/v1/install/status');
        const data = response.data?.data ?? {};

        renderRequirements(data.checks ?? [], Boolean(data.ready));

        return data;
    } catch {
        if (fallback?.status) {
            renderRequirements(fallback.status.checks ?? [], Boolean(fallback.status.ready));
            return fallback.status;
        }

        showToast('Could not load status', 'Refresh the page and try again.', 'error');
        return null;
    }
}

async function submitInstall(event) {
    event.preventDefault();

    const form = event.currentTarget;
    const formData = new FormData(form);

    const payload = {
        app_url: String(formData.get('app_url') || '').trim(),
        db_host: String(formData.get('db_host') || '').trim(),
        db_port: Number(formData.get('db_port') || 3306),
        db_database: String(formData.get('db_database') || '').trim(),
        db_username: String(formData.get('db_username') || '').trim(),
        db_password: String(formData.get('db_password') || ''),
        admin_name: String(formData.get('admin_name') || '').trim(),
        admin_email: String(formData.get('admin_email') || '').trim(),
        admin_password: String(formData.get('admin_password') || ''),
        admin_password_confirmation: String(formData.get('admin_password_confirmation') || ''),
    };

    if (payload.admin_password !== payload.admin_password_confirmation) {
        showToast('Passwords do not match', 'Confirm your admin password and try again.', 'error');
        return;
    }

    setInstalling(true);

    try {
        const response = await axios.post('/api/v1/install', payload);
        const data = response.data?.data ?? {};

        showToast('Install complete', response.data?.message || 'SmooveHub is ready.', 'success');

        window.setTimeout(() => {
            window.location.href = data.redirect || '/admin';
        }, 900);
    } catch (error) {
        const message =
            firstValidationError(error.response?.data?.errors) ||
            error.response?.data?.message ||
            'Installation failed. Check your database settings.';

        showToast('Install failed', message, 'error');
        setInstalling(false);
        await loadStatus(readBootstrap());
    }
}

export function initInstall() {
    const bootstrap = readBootstrap();
    const form = document.getElementById('install-form');

    if (!form) {
        return;
    }

    if (bootstrap?.status) {
        renderRequirements(bootstrap.status.checks ?? [], Boolean(bootstrap.status.ready));
    }

    loadStatus(bootstrap);
    form.addEventListener('submit', submitInstall);
}
