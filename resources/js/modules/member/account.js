import axios from 'axios';
import { getCurrentUser, openAuthModal, refreshCurrentUser } from '../auth/auth.js';
import { restorePortalUser } from '../auth/portal-auth.js';
import { showToast } from '../landing/toast.js';
import { formatMoney } from '../prototype/state.js';
import { closeModal, openModal } from '../../shared/modal.js';

const TABS = [
    'profile',
    'payments',
    'addresses',
    'password',
    'privacy',
    'notif-settings',
    'order-settings',
    'purchases',
    'notifications',
    'vouchers',
];

let purchaseTab = 'all';
let confirmAction = null;
let settingsTimer = null;

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    }[char]));
}

function apiError(error, fallback) {
    return error.response?.data?.message
        ?? Object.values(error.response?.data?.errors ?? {})[0]?.[0]
        ?? fallback;
}

function requireAuth(error) {
    if (error.response?.status === 401 || error.response?.status === 403) {
        showToast('Member sign-in required', 'Sign in with the account you use to shop.', 'warn');
        openAuthModal('login');
        return true;
    }

    return false;
}

function setAvatar(url, initials) {
    const nodes = [
        document.getElementById('member-sidebar-avatar'),
        document.getElementById('member-avatar-preview'),
    ];

    nodes.forEach((node) => {
        if (!node) {
            return;
        }

        if (url) {
            node.innerHTML = `<img src="${escapeHtml(url)}" alt="" class="h-full w-full object-cover">`;
            return;
        }

        node.textContent = initials || 'M';
    });
}

function showTab(tab) {
    const next = TABS.includes(tab) ? tab : 'profile';

    document.querySelectorAll('[data-member-panel]').forEach((panel) => {
        panel.classList.toggle('hidden', panel.getAttribute('data-member-panel') !== next);
    });

    document.querySelectorAll('[data-member-tab]').forEach((button) => {
        button.classList.toggle('is-active', button.getAttribute('data-member-tab') === next);
    });

    if (window.location.hash !== `#${next}`) {
        window.history.replaceState(null, '', `#${next}`);
    }

    loadTab(next);
}

async function loadTab(tab) {
    if (tab === 'profile' || tab === 'privacy' || tab === 'notif-settings' || tab === 'order-settings') {
        await loadOverview();
        return;
    }

    if (tab === 'payments') {
        await loadPayments();
        return;
    }

    if (tab === 'addresses') {
        await loadAddresses();
        return;
    }

    if (tab === 'purchases') {
        await loadOrders();
        return;
    }

    if (tab === 'notifications') {
        await loadNotifications();
        return;
    }

    if (tab === 'vouchers') {
        await loadVouchers();
    }
}

async function loadOverview() {
    try {
        const response = await axios.get('/api/v1/member/account');
        const data = response.data?.data ?? {};
        const member = data.member ?? {};
        const settings = data.settings ?? {};

        document.getElementById('member-sidebar-username').textContent = member.username || member.name || 'Member';
        document.getElementById('member-username').value = member.username ?? '';
        document.getElementById('member-name').value = member.name ?? '';
        document.getElementById('member-email').value = member.email ?? '';
        document.getElementById('member-email-masked').textContent = member.email_masked ?? member.email ?? '';
        document.getElementById('member-phone').value = member.phone ?? '';
        document.getElementById('member-phone-masked').textContent = member.phone_masked ?? member.phone ?? 'Add phone';
        document.getElementById('member-dob').value = member.date_of_birth ?? '';
        setAvatar(member.avatar_url, member.initials);

        document.querySelectorAll('input[name="gender"]').forEach((input) => {
            input.checked = input.value === member.gender;
        });

        document.getElementById('set-share-activity').checked = Boolean(settings.privacy?.share_order_activity);
        document.getElementById('set-personalized').checked = Boolean(settings.privacy?.personalized_recommendations);
        document.getElementById('set-notif-orders').checked = Boolean(settings.notifications?.order_updates);
        document.getElementById('set-notif-promos').checked = Boolean(settings.notifications?.promotions);
        document.getElementById('set-notif-wallet').checked = Boolean(settings.notifications?.wallet);
        document.getElementById('set-delivery-type').value = settings.orders?.default_delivery_type || 'CURBSIDE';
        document.getElementById('set-substitution').checked = Boolean(settings.orders?.allow_substitution);
    } catch (error) {
        if (!requireAuth(error)) {
            showToast('Account unavailable', apiError(error, 'Could not load your profile.'), 'error');
        }
    }
}

