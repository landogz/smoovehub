import axios from 'axios';
import { closeModal, openModal } from '../../shared/modal.js';
import { getCurrentUser, openAuthModal } from '../auth/auth.js';
import { portalLogout, restorePortalUser, userHasAnyRole } from '../auth/portal-auth.js';
import { showToast } from '../landing/toast.js';
import { CONDITION_LABELS, formatMoney } from './state.js';

const DASH_TABS = [
    'overview',
    'products',
    'orders',
    'payouts',
    'vouchers',
    'settings',
    'distcatalog',
    'myimports',
    'mypurchases',
    'onsiteappts',
    'openapi',
];

const ORDER_STATUSES = ['processing', 'shipped', 'delivered', 'cancelled'];

let dashboardCache = {
    products: [],
    orders: [],
    banks: [],
    vouchers: [],
};

let confirmAction = null;

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function paymentBadge(status) {
    if (status === 'paid') {
        return 'sh-badge sh-badge-green';
    }

    return 'sh-badge sh-badge-amber';
}

function emptyState(title, copy) {
    return `<div class="sh-dash-empty">
        <div class="text-sm font-semibold text-sh-s800">${escapeHtml(title)}</div>
        <p class="mt-1 max-w-sm text-[13px] text-sh-s500">${escapeHtml(copy)}</p>
    </div>`;
}

function statusLabel(value) {
    return String(value ?? '').replace(/_/g, ' ');
}

function askConfirm(message, action) {
    confirmAction = action;
    const text = document.getElementById('seller-confirm-text');

    if (text) {
        text.textContent = message;
    }

    openModal('seller-confirm-modal');
}

export function dashTab(tab, options = {}) {
    const next = DASH_TABS.includes(tab) ? tab : 'overview';

    DASH_TABS.forEach((key) => {
        document.getElementById(`dash-${key}`)?.classList.toggle('hidden', key !== next);
        document.getElementById(`dash-${key}`)?.classList.toggle('sh-dash-panel-active', key === next);

        const link = document.querySelector(`nav[aria-label="Dashboard navigation"] [data-dash-tab="${key}"]`);

        if (link) {
            link.classList.toggle('is-active', key === next);
        }
    });

    if (!options.skipHash) {
        const hash = `#${next}`;

        if (window.location.hash !== hash) {
            window.history.replaceState(null, '', hash);
        }
    }

    if (next === 'products') {
        renderDashProducts();
        loadSellerProducts();
    }

    if (next === 'orders') {
        renderDashOrders();
        loadSellerOrders();
    }

    if (next === 'payouts') {
        loadSellerPayouts();
    }

    if (next === 'vouchers') {
        loadSellerVouchers();
    }

    if (next === 'settings') {
        loadSellerSettings();
    }

    if (next === 'distcatalog') {
        loadDistCatalog();
    }

    if (next === 'myimports' || next === 'mypurchases') {
        loadImports(next);
    }

    if (next === 'onsiteappts') {
        loadOnsite();
    }

    if (next === 'openapi') {
        loadOpenApi();
    }
}

export function refreshDashboard() {
    const user = getCurrentUser();
    const greet = document.getElementById('dash-greet');

    if (greet) {
        greet.textContent = user?.name ?? user?.seller_profile?.store_name ?? 'Seller';
    }

    applySellerIdentity(user);
    applyStorefront(user?.seller_profile);
    loadDashboardOverview();
}

function storeInitials(name) {
    const parts = String(name ?? 'S').trim().split(/\s+/).filter(Boolean);

    if (parts.length === 0) {
        return 'S';
    }

    if (parts.length === 1) {
        return parts[0].slice(0, 1).toUpperCase();
    }

    return `${parts[0][0]}${parts[1][0]}`.toUpperCase();
}

function paintStoreLogo(element, store) {
    if (!element) {
        return;
    }

    if (store?.logo_url) {
        element.innerHTML = `<img src="${escapeHtml(store.logo_url)}" alt="" class="h-full w-full object-cover">`;
        return;
    }

    element.textContent = storeInitials(store?.store_name);
}

function paintAvatar(element, user) {
    if (!element) {
        return;
    }

    if (user?.avatar_url) {
        element.innerHTML = `<img src="${escapeHtml(user.avatar_url)}" alt="" class="h-full w-full object-cover">`;
        return;
    }

    element.textContent = user?.avatar_initials ?? 'S';
}

