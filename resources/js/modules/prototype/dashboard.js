import axios from 'axios';
import { getCurrentUser, openAuthModal } from '../auth/auth.js';
import { restorePortalUser, userHasAnyRole } from '../auth/portal-auth.js';
import { showToast } from '../landing/toast.js';
import { CONDITION_LABELS, formatMoney } from './state.js';

const DASH_TABS = [
    'overview',
    'products',
    'orders',
    'payouts',
    'settings',
    'distcatalog',
    'myimports',
    'mypurchases',
    'onsiteappts',
    'openapi',
];

let dashboardCache = {
    products: [],
    orders: [],
};

function paymentBadge(status) {
    if (status === 'paid') {
        return 'sh-badge sh-badge-green';
    }

    return 'sh-badge sh-badge-amber';
}

export function dashTab(tab) {
    DASH_TABS.forEach((key) => {
        document.getElementById(`dash-${key}`)?.classList.toggle('hidden', key !== tab);
        document.getElementById(`dash-${key}`)?.classList.toggle('sh-dash-panel-active', key === tab);

        const link = document.querySelector(`[data-dash-tab="${key}"]`);

        if (link) {
            link.classList.toggle('bg-white/15', key === tab);
            link.classList.toggle('text-white', key === tab);
            link.classList.toggle('text-white/70', key !== tab);
        }
    });

    if (tab === 'products') {
        renderDashProducts();
        loadSellerProducts();
    }

    if (tab === 'orders') {
        renderDashOrders();
        loadSellerOrders();
    }

    if (tab === 'payouts') {
        loadSellerPayouts();
    }

    if (tab === 'settings') {
        loadSellerSettings();
    }

    if (tab === 'distcatalog') {
        loadDistCatalog();
    }

    if (tab === 'myimports' || tab === 'mypurchases') {
        loadImports(tab);
    }

    if (tab === 'onsiteappts') {
        loadOnsite();
    }

    if (tab === 'openapi') {
        loadOpenApi();
    }
}

export function refreshDashboard() {
    const user = getCurrentUser();
    const greet = document.getElementById('dash-greet');

    if (greet) {
        greet.textContent = user?.name ?? user?.seller_profile?.store_name ?? 'Seller';
    }

    applyStorefront(user?.seller_profile);
    loadDashboardOverview();
}

function applyStorefront(store) {
    if (!store) {
        return;
    }

    const sellerName = document.getElementById('dash-seller-name');

    if (sellerName) {
        sellerName.textContent = store.store_name ?? 'My Store';
    }

    const storeUrl = document.getElementById('dash-store-url');

    if (storeUrl && store.store_slug) {
        storeUrl.textContent = `${window.location.host}/store/${store.store_slug}`;
    }

    const nameInput = document.getElementById('set-store-name');

    if (nameInput && !nameInput.value) {
        nameInput.value = store.store_name ?? '';
    }

    const emailInput = document.getElementById('set-email');

    if (emailInput) {
        emailInput.value = store.email ?? getCurrentUser()?.email ?? '';
    }
}

