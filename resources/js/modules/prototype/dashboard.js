import { getCurrentUser, openAuthModal } from '../auth/auth.js';
import { restorePortalUser, userHasAnyRole } from '../auth/portal-auth.js';
import { showToast } from '../landing/toast.js';
import { CONDITION_LABELS, formatMoney, loadState } from './state.js';

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
    }

    if (tab === 'orders') {
        renderDashOrders();
    }
}

export function refreshDashboard() {
    const user = getCurrentUser();
    const greet = document.getElementById('dash-greet');

    if (greet) {
        greet.textContent = user?.name ?? user?.seller_profile?.store_name ?? 'Seller';
    }

    const sellerName = document.getElementById('dash-seller-name');

    if (sellerName) {
        sellerName.textContent = user?.seller_profile?.store_name ?? user?.name ?? 'My Store';
    }

    const storeUrl = document.getElementById('dash-store-url');

    if (storeUrl && user?.seller_profile?.store_slug) {
        storeUrl.textContent = `${window.location.host}/store/${user.seller_profile.store_slug}`;
    }

    renderDashProducts();
    renderDashOrders();
    renderOverviewOrders();
}

function renderOverviewOrders() {
    const list = document.getElementById('dash-orders-list');
    const state = loadState();
    const orders = state.orders.slice(0, 5);

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
                <div class="text-xs font-semibold text-sh-s900">${order.orderNum}</div>
                <div class="text-[13px] text-sh-s500">${order.product}</div>
            </div>
            <div class="text-right">
                <div class="text-sm font-bold text-sh-s900">${formatMoney(order.amount)}</div>
                <span class="sh-badge sh-badge-green">${order.status}</span>
            </div>
        </div>`).join('');
}

export function renderDashProducts() {
    const el = document.getElementById('dash-products-list');
    const state = loadState();

    if (!el) {
        return;
    }

    if (!state.sellerProducts.length) {
        el.innerHTML = '<div class="py-8 text-center text-[13px] text-sh-s400">No products listed yet. Click <strong>+ Add Product</strong> to start selling.</div>';
        return;
    }

    el.innerHTML = state.sellerProducts.map((product) => `
        <div class="grid grid-cols-1 items-center gap-3 border-b border-sh-s100 py-4 last:border-0 md:grid-cols-[2fr_repeat(4,1fr)]">
            <span class="flex items-center gap-3 text-[13px]"><span class="text-2xl">${product.icon ?? '🛋️'}</span><strong>${product.name}</strong></span>
            <span class="font-semibold">${formatMoney(product.price)}</span>
            <span><span class="sh-badge sh-badge-blue">${CONDITION_LABELS[product.condition] ?? product.condition}</span></span>
            <span><span class="sh-badge sh-badge-green">Active</span></span>
            <span class="flex gap-2">
                <button type="button" class="sh-btn sh-btn-sm sh-btn-outline" data-modal-open="add-product-modal">Edit</button>
                <button type="button" class="sh-btn sh-btn-sm sh-btn-outline text-red-600" data-remove-product="${product.id}">Remove</button>
            </span>
        </div>`).join('');
}

function renderDashOrders() {
    const el = document.getElementById('dash-orders-tab-list');
    const state = loadState();

    if (!el) {
        return;
    }

    if (!state.orders.length) {
        el.innerHTML = '<div class="py-8 text-center text-[13px] text-sh-s400">No orders to display.</div>';
        return;
    }

    el.innerHTML = state.orders.map((order) => `
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-sh-s100 py-4 last:border-0">
            <div>
                <div class="font-semibold text-sh-s900">${order.orderNum}</div>
                <div class="text-[13px] text-sh-s500">${order.product} · ${order.seller}</div>
                <div class="text-xs text-sh-s400">${order.date}</div>
            </div>
            <div class="text-right">
                <div class="font-bold text-sh-s900">${formatMoney(order.amount)}</div>
                <span class="sh-badge sh-badge-green">${order.status}</span>
            </div>
        </div>`).join('');
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