function applySellerIdentity(user) {
    const username = user?.username ? `@${user.username}` : 'Seller';
    const displayName = user?.seller_profile?.store_name ?? user?.name ?? 'Seller';

    setText('dash-seller-username', username);
    setText('dash-header-username', username);
    setText('dash-header-name', displayName);
    paintAvatar(document.getElementById('dash-header-avatar'), user);
}

function applyStorefront(store) {
    if (!store) {
        return;
    }

    const sellerName = document.getElementById('dash-seller-name');

    if (sellerName) {
        sellerName.textContent = store.store_name ?? 'My Store';
    }

    const headerName = document.getElementById('dash-header-name');

    if (headerName && store.store_name) {
        headerName.textContent = store.store_name;
    }

    const hostUrl = store.store_slug ? `${window.location.host}/store/${store.store_slug}` : '';

    const storeUrl = document.getElementById('dash-store-url');

    if (storeUrl && hostUrl) {
        storeUrl.textContent = hostUrl;
    }

    paintStoreLogo(document.getElementById('dash-seller-avatar'), store);
    fillSettingsForm(store);
}

function fillSettingsForm(settings) {
    setValue('set-store-name', settings.store_name ?? '');
    setValue('set-description', settings.description ?? '');
    setValue('set-phone', settings.phone ?? '');
    setValue('set-city', settings.city ?? '');
    setValue('set-state', settings.state ?? '');
    setValue('set-zip', settings.pickup_zip ?? '');
    setValue('set-street', settings.street ?? '');
    setValue('set-business-type', settings.business_type ?? '');
    setValue('set-email', settings.email ?? getCurrentUser()?.email ?? '');
    setValue('set-returns', settings.policies?.returns ?? '');
    setValue('set-shipping', settings.policies?.shipping ?? '');

    const onsite = document.getElementById('set-onsite');

    if (onsite) {
        onsite.checked = Boolean(settings.accepts_onsite_visits);
    }

    const publicUrl = settings.store_slug ? `${window.location.host}/store/${settings.store_slug}` : '';
    setText('set-public-url', publicUrl || 'Store URL pending');

    const verified = document.getElementById('set-verified');

    if (verified) {
        verified.textContent = settings.verified ? 'Verified' : 'Pending';
        verified.className = settings.verified ? 'sh-badge sh-badge-green' : 'sh-badge sh-badge-amber';
    }

    setText('set-rating', settings.rating ? `Rating ${Number(settings.rating).toFixed(1)}` : 'Rating —');
    setText('set-member-since', settings.member_since ? `Since ${settings.member_since}` : '');
    setText('set-preview-name', settings.store_name ?? 'My Store');

    const location = [settings.city, settings.state, settings.pickup_zip].filter(Boolean).join(', ');
    setText('set-preview-meta', location || 'Location not set');

    const onsiteHint = document.getElementById('set-preview-onsite');

    if (onsiteHint) {
        onsiteHint.classList.toggle('hidden', !settings.accepts_onsite_visits);
        onsiteHint.classList.toggle('block', Boolean(settings.accepts_onsite_visits));
    }

    paintStoreLogo(document.getElementById('set-preview-logo'), settings);
    paintStoreLogo(document.getElementById('set-logo-preview'), settings);
}

function setText(id, value) {
    const el = document.getElementById(id);

    if (el) {
        el.textContent = value;
    }
}

function setValue(id, value) {
    const el = document.getElementById(id);

    if (el) {
        el.value = value ?? '';
    }
}

async function loadDashboardOverview() {
    try {
        const response = await axios.get('/api/v1/seller/dashboard');
        const data = response.data?.data ?? {};

        applyStorefront(data.store);
        dashboardCache.orders = data.recent_orders ?? [];

        const stats = data.stats ?? {};
        setText('dash-stat-revenue', formatMoney(stats.revenue_30d ?? 0));
        setText('dash-stat-orders', String(stats.orders ?? 0));
        setText('dash-stat-products', String(stats.products ?? 0));
        setText('dash-stat-rating', stats.rating ? `${stats.rating}★` : '—');

        renderOverviewOrders();
    } catch (error) {
        if (error.response?.status === 401 || error.response?.status === 403) {
            return;
        }

        showToast('Dashboard unavailable', error.response?.data?.message ?? 'Could not load overview.', 'error');
    }
}