function setText(id, value) {
    const el = document.getElementById(id);

    if (el) {
        el.textContent = value;
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
        list.innerHTML = '<p class="py-6 text-center text-[13px] text-sh-s400">No orders yet.</p>';
        return;
    }

    list.innerHTML = orders.map((order) => `
        <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-sh-s100 px-4 py-3">
            <div>
                <div class="text-xs font-semibold text-sh-s900">${order.order_number}</div>
                <div class="text-[13px] text-sh-s500">${order.product}</div>
            </div>
            <div class="text-right">
                <div class="text-sm font-bold text-sh-s900">${formatMoney(order.total)}</div>
                <span class="${paymentBadge(order.payment_status)}">${order.payment_status}</span>
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
        el.innerHTML = '<div class="py-8 text-center text-[13px] text-sh-s400">No products listed yet. Click <strong>+ Add Product</strong> to start selling.</div>';
        return;
    }

    el.innerHTML = products.map((product) => `
        <div class="grid grid-cols-1 items-center gap-3 border-b border-sh-s100 py-4 last:border-0 md:grid-cols-[2fr_repeat(4,1fr)]">
            <span class="flex items-center gap-3 text-[13px]"><span class="text-2xl">${product.icon ?? '🛋️'}</span><strong>${product.name}</strong></span>
            <span class="font-semibold">${formatMoney(product.price)}</span>
            <span><span class="sh-badge sh-badge-blue">${CONDITION_LABELS[product.condition] ?? product.condition}</span></span>
            <span><span class="sh-badge sh-badge-green">${product.status ?? 'active'}</span></span>
            <span class="flex gap-2">
                <button type="button" class="sh-btn sh-btn-sm sh-btn-outline text-red-600" data-remove-product="${product.id}">Remove</button>
            </span>
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
        el.innerHTML = '<div class="py-8 text-center text-[13px] text-sh-s400">No orders to display.</div>';
        return;
    }

    el.innerHTML = dashboardCache.orders.map((order) => `
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-sh-s100 py-4 last:border-0">
            <div>
                <div class="font-semibold text-sh-s900">${order.order_number}</div>
                <div class="text-[13px] text-sh-s500">${order.product}</div>
                <div class="text-xs text-sh-s400">${order.date ?? ''}</div>
            </div>
            <div class="text-right">
                <div class="font-bold text-sh-s900">${formatMoney(order.total)}</div>
                <span class="${paymentBadge(order.payment_status)}">${order.payment_status}</span>
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

async function loadSellerPayouts() {
    try {
        const response = await axios.get('/api/v1/seller/payouts');
        const data = response.data?.data ?? {};
        setText('dash-payout-available', formatMoney(data.available ?? 0));
        setText('dash-payout-pending', formatMoney(data.pending ?? 0));
        setText('dash-payout-paid', formatMoney(data.total_paid ?? 0));
        setText('dash-payout-note', data.note ?? '');
    } catch {
        // Empty payouts stay as placeholders until auth succeeds.
    }
}

async function loadSellerSettings() {
    try {
        const response = await axios.get('/api/v1/seller/settings');
        applyStorefront(response.data?.data?.settings);
        const settings = response.data?.data?.settings ?? {};
        const nameInput = document.getElementById('set-store-name');

        if (nameInput) {
            nameInput.value = settings.store_name ?? '';
        }
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
            body.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-[13px] text-sh-s400">No wholesale SKUs yet. Distribution inventory ships in Phase 5.</td></tr>';
            return;
        }

        body.innerHTML = items.map((item) => `
            <tr class="border-b border-sh-s100 last:border-0">
                <td class="px-4 py-3 font-mono text-xs">${item.sku}</td>
                <td class="px-4 py-3 font-medium">${item.name}</td>
                <td class="px-4 py-3">${formatMoney(item.wholesale)}</td>
                <td class="px-4 py-3">${item.qty}</td>
                <td class="px-4 py-3 text-[12px] text-sh-s400">${item.supplier}</td>
            </tr>`).join('');
    } catch {
        body.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-[13px] text-sh-s400">Sign in as a seller to browse wholesale inventory.</td></tr>';
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
            target.textContent = tab === 'mypurchases'
                ? 'No distribution purchases yet.'
                : 'No import orders yet. Browse the Distribution Catalog to import wholesale inventory.';
            return;
        }

        target.innerHTML = rows.map((row) => `<div class="border-b border-sh-s100 py-3 text-[13px]">${row.status} · ${formatMoney(row.resale_price)}</div>`).join('');
    } catch {
        target.textContent = 'Sign in as a seller to load this list.';
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
        target.textContent = rows.length
            ? `${rows.length} visit request(s).`
            : 'Customer onsite visit requests will appear here when buyers book showroom appointments.';
    } catch {
        target.textContent = 'Sign in as a seller to load visit requests.';
    }
}

async function loadOpenApi() {
    const target = document.getElementById('dash-openapi-status');

    if (!target) {
        return;
    }

    try {
        const response = await axios.get('/api/v1/seller/open-api');
        const data = response.data?.data ?? {};
        target.textContent = data.message ?? data.key_preview ?? '';
    } catch {
        target.textContent = 'Sign in as a seller to view API status.';
    }
}

export async function saveSellerSettings() {
    const name = document.getElementById('set-store-name')?.value.trim();

    if (!name) {
        showToast('Store name required', 'Enter a store name before saving.', 'warn');
        return;
    }

    try {
        const response = await axios.put('/api/v1/seller/settings', { store_name: name });
        applyStorefront(response.data?.data?.settings);
        showToast('Settings saved', response.data?.message ?? 'Your store information has been updated.', 'success');
    } catch (error) {
        showToast('Save failed', error.response?.data?.message ?? 'Could not update settings.', 'error');
    }
}

export async function removeProduct(id) {
    try {
        await axios.delete(`/api/v1/seller/products/${id}`);
        dashboardCache.products = dashboardCache.products.filter((product) => String(product.id) !== String(id));
        renderDashProducts();
        loadDashboardOverview();
        showToast('Product removed', 'Listing has been deleted.', 'success');
    } catch (error) {
        showToast('Remove failed', error.response?.data?.message ?? 'Could not delete this listing.', 'error');
    }
}

export function initDashboard() {
    const shell = document.getElementById('seller-dashboard');

    if (!shell) {
        return;
    }

    document.querySelectorAll('[data-dash-tab]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            dashTab(link.dataset.dashTab);
        });
    });

    document.getElementById('dash-store-url')?.addEventListener('click', () => {
        const user = getCurrentUser();
        const slug = user?.seller_profile?.store_slug;

        if (slug) {
            window.location.href = `/store/${slug}`;
        }
    });

    document.querySelector('[data-open-my-store]')?.addEventListener('click', () => {
        const user = getCurrentUser();
        const slug = user?.seller_profile?.store_slug ?? 'demo-seller';
        window.location.href = `/store/${slug}`;
    });

    document.getElementById('dash-products-list')?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-remove-product]');

        if (button) {
            window.dispatchEvent(new CustomEvent('smoovehub:remove-product', {
                detail: { id: button.dataset.removeProduct },
            }));
        }
    });

    document.querySelector('[data-save-settings]')?.addEventListener('click', () => {
        window.dispatchEvent(new CustomEvent('smoovehub:save-settings'));
    });

    window.addEventListener('smoovehub:dashboard-refresh', refreshDashboard);

    Promise.resolve(restorePortalUser()).then((user) => {
        if (!userHasAnyRole(user || getCurrentUser(), ['seller'])) {
            showToast('Seller sign-in required', 'Sign in with a seller account to use the dashboard.', 'warn');
            openAuthModal('login');
        }

        refreshDashboard();
        dashTab('overview');
    });
}