function emptyState(message) {
    return `<p class="py-6 text-center text-[13px] text-sh-s400">${escapeHtml(message)}</p>`;
}

async function loadAddresses() {
    const list = document.getElementById('member-addresses-list');

    try {
        const response = await axios.get('/api/v1/member/addresses');
        const addresses = response.data?.data?.addresses ?? [];

        if (!addresses.length) {
            list.innerHTML = emptyState('No addresses yet.');
            return;
        }

        list.innerHTML = addresses.map((address) => `
            <div class="rounded-xl border border-sh-s100 p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="font-semibold text-sh-s900">${escapeHtml(address.recipient_name)} <span class="font-normal text-sh-s500">${escapeHtml(address.phone)}</span></div>
                        <div class="mt-1 text-[13px] text-sh-s600">${escapeHtml([address.line1, address.line2, address.city, address.state, address.zip].filter(Boolean).join(', '))}</div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            ${address.is_default ? '<span class="sh-badge sh-badge-amber">Default</span>' : ''}
                            ${address.is_pickup ? '<span class="sh-badge">Pickup</span>' : ''}
                            ${address.is_return ? '<span class="sh-badge">Return</span>' : ''}
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="text-sm font-semibold text-sh-blue" data-edit-address="${encodeURIComponent(JSON.stringify(address))}">Edit</button>
                        ${address.is_default ? '' : `<button type="button" class="text-sm font-semibold text-sh-s600" data-default-address="${escapeHtml(address.id)}">Set as default</button>`}
                        <button type="button" class="text-sm font-semibold text-red-600" data-delete-address="${escapeHtml(address.id)}">Delete</button>
                    </div>
                </div>
            </div>`).join('');
    } catch (error) {
        if (!requireAuth(error)) {
            showToast('Addresses unavailable', apiError(error, 'Could not load addresses.'), 'error');
        }
    }
}

function paymentRow(method) {
    const title = method.type === 'bank' ? (method.bank_name || 'Bank') : (method.brand || 'Card');
    const mask = method.type === 'bank' ? `**** ${escapeHtml(method.last4)}` : escapeHtml(method.masked);

    return `
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-sh-s100 px-4 py-3">
            <div>
                <div class="font-semibold text-sh-s900">${escapeHtml(title)} ${method.is_default ? '<span class="sh-badge sh-badge-green">Default</span>' : ''}</div>
                <div class="text-[13px] text-sh-s500">${mask}</div>
            </div>
            <div class="flex gap-2">
                ${method.is_default ? '' : `<button type="button" class="sh-btn sh-btn-sm sh-btn-outline" data-default-payment="${escapeHtml(method.id)}">Set as Default</button>`}
                <button type="button" class="text-sm font-semibold text-red-600" data-delete-payment="${escapeHtml(method.id)}">Delete</button>
            </div>
        </div>`;
}

async function loadPayments() {
    try {
        const response = await axios.get('/api/v1/member/payment-methods');
        const methods = response.data?.data?.payment_methods ?? [];
        const cards = methods.filter((method) => method.type === 'card');
        const banks = methods.filter((method) => method.type === 'bank');

        document.getElementById('member-cards-list').innerHTML = cards.length
            ? cards.map(paymentRow).join('')
            : emptyState('No cards saved.');
        document.getElementById('member-banks-list').innerHTML = banks.length
            ? banks.map(paymentRow).join('')
            : emptyState('No bank accounts saved.');
    } catch (error) {
        if (!requireAuth(error)) {
            showToast('Payments unavailable', apiError(error, 'Could not load payment methods.'), 'error');
        }
    }
}