function renderOverviewOrders() {
    const list = document.getElementById('dash-orders-list');
    const orders = dashboardCache.orders.slice(0, 5);

    if (!list) {
        return;
    }

    if (!orders.length) {
        list.innerHTML = emptyState('No orders yet', 'Customer purchases will appear here.');
        return;
    }

    list.innerHTML = orders.map((order) => `
        <div class="sh-dash-row">
            <div>
                <div class="text-[13px] font-semibold text-sh-s900">${escapeHtml(order.order_number)}</div>
                <div class="text-[12px] text-sh-s500">${escapeHtml(order.product)}</div>
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-sh-s900">${formatMoney(order.total)}</div>
                <span class="${paymentBadge(order.payment_status)}">${escapeHtml(statusLabel(order.payment_status))}</span>
            </div>
        </div>`).join('');
}

export function renderDashProducts() {
    const el = document.getElementById('dash-products-list');
    const products = dashboardCache.products;

    if (!el) {
        return;
    }

    if (!products.length) {
        el.innerHTML = emptyState('No listings yet', 'Add a product to start selling on your storefront.');
        return;
    }

    el.innerHTML = products.map((product) => `
        <div class="sh-dash-row mb-2 last:mb-0">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-lg">${escapeHtml(product.icon ?? '🛋️')}</span>
                <div class="min-w-0">
                    <div class="truncate text-[13px] font-semibold text-sh-s900">${escapeHtml(product.name)}</div>
                    <div class="text-[12px] text-sh-s500">${formatMoney(product.price)} · ${escapeHtml(CONDITION_LABELS[product.condition] ?? product.condition)}</div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="sh-badge sh-badge-green">${escapeHtml(product.status ?? 'active')}</span>
                <button type="button" class="sh-dash-ghost" data-edit-product="${escapeHtml(product.id)}">Edit</button>
                <button type="button" class="sh-dash-ghost text-red-600" data-remove-product="${escapeHtml(product.id)}">Remove</button>
            </div>
        </div>`).join('');
}

async function loadSellerProducts() {
    try {
        const response = await axios.get('/api/v1/seller/products');
        dashboardCache.products = response.data?.data?.products ?? [];
        renderDashProducts();
    } catch (error) {
        if (error.response?.status !== 401 && error.response?.status !== 403) {
            showToast('Products unavailable', error.response?.data?.message ?? 'Could not load listings.', 'error');
        }
    }
}

function renderDashOrders() {
    const el = document.getElementById('dash-orders-tab-list');

    if (!el) {
        return;
    }

    if (!dashboardCache.orders.length) {
        el.innerHTML = emptyState('No orders yet', 'When shoppers buy from your store, fulfillment actions will show here.');
        return;
    }

    el.innerHTML = dashboardCache.orders.map((order) => `
        <div class="sh-dash-row mb-2 last:mb-0">
            <div>
                <div class="text-[13px] font-semibold text-sh-s900">${escapeHtml(order.order_number)}</div>
                <div class="text-[12px] text-sh-s500">${escapeHtml(order.product)} · ${escapeHtml(order.date ?? '')}</div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="text-right">
                    <div class="text-sm font-semibold text-sh-s900">${formatMoney(order.total)}</div>
                    <span class="${paymentBadge(order.payment_status)}">${escapeHtml(statusLabel(order.payment_status))}</span>
                </div>
                ${ORDER_STATUSES.filter((status) => status !== order.status).map((status) => `
                    <button type="button" class="sh-dash-ghost" data-order-status="${escapeHtml(order.id)}" data-status="${status}">${escapeHtml(statusLabel(status))}</button>
                `).join('')}
            </div>
        </div>`).join('');
}

async function loadSellerOrders() {
    try {
        const response = await axios.get('/api/v1/seller/orders');
        dashboardCache.orders = response.data?.data?.orders ?? [];
        renderDashOrders();
        renderOverviewOrders();
    } catch (error) {
        if (error.response?.status !== 401 && error.response?.status !== 403) {
            showToast('Orders unavailable', error.response?.data?.message ?? 'Could not load orders.', 'error');
        }
    }
}

function renderBanks(banks) {
    const list = document.getElementById('dash-banks-list');

    if (!list) {
        return;
    }

    if (!banks.length) {
        list.innerHTML = emptyState('No banks yet', 'Add a checking or savings account using the last 4 digits only.');
        return;
    }

    list.innerHTML = banks.map((bank) => `
        <div class="sh-dash-row">
            <div>
                <div class="text-sm font-semibold text-sh-s900">${escapeHtml(bank.bank_name)} · •••• ${escapeHtml(bank.last4)}</div>
                <div class="text-[12px] text-sh-s500">${escapeHtml(bank.holder_name)} · ${escapeHtml(bank.account_type)}${bank.is_default ? ' · Default' : ''}</div>
            </div>
            <div class="flex gap-2">
                ${bank.is_default ? '' : `<button type="button" class="sh-dash-ghost" data-default-bank="${escapeHtml(bank.id)}">Default</button>`}
                <button type="button" class="sh-dash-ghost text-red-600" data-delete-bank="${escapeHtml(bank.id)}">Remove</button>
            </div>
        </div>`).join('');
}

async function loadSellerPayouts() {
    try {
        const response = await axios.get('/api/v1/seller/payouts');
        const data = response.data?.data ?? {};
        dashboardCache.banks = data.banks ?? [];
        setText('dash-payout-available', formatMoney(data.available ?? 0));
        setText('dash-payout-pending', formatMoney(data.pending ?? 0));
        setText('dash-payout-paid', formatMoney(data.total_paid ?? 0));
        setText('dash-payout-note', data.note ?? '');
        renderBanks(dashboardCache.banks);
    } catch {
        // Empty payouts stay as placeholders until auth succeeds.
    }
}

function renderVouchers(vouchers) {
    const list = document.getElementById('dash-vouchers-list');

    if (!list) {
        return;
    }

    if (!vouchers.length) {
        list.innerHTML = emptyState('No vouchers yet', 'Create a store code members can redeem from My Account.');
        return;
    }

    list.innerHTML = vouchers.map((voucher) => `
        <div class="sh-dash-row mb-2 last:mb-0">
            <div>
                <div class="font-mono text-sm font-semibold text-sh-blue">${escapeHtml(voucher.code)}</div>
                <div class="text-[13px] text-sh-s800">${escapeHtml(voucher.title)}</div>
                <div class="text-[12px] text-sh-s500">${voucher.type === 'percent' ? `${voucher.value}%` : formatMoney(voucher.value)} off · min ${formatMoney(voucher.min_spend)}</div>
            </div>
            <div class="flex gap-2">
                <span class="sh-badge ${voucher.status === 'active' ? 'sh-badge-green' : 'sh-badge-slate'}">${escapeHtml(voucher.status)}</span>
                <button type="button" class="sh-dash-ghost" data-edit-voucher="${escapeHtml(voucher.id)}">Edit</button>
                <button type="button" class="sh-dash-ghost text-red-600" data-delete-voucher="${escapeHtml(voucher.id)}">Delete</button>
            </div>
        </div>`).join('');
}

async function loadSellerVouchers() {
    try {
        const response = await axios.get('/api/v1/seller/vouchers');
        dashboardCache.vouchers = response.data?.data?.vouchers ?? [];
        renderVouchers(dashboardCache.vouchers);
    } catch (error) {
        if (error.response?.status !== 401 && error.response?.status !== 403) {
            showToast('Vouchers unavailable', error.response?.data?.message ?? 'Could not load vouchers.', 'error');
        }
    }
}

async function loadSellerSettings() {
    try {
        const response = await axios.get('/api/v1/seller/settings');
        applyStorefront(response.data?.data?.settings);
    } catch {
        // Guest shell until seller signs in.
    }
}

async function loadDistCatalog() {
    const body = document.getElementById('dash-dist-catalog-body');

    if (!body) {
        return;
    }

    try {
        const response = await axios.get('/api/v1/seller/dist-catalog');
        const items = response.data?.data?.items ?? [];

        if (!items.length) {
            body.innerHTML = '<tr><td colspan="5" class="px-4 py-12 text-center text-[13px] text-sh-s500">No wholesale SKUs yet. Distributor inventory uploads ship in Phase 5.</td></tr>';
            return;
        }

        body.innerHTML = items.map((item) => `
            <tr class="border-b border-sh-s100 last:border-0">
                <td class="px-4 py-3.5 font-mono text-xs text-sh-s600">${escapeHtml(item.sku)}</td>
                <td class="px-4 py-3.5 font-medium text-sh-s900">${escapeHtml(item.name)}</td>
                <td class="px-4 py-3.5">${formatMoney(item.wholesale)}</td>
                <td class="px-4 py-3.5 text-sh-s600">${escapeHtml(item.qty)}</td>
                <td class="px-4 py-3.5 text-right">
                    <button type="button" class="sh-btn sh-btn-sm sh-btn-blue" data-import-sku="${escapeHtml(item.id)}">Import</button>
                </td>
            </tr>`).join('');
    } catch {
        body.innerHTML = '<tr><td colspan="5" class="px-4 py-12 text-center text-[13px] text-sh-s500">Sign in as a seller to browse wholesale inventory.</td></tr>';
    }
}