async function loadOrders() {
    const list = document.getElementById('member-orders-list');
    const query = document.getElementById('member-order-search')?.value?.trim() ?? '';

    try {
        const response = await axios.get('/api/v1/member/orders', { params: { tab: purchaseTab, q: query } });
        const orders = response.data?.data?.orders ?? [];

        if (!orders.length) {
            list.innerHTML = emptyState('No purchases in this tab.');
            return;
        }

        list.innerHTML = orders.map((order) => `
            <div class="rounded-xl border border-sh-s100 p-4">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2 text-[13px]">
                    <div class="font-semibold text-sh-s900">${escapeHtml(order.seller)}</div>
                    <div class="font-bold text-sh-blue">${escapeHtml(order.tab_status)}</div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-sh-s900">${escapeHtml(order.product)}</div>
                        <div class="text-xs text-sh-s400">${escapeHtml(order.order_number)} · x${escapeHtml(order.quantity)} · ${escapeHtml(order.date)}</div>
                    </div>
                    <div class="text-right font-bold text-sh-s900">${formatMoney(order.total)}</div>
                </div>
            </div>`).join('');
    } catch (error) {
        if (!requireAuth(error)) {
            showToast('Purchases unavailable', apiError(error, 'Could not load orders.'), 'error');
        }
    }
}

async function loadNotifications() {
    const list = document.getElementById('member-notifications-list');

    try {
        const response = await axios.get('/api/v1/member/notifications');
        const items = response.data?.data?.notifications ?? [];

        if (!items.length) {
            list.innerHTML = emptyState('No notifications yet.');
            return;
        }

        list.innerHTML = items.map((item) => `
            <div class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-sh-s100 p-4 ${item.read ? 'bg-white' : 'bg-sh-blue-lt'}">
                <div>
                    <div class="font-semibold text-sh-s900">${escapeHtml(item.title)}</div>
                    <div class="mt-1 text-[13px] text-sh-s600">${escapeHtml(item.body)}</div>
                    <div class="mt-1 text-xs text-sh-s400">${escapeHtml(item.created_at)}</div>
                </div>
                ${item.read ? '' : `<button type="button" class="sh-btn sh-btn-sm sh-btn-outline" data-read-notification="${escapeHtml(item.id)}">View</button>`}
            </div>`).join('');
    } catch (error) {
        if (!requireAuth(error)) {
            showToast('Notifications unavailable', apiError(error, 'Could not load notifications.'), 'error');
        }
    }
}

async function loadVouchers() {
    const list = document.getElementById('member-vouchers-list');

    try {
        const response = await axios.get('/api/v1/member/vouchers');
        const vouchers = response.data?.data?.vouchers ?? [];

        if (!vouchers.length) {
            list.innerHTML = `<div class="sm:col-span-2">${emptyState('No vouchers yet. Redeem a code above.')}</div>`;
            return;
        }

        list.innerHTML = vouchers.map((voucher) => {
            const offer = voucher.type === 'percent'
                ? `${voucher.value}% off`
                : `${formatMoney(voucher.value)} off`;

            return `
                <div class="overflow-hidden rounded-xl border border-sh-s200">
                    <div class="bg-sh-blue px-4 py-5 text-white">
                        <div class="text-lg font-black">${escapeHtml(offer)}</div>
                        <div class="text-xs opacity-80">Min. spend ${formatMoney(voucher.min_spend)}</div>
                    </div>
                    <div class="px-4 py-3 text-[13px] text-sh-s600">
                        <div class="font-semibold text-sh-s900">${escapeHtml(voucher.title)}</div>
                        <div class="mt-1">${voucher.expired ? 'Expired' : `Expires ${escapeHtml(voucher.expires_at || '')}`}</div>
                    </div>
                </div>`;
        }).join('');
    } catch (error) {
        if (!requireAuth(error)) {
            showToast('Vouchers unavailable', apiError(error, 'Could not load vouchers.'), 'error');
        }
    }
}