async function loadImports(tab) {
    const target = document.getElementById(tab === 'mypurchases' ? 'dash-purchases-list' : 'dash-imports-list');

    if (!target) {
        return;
    }

    try {
        const path = tab === 'mypurchases' ? '/api/v1/seller/purchases' : '/api/v1/seller/imports';
        const key = tab === 'mypurchases' ? 'purchases' : 'imports';
        const response = await axios.get(path);
        const rows = response.data?.data?.[key] ?? [];

        if (!rows.length) {
            target.innerHTML = emptyState(
                tab === 'mypurchases' ? 'No purchases yet' : 'No imports yet',
                tab === 'mypurchases'
                    ? 'Distribution purchases will appear here.'
                    : 'Import a SKU from the distribution catalog to populate this list.',
            );
            return;
        }

        target.innerHTML = rows.map((row) => `
            <div class="sh-dash-row mb-2 last:mb-0">
                <div>
                    <div class="text-[13px] font-semibold text-sh-s900">${escapeHtml(row.name ?? 'Imported SKU')}</div>
                    <div class="text-[12px] text-sh-s500">${escapeHtml(statusLabel(row.status))} · ${formatMoney(row.resale_price)} · ${escapeHtml(row.imported_at ?? '')}</div>
                </div>
            </div>`).join('');
    } catch {
        target.innerHTML = emptyState('Unavailable', 'Sign in as a seller to load this list.');
    }
}

async function loadOnsite() {
    const target = document.getElementById('dash-onsite-list');

    if (!target) {
        return;
    }

    try {
        const response = await axios.get('/api/v1/seller/onsite-appointments');
        const rows = response.data?.data?.appointments ?? [];

        if (!rows.length) {
            target.innerHTML = emptyState('No visit requests', 'Buyer showroom appointments will appear here.');
            return;
        }

        target.innerHTML = rows.map((row) => `
            <div class="sh-dash-row mb-2 last:mb-0">
                <div>
                    <div class="text-sm font-semibold text-sh-s900">${escapeHtml(statusLabel(row.status))}</div>
                    <div class="text-[12px] text-sh-s500">${escapeHtml(row.scheduled_at || 'Unscheduled')} · ${escapeHtml(row.customer_notes || 'No notes')}</div>
                </div>
                <div class="flex gap-2">
                    ${row.status === 'confirmed' || row.status === 'cancelled' ? '' : `
                        <button type="button" class="sh-btn sh-btn-sm sh-btn-blue" data-onsite-status="${escapeHtml(row.id)}" data-status="confirmed">Confirm</button>
                        <button type="button" class="sh-dash-ghost" data-onsite-status="${escapeHtml(row.id)}" data-status="cancelled">Cancel</button>
                    `}
                </div>
            </div>`).join('');
    } catch {
        target.innerHTML = emptyState('Unavailable', 'Sign in as a seller to load visit requests.');
    }
}

function renderOpenApi(data, plainToken) {
    const target = document.getElementById('dash-openapi-status');
    const plain = document.getElementById('dash-openapi-plain');

    if (target) {
        target.textContent = data.key_preview
            ? `API Key: ${data.key_preview}`
            : (data.message ?? 'No key issued yet.');
    }

    if (plain) {
        if (plainToken) {
            plain.textContent = `Copy now: ${plainToken}`;
            plain.classList.remove('hidden');
        } else {
            plain.classList.add('hidden');
            plain.textContent = '';
        }
    }
}

async function loadOpenApi() {
    try {
        const response = await axios.get('/api/v1/seller/open-api');
        renderOpenApi(response.data?.data ?? {});
    } catch {
        setText('dash-openapi-status', 'Sign in as a seller to view API status.');
    }
}

export async function saveSellerSettings() {
    const name = document.getElementById('set-store-name')?.value.trim();

    if (!name) {
        showToast('Store name required', 'Enter a store name before saving.', 'warn');
        return;
    }

    try {
        const response = await axios.put('/api/v1/seller/settings', {
            store_name: name,
            description: document.getElementById('set-description')?.value.trim() || null,
            phone: document.getElementById('set-phone')?.value.trim() || null,
            city: document.getElementById('set-city')?.value.trim() || null,
            state: document.getElementById('set-state')?.value.trim() || null,
            pickup_zip: document.getElementById('set-zip')?.value.trim() || null,
            street: document.getElementById('set-street')?.value.trim() || null,
            business_type: document.getElementById('set-business-type')?.value.trim() || null,
            accepts_onsite_visits: Boolean(document.getElementById('set-onsite')?.checked),
            policies: {
                returns: document.getElementById('set-returns')?.value.trim() || null,
                shipping: document.getElementById('set-shipping')?.value.trim() || null,
            },
        });
        applyStorefront(response.data?.data?.settings);
        showToast('Settings saved', response.data?.message ?? 'Your store information has been updated.', 'success');
    } catch (error) {
        showToast('Save failed', error.response?.data?.message ?? 'Could not update settings.', 'error');
    }
}

export async function removeProduct(id) {
    askConfirm('Remove this listing from your store?', async () => {
        try {
            await axios.delete(`/api/v1/seller/products/${id}`);
            dashboardCache.products = dashboardCache.products.filter((product) => String(product.id) !== String(id));
            renderDashProducts();
            loadDashboardOverview();
            showToast('Product removed', 'Listing has been deleted.', 'success');
        } catch (error) {
            showToast('Remove failed', error.response?.data?.message ?? 'Could not delete this listing.', 'error');
        }
    });
}

async function saveBank() {
    const last4 = document.getElementById('bank-last4')?.value.trim();

    if (!/^\d{4}$/.test(last4)) {
        showToast('Last 4 required', 'Enter exactly four digits. Full account numbers are not accepted.', 'warn');
        return;
    }

    try {
        await axios.post('/api/v1/seller/payout-accounts', {
            bank_name: document.getElementById('bank-name')?.value.trim(),
            holder_name: document.getElementById('bank-holder')?.value.trim(),
            account_type: document.getElementById('bank-type')?.value,
            last4,
            is_default: Boolean(document.getElementById('bank-default')?.checked),
        });
        closeModal('seller-bank-modal');
        showToast('Bank saved', 'Payout account added with last 4 only.', 'success');
        loadSellerPayouts();
    } catch (error) {
        showToast('Bank not saved', error.response?.data?.message ?? 'Could not save this bank.', 'error');
    }
}

function resetVoucherForm() {
    setValue('voucher-id', '');
    setValue('voucher-code', '');
    setValue('voucher-title', '');
    setValue('voucher-type', 'amount');
    setValue('voucher-value', '');
    setValue('voucher-min', '0');
    setValue('voucher-expires', '');
    setValue('voucher-status', 'active');
}

function fillVoucherForm(voucher) {
    setValue('voucher-id', voucher.id);
    setValue('voucher-code', voucher.code);
    setValue('voucher-title', voucher.title);
    setValue('voucher-type', voucher.type);
    setValue('voucher-value', String(voucher.value));
    setValue('voucher-min', String(voucher.min_spend ?? 0));
    setValue('voucher-expires', voucher.expires_at ?? '');
    setValue('voucher-status', voucher.status);
}

async function saveVoucher() {
    const uuid = document.getElementById('voucher-id')?.value.trim();
    const payload = {
        code: document.getElementById('voucher-code')?.value.trim(),
        title: document.getElementById('voucher-title')?.value.trim(),
        type: document.getElementById('voucher-type')?.value,
        value: parseFloat(document.getElementById('voucher-value')?.value),
        min_spend: parseFloat(document.getElementById('voucher-min')?.value || '0'),
        expires_at: document.getElementById('voucher-expires')?.value || null,
        status: document.getElementById('voucher-status')?.value,
    };

    try {
        if (uuid) {
            await axios.put(`/api/v1/seller/vouchers/${uuid}`, payload);
        } else {
            await axios.post('/api/v1/seller/vouchers', payload);
        }

        closeModal('seller-voucher-modal');
        showToast('Voucher saved', 'Members can redeem this store code.', 'success');
        loadSellerVouchers();
    } catch (error) {
        showToast('Voucher not saved', error.response?.data?.message ?? 'Could not save voucher.', 'error');
    }
}

function tabFromHash() {
    const key = window.location.hash.replace('#', '');

    return DASH_TABS.includes(key) ? key : 'overview';
}