function askConfirm(message, action) {
    confirmAction = action;
    const text = document.getElementById('member-confirm-text');

    if (text) {
        text.textContent = message;
    }

    openModal('member-confirm-modal');
}

function fillAddressForm(address) {
    document.getElementById('address-uuid').value = address?.id ?? '';
    document.getElementById('address-recipient').value = address?.recipient_name ?? '';
    document.getElementById('address-phone').value = address?.phone ?? '';
    document.getElementById('address-line1').value = address?.line1 ?? '';
    document.getElementById('address-line2').value = address?.line2 ?? '';
    document.getElementById('address-city').value = address?.city ?? '';
    document.getElementById('address-state').value = address?.state ?? '';
    document.getElementById('address-zip').value = address?.zip ?? '';
    document.getElementById('address-label').value = address?.label ?? '';
    document.getElementById('address-default').checked = Boolean(address?.is_default);
    document.getElementById('address-pickup').checked = Boolean(address?.is_pickup);
    document.getElementById('address-return').checked = Boolean(address?.is_return);
}

function setPaymentType(type) {
    document.getElementById('payment-type').value = type;
    document.getElementById('payment-card-fields').classList.toggle('hidden', type !== 'card');
    document.getElementById('payment-bank-name').classList.toggle('hidden', type !== 'bank');
}

function collectSettings() {
    return {
        privacy: {
            share_order_activity: document.getElementById('set-share-activity').checked,
            personalized_recommendations: document.getElementById('set-personalized').checked,
        },
        notifications: {
            order_updates: document.getElementById('set-notif-orders').checked,
            promotions: document.getElementById('set-notif-promos').checked,
            wallet: document.getElementById('set-notif-wallet').checked,
        },
        orders: {
            default_delivery_type: document.getElementById('set-delivery-type').value,
            allow_substitution: document.getElementById('set-substitution').checked,
        },
    };
}

function queueSettingsSave() {
    window.clearTimeout(settingsTimer);
    settingsTimer = window.setTimeout(async () => {
        try {
            await axios.put('/api/v1/member/settings', collectSettings());
            showToast('Saved', 'Settings updated.', 'success');
        } catch (error) {
            if (!requireAuth(error)) {
                showToast('Settings not saved', apiError(error, 'Could not save settings.'), 'error');
            }
        }
    }, 250);
}