export function initDashboard() {
    const shell = document.getElementById('seller-dashboard');

    if (!shell) {
        return;
    }

    document.querySelectorAll('[data-dash-tab]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            document.getElementById('dash-user-menu')?.classList.add('hidden');
            document.getElementById('dash-user-toggle')?.setAttribute('aria-expanded', 'false');
            dashTab(link.dataset.dashTab);
        });
    });

    const userToggle = document.getElementById('dash-user-toggle');
    const userMenu = document.getElementById('dash-user-menu');

    userToggle?.addEventListener('click', () => {
        userMenu?.classList.toggle('hidden');
        userToggle.setAttribute(
            'aria-expanded',
            userMenu?.classList.contains('hidden') ? 'false' : 'true',
        );
    });

    document.addEventListener('click', (event) => {
        if (!userToggle || !userMenu) {
            return;
        }

        if (!userMenu.contains(event.target) && !userToggle.contains(event.target)) {
            userMenu.classList.add('hidden');
            userToggle.setAttribute('aria-expanded', 'false');
        }
    });

    document.querySelector('[data-dash-logout]')?.addEventListener('click', async () => {
        await portalLogout();
        window.location.href = '/';
    });

    window.addEventListener('hashchange', () => dashTab(tabFromHash(), { skipHash: true }));

    document.getElementById('dash-store-url')?.addEventListener('click', () => {
        const user = getCurrentUser();
        const slug = user?.seller_profile?.store_slug;

        if (slug) {
            window.location.href = `/store/${slug}`;
        }
    });

    document.querySelectorAll('[data-open-my-store]').forEach((button) => {
        button.addEventListener('click', () => {
            const user = getCurrentUser();
            const slug = user?.seller_profile?.store_slug ?? 'demo-seller';
            window.location.href = `/store/${slug}`;
        });
    });

    document.getElementById('dash-products-list')?.addEventListener('click', (event) => {
        const edit = event.target.closest('[data-edit-product]');
        const remove = event.target.closest('[data-remove-product]');

        if (edit) {
            const product = dashboardCache.products.find((item) => String(item.id) === String(edit.dataset.editProduct));
            window.dispatchEvent(new CustomEvent('smoovehub:edit-product', { detail: { product } }));
        }

        if (remove) {
            window.dispatchEvent(new CustomEvent('smoovehub:remove-product', {
                detail: { id: remove.dataset.removeProduct },
            }));
        }
    });

    document.getElementById('dash-orders-tab-list')?.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-order-status]');

        if (!button) {
            return;
        }

        try {
            await axios.patch(`/api/v1/seller/orders/${button.dataset.orderStatus}`, { status: button.dataset.status });
            showToast('Order updated', `Status set to ${button.dataset.status}.`, 'success');
            loadSellerOrders();
            loadDashboardOverview();
        } catch (error) {
            showToast('Update failed', error.response?.data?.message ?? 'Could not update order.', 'error');
        }
    });

    document.getElementById('dash-banks-list')?.addEventListener('click', (event) => {
        const def = event.target.closest('[data-default-bank]');
        const del = event.target.closest('[data-delete-bank]');

        if (def) {
            axios.post(`/api/v1/seller/payout-accounts/${def.dataset.defaultBank}/default`)
                .then(() => {
                    showToast('Default bank updated', 'This account will receive Friday payouts once Stripe is live.', 'success');
                    loadSellerPayouts();
                })
                .catch((error) => showToast('Update failed', error.response?.data?.message ?? 'Could not set default.', 'error'));
        }

        if (del) {
            askConfirm('Remove this payout bank?', async () => {
                try {
                    await axios.delete(`/api/v1/seller/payout-accounts/${del.dataset.deleteBank}`);
                    showToast('Bank removed', 'Payout account deleted.', 'success');
                    loadSellerPayouts();
                } catch (error) {
                    showToast('Remove failed', error.response?.data?.message ?? 'Could not delete bank.', 'error');
                }
            });
        }
    });

    document.getElementById('dash-vouchers-list')?.addEventListener('click', (event) => {
        const edit = event.target.closest('[data-edit-voucher]');
        const del = event.target.closest('[data-delete-voucher]');

        if (edit) {
            const voucher = dashboardCache.vouchers.find((item) => String(item.id) === String(edit.dataset.editVoucher));
            fillVoucherForm(voucher);
            openModal('seller-voucher-modal');
        }

        if (del) {
            askConfirm('Delete this voucher?', async () => {
                try {
                    await axios.delete(`/api/v1/seller/vouchers/${del.dataset.deleteVoucher}`);
                    showToast('Voucher deleted', 'The store code is no longer available.', 'success');
                    loadSellerVouchers();
                } catch (error) {
                    showToast('Delete failed', error.response?.data?.message ?? 'Could not delete voucher.', 'error');
                }
            });
        }
    });

    document.getElementById('dash-dist-catalog-body')?.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-import-sku]');

        if (!button) {
            return;
        }

        try {
            await axios.post(`/api/v1/seller/dist-catalog/${button.dataset.importSku}/import`);
            showToast('SKU imported', 'The wholesale item is now in your catalog.', 'success');
            loadDistCatalog();
            loadImports('myimports');
        } catch (error) {
            showToast('Import failed', error.response?.data?.message ?? 'Could not import this SKU.', 'error');
        }
    });

    document.getElementById('dash-onsite-list')?.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-onsite-status]');

        if (!button) {
            return;
        }

        try {
            await axios.patch(`/api/v1/seller/onsite-appointments/${button.dataset.onsiteStatus}`, {
                status: button.dataset.status,
            });
            showToast('Visit updated', `Marked ${button.dataset.status}.`, 'success');
            loadOnsite();
        } catch (error) {
            showToast('Update failed', error.response?.data?.message ?? 'Could not update visit.', 'error');
        }
    });

    document.querySelector('[data-save-settings]')?.addEventListener('click', () => {
        window.dispatchEvent(new CustomEvent('smoovehub:save-settings'));
    });

    document.getElementById('set-logo')?.addEventListener('change', async (event) => {
        const file = event.target.files?.[0];

        if (!file) {
            return;
        }

        const form = new FormData();
        form.append('logo', file);

        try {
            const response = await axios.post('/api/v1/seller/logo', form);
            applyStorefront(response.data?.data?.settings);
            showToast('Logo updated', response.data?.message ?? 'Your store logo is live.', 'success');
        } catch (error) {
            showToast('Logo failed', error.response?.data?.message ?? 'Could not upload that image.', 'error');
        }

        event.target.value = '';
    });

    document.querySelector('[data-copy-store-url]')?.addEventListener('click', async () => {
        const url = document.getElementById('set-public-url')?.textContent?.trim();

        if (!url || url.includes('pending')) {
            showToast('No store URL', 'Save a store name first to generate a public link.', 'warn');
            return;
        }

        try {
            await navigator.clipboard.writeText(`${window.location.protocol}//${url}`);
            showToast('Link copied', 'Store URL is on your clipboard.', 'success');
        } catch {
            showToast('Copy failed', url, 'warn');
        }
    });

    document.querySelector('[data-open-bank-modal]')?.addEventListener('click', () => openModal('seller-bank-modal'));
    document.querySelector('[data-save-bank]')?.addEventListener('click', saveBank);
    document.querySelector('[data-open-voucher-modal]')?.addEventListener('click', () => {
        resetVoucherForm();
        openModal('seller-voucher-modal');
    });
    document.querySelector('[data-save-voucher]')?.addEventListener('click', saveVoucher);
    document.querySelector('[data-seller-confirm]')?.addEventListener('click', async () => {
        const action = confirmAction;
        confirmAction = null;
        closeModal('seller-confirm-modal');

        if (typeof action === 'function') {
            await action();
        }
    });

    document.querySelector('[data-generate-openapi]')?.addEventListener('click', () => {
        askConfirm('Generate a new Open API key? The previous key will stop working.', async () => {
            try {
                const response = await axios.post('/api/v1/seller/open-api/token');
                renderOpenApi(response.data?.data ?? {}, response.data?.data?.token);
                showToast('Key generated', 'Copy the token now. It will not be shown again.', 'success');
            } catch (error) {
                showToast('Key failed', error.response?.data?.message ?? 'Could not generate token.', 'error');
            }
        });
    });

    document.querySelector('[data-revoke-openapi]')?.addEventListener('click', () => {
        askConfirm('Revoke the current Open API key?', async () => {
            try {
                const response = await axios.delete('/api/v1/seller/open-api/token');
                renderOpenApi(response.data?.data ?? {});
                showToast('Key revoked', 'The store token is no longer valid.', 'success');
            } catch (error) {
                showToast('Revoke failed', error.response?.data?.message ?? 'Could not revoke token.', 'error');
            }
        });
    });

    window.addEventListener('smoovehub:dashboard-refresh', refreshDashboard);

    Promise.resolve(restorePortalUser()).then((user) => {
        if (!userHasAnyRole(user || getCurrentUser(), ['seller'])) {
            showToast('Seller sign-in required', 'Sign in with a seller account to use the dashboard.', 'warn');
            openAuthModal('login');
        }

        refreshDashboard();
        dashTab(tabFromHash());
    });
}