function bindEvents() {
    document.querySelectorAll('[data-member-tab]').forEach((button) => {
        button.addEventListener('click', () => showTab(button.getAttribute('data-member-tab')));
    });

    document.querySelectorAll('[data-purchase-tab]').forEach((button) => {
        button.addEventListener('click', () => {
            purchaseTab = button.getAttribute('data-purchase-tab') || 'all';
            document.querySelectorAll('[data-purchase-tab]').forEach((tab) => {
                tab.classList.toggle('is-active', tab === button);
            });
            loadOrders();
        });
    });

    document.getElementById('member-order-search')?.addEventListener('input', () => {
        window.clearTimeout(document.getElementById('member-order-search')._timer);
        document.getElementById('member-order-search')._timer = window.setTimeout(loadOrders, 250);
    });

    document.querySelector('[data-toggle-email]')?.addEventListener('click', () => {
        document.getElementById('member-email')?.classList.toggle('hidden');
    });

    document.querySelector('[data-toggle-phone]')?.addEventListener('click', () => {
        document.getElementById('member-phone')?.classList.toggle('hidden');
    });

    document.getElementById('member-profile-form')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const gender = form.querySelector('input[name="gender"]:checked')?.value ?? null;

        try {
            await axios.put('/api/v1/member/profile', {
                name: form.name.value,
                username: form.username.value,
                email: form.email.value || null,
                phone: form.phone.value || null,
                gender,
                date_of_birth: form.date_of_birth.value || null,
            });
            showToast('Profile saved', 'Your account details were updated.', 'success');
            await loadOverview();
            await refreshCurrentUser();
        } catch (error) {
            if (!requireAuth(error)) {
                showToast('Profile not saved', apiError(error, 'Could not save profile.'), 'error');
            }
        }
    });

    document.getElementById('member-avatar-input')?.addEventListener('change', async (event) => {
        const file = event.target.files?.[0];

        if (!file) {
            return;
        }

        const body = new FormData();
        body.append('avatar', file);

        try {
            const response = await axios.post('/api/v1/member/avatar', body);
            setAvatar(response.data?.data?.avatar_url, 'M');
            showToast('Photo updated', 'Your profile photo was saved.', 'success');
            await refreshCurrentUser();
        } catch (error) {
            if (!requireAuth(error)) {
                showToast('Photo not saved', apiError(error, 'Use a JPEG or PNG under 1 MB.'), 'error');
            }
        }
    });

    document.getElementById('member-password-form')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.currentTarget;

        try {
            await axios.put('/api/v1/member/password', {
                current_password: form.current_password.value,
                password: form.password.value,
                password_confirmation: form.password_confirmation.value,
            });
            form.reset();
            showToast('Password updated', 'Use your new password next time you sign in.', 'success');
        } catch (error) {
            if (!requireAuth(error)) {
                showToast('Password not updated', apiError(error, 'Could not change password.'), 'error');
            }
        }
    });

    ['set-share-activity', 'set-personalized', 'set-notif-orders', 'set-notif-promos', 'set-notif-wallet', 'set-delivery-type', 'set-substitution']
        .forEach((id) => document.getElementById(id)?.addEventListener('change', queueSettingsSave));

    document.querySelector('[data-open-address]')?.addEventListener('click', () => {
        fillAddressForm(null);
        openModal('member-address-modal');
    });

    document.getElementById('member-address-form')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const uuid = form.uuid.value;
        const payload = {
            recipient_name: form.recipient_name.value,
            phone: form.phone.value,
            line1: form.line1.value,
            line2: form.line2.value || null,
            city: form.city.value,
            state: form.state.value,
            zip: form.zip.value,
            label: form.label.value || null,
            is_default: form.is_default.checked,
            is_pickup: form.is_pickup.checked,
            is_return: form.is_return.checked,
        };

        try {
            if (uuid) {
                await axios.put(`/api/v1/member/addresses/${uuid}`, payload);
            } else {
                await axios.post('/api/v1/member/addresses', payload);
            }

            closeModal('member-address-modal');
            showToast('Address saved', 'Delivery address updated.', 'success');
            await loadAddresses();
        } catch (error) {
            if (!requireAuth(error)) {
                showToast('Address not saved', apiError(error, 'Could not save address.'), 'error');
            }
        }
    });

    document.querySelectorAll('[data-open-payment]').forEach((button) => {
        button.addEventListener('click', () => {
            const type = button.getAttribute('data-open-payment') || 'card';
            document.getElementById('member-payment-form').reset();
            setPaymentType(type);
            openModal('member-payment-modal');
        });
    });

    document.getElementById('member-payment-form')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const type = form.type.value;

        try {
            await axios.post('/api/v1/member/payment-methods', {
                type,
                brand: type === 'card' ? form.brand.value : null,
                bank_name: type === 'bank' ? form.bank_name.value : null,
                last4: form.last4.value,
                exp_month: type === 'card' ? Number(form.exp_month.value) : null,
                exp_year: type === 'card' ? Number(form.exp_year.value) : null,
                holder_name: form.holder_name.value,
                is_default: form.is_default.checked,
            });
            closeModal('member-payment-modal');
            showToast('Payment method saved', 'Last 4 digits stored only.', 'success');
            await loadPayments();
        } catch (error) {
            if (!requireAuth(error)) {
                showToast('Not saved', apiError(error, 'Could not save payment method.'), 'error');
            }
        }
    });

    document.getElementById('member-voucher-form')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const code = document.getElementById('member-voucher-code').value;

        try {
            await axios.post('/api/v1/member/vouchers/redeem', { code });
            document.getElementById('member-voucher-code').value = '';
            showToast('Voucher added', 'The code is now in your wallet.', 'success');
            await loadVouchers();
        } catch (error) {
            if (!requireAuth(error)) {
                showToast('Code not added', apiError(error, 'Invalid voucher code.'), 'error');
            }
        }
    });

    document.querySelector('[data-read-all-notifications]')?.addEventListener('click', async () => {
        try {
            await axios.post('/api/v1/member/notifications/read-all');
            await loadNotifications();
        } catch (error) {
            if (!requireAuth(error)) {
                showToast('Could not update', apiError(error, 'Try again.'), 'error');
            }
        }
    });

    document.getElementById('member-confirm-ok')?.addEventListener('click', async () => {
        const action = confirmAction;
        confirmAction = null;
        closeModal('member-confirm-modal');

        if (action) {
            await action();
        }
    });

    document.getElementById('member-account')?.addEventListener('click', (event) => {
        const editAddress = event.target.closest('[data-edit-address]');
        const defaultAddress = event.target.closest('[data-default-address]');
        const deleteAddress = event.target.closest('[data-delete-address]');
        const defaultPayment = event.target.closest('[data-default-payment]');
        const deletePayment = event.target.closest('[data-delete-payment]');
        const readNotification = event.target.closest('[data-read-notification]');

        if (editAddress) {
            fillAddressForm(JSON.parse(decodeURIComponent(editAddress.getAttribute('data-edit-address'))));
            openModal('member-address-modal');
        }

        if (defaultAddress) {
            axios.post(`/api/v1/member/addresses/${defaultAddress.getAttribute('data-default-address')}/default`)
                .then(loadAddresses)
                .catch((error) => {
                    if (!requireAuth(error)) {
                        showToast('Not updated', apiError(error, 'Could not set default address.'), 'error');
                    }
                });
        }

        if (deleteAddress) {
            const id = deleteAddress.getAttribute('data-delete-address');
            askConfirm('Delete this address?', async () => {
                await axios.delete(`/api/v1/member/addresses/${id}`);
                showToast('Address deleted', 'The address was removed.', 'success');
                await loadAddresses();
            });
        }

        if (defaultPayment) {
            axios.post(`/api/v1/member/payment-methods/${defaultPayment.getAttribute('data-default-payment')}/default`)
                .then(loadPayments)
                .catch((error) => {
                    if (!requireAuth(error)) {
                        showToast('Not updated', apiError(error, 'Could not set default payment method.'), 'error');
                    }
                });
        }

        if (deletePayment) {
            const id = deletePayment.getAttribute('data-delete-payment');
            askConfirm('Delete this payment method?', async () => {
                await axios.delete(`/api/v1/member/payment-methods/${id}`);
                showToast('Deleted', 'Payment method removed.', 'success');
                await loadPayments();
            });
        }

        if (readNotification) {
            axios.post(`/api/v1/member/notifications/${readNotification.getAttribute('data-read-notification')}/read`)
                .then(loadNotifications)
                .catch((error) => {
                    if (!requireAuth(error)) {
                        showToast('Could not update', apiError(error, 'Try again.'), 'error');
                    }
                });
        }
    });

    window.addEventListener('hashchange', () => {
        const tab = window.location.hash.replace('#', '');
        showTab(tab);
    });
}

export function initMemberAccount() {
    if (!document.getElementById('member-account')) {
        return;
    }

    bindEvents();

    Promise.resolve(restorePortalUser()).then((user) => {
        if (!user && !getCurrentUser()) {
            showToast('Member sign-in required', 'Sign in with the account you use to shop.', 'warn');
            openAuthModal('login');
        }

        showTab(window.location.hash.replace('#', '') || 'profile');
    });
}
