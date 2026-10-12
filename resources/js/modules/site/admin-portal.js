import { getCurrentUser } from '../auth/auth.js';
import {
    portalAuthErrorMessage,
    portalLogin,
    portalLogout,
    restorePortalUser,
    userHasAnyRole,
} from '../auth/portal-auth.js';
import { showToast } from '../landing/toast.js';
import { showPortalDashboard, showPortalLogin } from './portal-screen.js';

const STORAGE_SESSION = 'smoovehub_admin_session';
const STORAGE_SHOP_PRODS = 'sh_admin_prods';
const ADMIN_ROLES = ['admin', 'super-admin'];

let config = {};
let ADM_CREDENTIALS = {};
let ADM_SELLERS = [];
let ADM_DRIVERS = [];
let ADM_CUSTOMERS = [];
let ADM_DISTRIBUTORS = [];
let ADM_COMPANY_BANKS = {};
let ADM_ORDER_LEDGER = [];
let ADM_SHOP_PRODUCTS = [];
let ADM_FILE_RECORDS = [];
let ADM_ORDERS = [];
let ADM_PAYOUTS = [];
let ADM_INVENTORY = [];
let ADM_DELIVERY = [];
let ADM_API_SECTIONS = [];
let ADM_USER = null;
let ADM_ACTIVE_TAB = 'adm-overview';
let ADM_SHOP_EDIT_ID = null;
let ffFilter = 'all';
let homeUrl = '/';

const TAB_TITLES = {
    'adm-overview': 'Platform Overview',
    'adm-sellers': 'Seller Management',
    'adm-shopprods': 'Shop Products — Admin Listings',
    'adm-drivers': 'Driver Management',
    'adm-customers': 'Customer Management',
    'adm-dist': 'Distribution Companies',
    'adm-orders': 'Order Management',
    'adm-distorders': 'Distribution Order Ledger (Sourcing)',
    'adm-payouts': 'Payout Monitoring',
    'adm-delivery': 'Delivery Tracking',
    'adm-fulfillment': 'Fulfillment — Source Routing',
    'adm-analytics': 'Platform Analytics',
    'adm-apihub': 'API Hub — All Endpoints',
    'adm-filemanager': 'File Manager — Deliveries, Photos & Customer Records',
    'adm-settings': 'Admin Settings',
};

function loadConfig() {
    const el = document.getElementById('admin-portal-config');
    if (!el) return false;

    config = JSON.parse(el.textContent || '{}');
    ADM_CREDENTIALS = { ...(config.credentials ?? {}) };
    ADM_SELLERS = JSON.parse(JSON.stringify(config.sellers ?? []));
    ADM_DRIVERS = JSON.parse(JSON.stringify(config.drivers ?? []));
    ADM_CUSTOMERS = JSON.parse(JSON.stringify(config.customers ?? []));
    ADM_DISTRIBUTORS = [...(config.distributors ?? [])];
    ADM_COMPANY_BANKS = { ...(config.company_banks ?? {}) };
    ADM_ORDER_LEDGER = JSON.parse(JSON.stringify(config.order_ledger ?? []));
    ADM_ORDERS = [...(config.orders ?? [])];
    ADM_PAYOUTS = [...(config.payouts ?? [])];
    ADM_INVENTORY = [...(config.inventory ?? [])];
    ADM_DELIVERY = [...(config.delivery_integrations ?? [])];
    ADM_API_SECTIONS = [...(config.api_sections ?? [])];
    ADM_FILE_RECORDS = [...(config.file_records ?? [])];
    homeUrl = config.home_url ?? '/';

    const savedProds = localStorage.getItem(STORAGE_SHOP_PRODS);
    ADM_SHOP_PRODUCTS = savedProds
        ? JSON.parse(savedProds)
        : JSON.parse(JSON.stringify(config.shop_products ?? []));

    return true;
}

function persistShopProducts() {
    localStorage.setItem(STORAGE_SHOP_PRODS, JSON.stringify(ADM_SHOP_PRODUCTS));
}

function persistSession() {
    if (ADM_USER) {
        localStorage.setItem(STORAGE_SESSION, ADM_USER);
    } else {
        localStorage.removeItem(STORAGE_SESSION);
    }
}

function escapeHtml(v) {
    return String(v).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function parseMoney(str) {
    return parseFloat(String(str).replace(/[$,]/g, '')) || 0;
}

function admStat(label, val, icon, colorClass, bgClass, tag = '') {
    return `<div class="sh-adm-stat-card">
        <div class="flex items-center gap-2 mb-2.5">
            <div class="sh-adm-stat-icon ${bgClass}">${icon}</div>
            <div class="text-[11px] font-semibold uppercase tracking-wide text-sh-s400">${escapeHtml(label)}</div>
        </div>
        <div class="sh-adm-stat-value ${colorClass}">${escapeHtml(String(val))}</div>
        ${tag ? `<div class="mt-1.5 inline-block rounded-full px-2 py-0.5 text-[11px] font-semibold ${bgClass} ${colorClass}">${escapeHtml(tag)}</div>` : ''}
    </div>`;
}

function admStatusBadge(status) {
    const map = {
        Active: 'sh-adm-badge-active', Pending: 'sh-adm-badge-pending', Suspended: 'sh-adm-badge-banned',
        Inactive: 'sh-adm-badge-inactive', Verified: 'sh-adm-badge-verified', 'In Transit': 'sh-adm-badge-verified',
        Connected: 'sh-adm-badge-active', PAID: 'sh-adm-badge-active', PROCESSING: 'sh-adm-badge-verified',
        PENDING: 'sh-adm-badge-pending', FULFILLED: 'sh-adm-badge-active', PENDING_FULFILLMENT: 'sh-adm-badge-pending',
        IN_TRANSIT: 'sh-adm-badge-verified', DELIVERED: 'sh-adm-badge-active', Expired: 'sh-adm-badge-banned',
    };
    const cls = map[status] ?? 'sh-adm-badge-inactive';
    const label = String(status).replace(/_/g, ' ');
    return `<span class="sh-adm-badge ${cls}">${escapeHtml(label)}</span>`;
}

function showPortal() {
    showPortalDashboard('admin-login-overlay', 'admin-main');
}

function hidePortal() {
    showPortalLogin('admin-login-overlay', 'admin-main');
}

function admTab(tab) {
    ADM_ACTIVE_TAB = tab;
    document.querySelectorAll('[data-adm-tab]').forEach((el) => {
        el.classList.toggle('sh-adm-nav-item-active', el.dataset.admTab === tab);
    });
    document.querySelectorAll('.sh-adm-panel').forEach((panel) => {
        panel.classList.toggle('hidden', panel.id !== tab);
    });
    const title = document.getElementById('adm-page-title');
    if (title) title.textContent = TAB_TITLES[tab] ?? 'Admin';
    const refresh = document.getElementById('adm-last-refresh');
    if (refresh) {
        refresh.textContent = `Updated ${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
    }

    const renders = {
        'adm-overview': admRenderOverview,
        'adm-sellers': admRenderSellers,
        'adm-shopprods': admRenderShopProds,
        'adm-drivers': admRenderDrivers,
        'adm-customers': admRenderCustomers,
        'adm-dist': admRenderDist,
        'adm-orders': admRenderOrders,
        'adm-distorders': admRenderDistOrders,
        'adm-payouts': admRenderPayouts,
        'adm-delivery': admRenderDelivery,
        'adm-fulfillment': admRenderFulfillment,
        'adm-analytics': admRenderAnalytics,
        'adm-apihub': admRenderAPIHub,
        'adm-filemanager': admRenderFileManager,
        'adm-settings': admRenderSettings,
    };
    renders[tab]?.();
}

function admRenderOverview() {
    const el = document.getElementById('adm-overview');
    if (!el) return;

    const totalRev = ADM_SELLERS.reduce((s, sl) => s + sl.revenue, 0);
    const activeSell = ADM_SELLERS.filter((s) => s.status === 'Active').length;
    const activeDrv = ADM_DRIVERS.filter((d) => d.status === 'Active' || d.status === 'In Transit').length;
    const totalOrds = ADM_CUSTOMERS.reduce((s, c) => s + c.orders, 0);
    const inTransit = ADM_DRIVERS.filter((d) => d.status === 'In Transit').length;

    const topSellers = [...ADM_SELLERS].sort((a, b) => b.revenue - a.revenue).slice(0, 5);
    const topDrivers = [...ADM_DRIVERS].sort((a, b) => b.deliveries - a.deliveries).slice(0, 5);
    const topCustomers = [...ADM_CUSTOMERS].sort((a, b) => parseMoney(b.spent) - parseMoney(a.spent)).slice(0, 5);
    const medals = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];

    el.innerHTML = `
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-5 mb-6">
            ${admStat('Total Revenue', `$${totalRev.toLocaleString()}`, '💰', 'text-sh-blue', 'bg-sh-blue-lt', 'All time')}
            ${admStat('Active Sellers', activeSell, '🏪', 'text-green-600', 'bg-green-50', '+2 this week')}
            ${admStat('Active Drivers', activeDrv, '🚛', 'text-amber-600', 'bg-amber-50', `On route: ${inTransit}`)}
            ${admStat('Total Customers', ADM_CUSTOMERS.length, '👥', 'text-violet-600', 'bg-violet-50', '+3 this week')}
            ${admStat('Total Orders', totalOrds, '📦', 'text-red-600', 'bg-red-50')}
        </div>
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-4 mb-6">
            ${admStat('Pending Sellers', ADM_SELLERS.filter((s) => s.status === 'Pending').length, '⏳', 'text-amber-600', 'bg-amber-50', 'Awaiting approval')}
            ${admStat('Suspended', ADM_SELLERS.filter((s) => s.status === 'Suspended').length + ADM_DRIVERS.filter((d) => d.status === 'Suspended').length, '🚫', 'text-red-600', 'bg-red-50')}
            ${admStat('Pending Drivers', ADM_DRIVERS.filter((d) => d.status === 'Pending').length, '⏳', 'text-amber-600', 'bg-amber-50')}
            ${admStat('Distributors', ADM_DISTRIBUTORS.length, '🏭', 'text-sh-blue', 'bg-sh-blue-lt', 'Enterprise partners')}
        </div>
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 mb-5">
            <div class="sh-adm-table-wrap">
                <div class="sh-adm-table-head">🏪 Top Sellers by Revenue</div>
                ${topSellers.map((s, i) => `
                    <button type="button" class="sh-adm-list-row w-full text-left" data-adm-action="switch-tab" data-tab="adm-sellers">
                        <div class="flex items-center gap-2"><span>${medals[i]}</span><div><div class="font-semibold text-[13px]">${escapeHtml(s.name)}</div><div class="text-[11px] text-sh-s400">${s.sales} sales</div></div></div>
                        <div class="font-bold text-sh-blue">$${s.revenue.toLocaleString()}</div>
                    </button>`).join('')}
            </div>
            <div class="sh-adm-table-wrap">
                <div class="sh-adm-table-head">🚛 Top Drivers by Deliveries</div>
                ${topDrivers.map((d, i) => `
                    <button type="button" class="sh-adm-list-row w-full text-left" data-adm-action="switch-tab" data-tab="adm-drivers">
                        <div class="flex items-center gap-2"><span>${medals[i]}</span><div><div class="font-semibold text-[13px]">${escapeHtml(d.name)}</div><div class="text-[11px] text-sh-s400">${escapeHtml(d.status)}</div></div></div>
                        <div class="font-bold text-sh-blue">${d.deliveries}</div>
                    </button>`).join('')}
            </div>
            <div class="sh-adm-table-wrap">
                <div class="sh-adm-table-head">👥 Top Customers by Spend</div>
                ${topCustomers.map((c, i) => `
                    <button type="button" class="sh-adm-list-row w-full text-left" data-adm-action="switch-tab" data-tab="adm-customers">
                        <div class="flex items-center gap-2"><span>${medals[i]}</span><div><div class="font-semibold text-[13px]">${escapeHtml(c.name)}</div><div class="text-[11px] text-sh-s400">${c.orders} orders</div></div></div>
                        <div class="font-bold text-sh-blue">${escapeHtml(c.spent)}</div>
                    </button>`).join('')}
            </div>
        </div>
        <div class="sh-adm-crm-strip">
            <div>
                <div class="sh-adm-crm-title">🔄 CRS Delivery Network — Live</div>
                <div class="text-[12px] text-white/50">Competitive Relocation Service · (877) 385-2919</div>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="sh-adm-crm-stat"><div class="text-[22px] font-black text-green-400">${inTransit}</div><div class="text-[10px] text-white/45">In Transit</div></div>
                <div class="sh-adm-crm-stat"><div class="text-[22px] font-black text-amber-300">${ADM_DRIVERS.filter((d) => d.status === 'Active').length}</div><div class="text-[10px] text-white/45">Active Drivers</div></div>
                <div class="sh-adm-crm-stat"><div class="text-[22px] font-black text-blue-300">${ADM_DRIVERS.reduce((s, d) => s + d.deliveries, 0)}</div><div class="text-[10px] text-white/45">Total Deliveries</div></div>
                <a href="${escapeHtml(config.crm_url ?? '#')}" target="_blank" rel="noopener" class="sh-adm-btn sh-adm-btn-orange">Open CRM →</a>
            </div>
        </div>`;
}

function sellerActions(s) {
    let btns = `<button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-gray" data-adm-action="view-seller" data-id="${escapeHtml(s.id)}">View</button>`;
    if (s.status === 'Pending') btns += `<button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-green" data-adm-action="approve-seller" data-id="${escapeHtml(s.id)}">Approve</button>`;
    if (s.status === 'Active') btns += `<button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-amber" data-adm-action="suspend-seller" data-id="${escapeHtml(s.id)}">Suspend</button>`;
    if (s.status === 'Suspended') btns += `<button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-blue" data-adm-action="reinstate-seller" data-id="${escapeHtml(s.id)}">Reinstate</button>`;
    return btns;
}

function driverActions(d) {
    let btns = `<button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-gray" data-adm-action="view-driver" data-id="${escapeHtml(d.id)}">View</button>`;
    if (d.status === 'Pending') btns += `<button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-green" data-adm-action="approve-driver" data-id="${escapeHtml(d.id)}">Approve</button>`;
    if (d.status === 'Active' || d.status === 'In Transit') btns += `<button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-amber" data-adm-action="suspend-driver" data-id="${escapeHtml(d.id)}">Suspend</button>`;
    if (d.status === 'Suspended') btns += `<button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-blue" data-adm-action="reinstate-driver" data-id="${escapeHtml(d.id)}">Reinstate</button>`;
    return btns;
}

function admRenderSellers() {
    const el = document.getElementById('adm-sellers');
    if (!el) return;
    const totalRev = ADM_SELLERS.reduce((s, sl) => s + sl.revenue, 0);

    el.innerHTML = `
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-4 mb-5">
            ${admStat('Total Sellers', ADM_SELLERS.length, '🏪', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('Active', ADM_SELLERS.filter((s) => s.status === 'Active').length, '✅', 'text-green-600', 'bg-green-50')}
            ${admStat('Pending Approval', ADM_SELLERS.filter((s) => s.status === 'Pending').length, '⏳', 'text-amber-600', 'bg-amber-50', 'Needs review')}
            ${admStat('Total Revenue', `$${totalRev.toLocaleString()}`, '💰', 'text-violet-600', 'bg-violet-50')}
        </div>
        <div class="sh-adm-table-wrap overflow-x-auto">
            <div class="sh-adm-table-head sh-adm-sellers-grid">ID · Seller · Contact · Type · Products · Sales · Revenue · Stripe · Actions</div>
            ${ADM_SELLERS.map((s) => `
                <div class="sh-adm-table-row sh-adm-sellers-grid">
                    <span class="font-mono text-[11px] font-bold text-sh-blue">${escapeHtml(s.id)}</span>
                    <div><div class="font-bold text-[13px]">${escapeHtml(s.name)}</div><div class="text-[11px] text-sh-s400">📍 ${escapeHtml(s.city)} · ${escapeHtml(s.joined)}</div>${admStatusBadge(s.status)}</div>
                    <div><div class="text-[12px]">${escapeHtml(s.email)}</div><div class="text-[11px] text-sh-s400">${escapeHtml(s.phone)}</div></div>
                    <span class="text-[11px]">${escapeHtml(s.type)}</span>
                    <span class="font-semibold text-center">${s.products}</span>
                    <span class="font-semibold text-center">${s.sales}</span>
                    <span class="font-bold text-sh-blue">$${s.revenue.toLocaleString()}</span>
                    <span>${admStatusBadge(s.stripe)}</span>
                    <div class="flex flex-wrap gap-1">${sellerActions(s)}</div>
                </div>`).join('')}
        </div>`;
}

function admRenderDrivers() {
    const el = document.getElementById('adm-drivers');
    if (!el) return;
    el.innerHTML = `
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-5 mb-5">
            ${admStat('Total Drivers', ADM_DRIVERS.length, '🚛', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('Active', ADM_DRIVERS.filter((d) => d.status === 'Active').length, '✅', 'text-green-600', 'bg-green-50')}
            ${admStat('In Transit', ADM_DRIVERS.filter((d) => d.status === 'In Transit').length, '🔄', 'text-amber-600', 'bg-amber-50', 'Live now')}
            ${admStat('Pending', ADM_DRIVERS.filter((d) => d.status === 'Pending').length, '⏳', 'text-violet-600', 'bg-violet-50')}
            ${admStat('Total Deliveries', ADM_DRIVERS.reduce((s, d) => s + d.deliveries, 0), '📦', 'text-red-600', 'bg-red-50')}
        </div>
        <div class="sh-adm-table-wrap overflow-x-auto">
            <div class="sh-adm-table-head sh-adm-drivers-grid">ID · Driver · Email · Company · Status · Trucks · Deliv. · Rating · Payout · Actions</div>
            ${ADM_DRIVERS.map((d) => {
                const ratingColor = d.rating >= 4.5 ? 'text-green-600' : d.rating >= 3.5 ? 'text-amber-600' : 'text-red-600';
                return `
                <div class="sh-adm-table-row sh-adm-drivers-grid">
                    <span class="font-mono text-[11px] font-bold text-sh-blue">${escapeHtml(d.id)}</span>
                    <div><div class="font-bold text-[13px]">${escapeHtml(d.name)}</div><div class="text-[11px] text-sh-s400">📍 ${escapeHtml(d.city)} · ${escapeHtml(d.mc)}</div><span class="sh-adm-badge ${d.insurance === 'Active' ? 'sh-adm-badge-active' : 'sh-adm-badge-banned'}">Ins: ${escapeHtml(d.insurance)}</span></div>
                    <div><div class="text-[12px]">${escapeHtml(d.email)}</div><div class="text-[11px] text-sh-s400">${escapeHtml(d.phone)}</div></div>
                    <div><div class="text-[12px] font-semibold">${escapeHtml(d.company)}</div><div class="text-[10px] text-sh-s400">${escapeHtml(d.dot)}</div></div>
                    ${admStatusBadge(d.status)}
                    <span class="font-semibold text-center">${d.trucks}</span>
                    <span class="font-bold text-center text-sh-blue">${d.deliveries}</span>
                    <span class="font-bold ${ratingColor}">${d.rating > 0 ? `★${d.rating}` : '—'}</span>
                    <span class="text-[11px]">${escapeHtml(d.payout)}</span>
                    <div class="flex flex-wrap gap-1">${driverActions(d)}</div>
                </div>`;
            }).join('')}
        </div>`;
}

function admRenderCustomers() {
    const el = document.getElementById('adm-customers');
    if (!el) return;
    const totalSpent = ADM_CUSTOMERS.reduce((s, c) => s + parseMoney(c.spent), 0);
    const totalOrds = ADM_CUSTOMERS.reduce((s, c) => s + c.orders, 0);

    el.innerHTML = `
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-4 mb-5">
            ${admStat('Total Customers', ADM_CUSTOMERS.length, '👥', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('Active', ADM_CUSTOMERS.filter((c) => c.status === 'Active').length, '✅', 'text-green-600', 'bg-green-50')}
            ${admStat('Total Orders', totalOrds, '📦', 'text-amber-600', 'bg-amber-50')}
            ${admStat('Total Spent', `$${Math.round(totalSpent).toLocaleString()}`, '💳', 'text-violet-600', 'bg-violet-50', 'All time')}
        </div>
        <div class="sh-adm-table-wrap overflow-x-auto">
            <div class="sh-adm-table-head sh-adm-customers-grid">ID · Customer · Contact · Type · Status · Orders · Spent · Last Order</div>
            ${ADM_CUSTOMERS.map((c) => `
                <div class="sh-adm-table-row sh-adm-customers-grid">
                    <span class="font-mono text-[11px] font-bold text-sh-blue">${escapeHtml(c.id)}</span>
                    <div><div class="font-bold text-[13px]">${escapeHtml(c.name)}</div><div class="text-[11px] text-sh-s400">📍 ${escapeHtml(c.city)} · ${escapeHtml(c.joined)}</div></div>
                    <div><div class="text-[12px]">${escapeHtml(c.email)}</div><div class="text-[11px] text-sh-s400">${escapeHtml(c.phone)}</div></div>
                    <span class="text-[12px]">${escapeHtml(c.type)}</span>
                    ${admStatusBadge(c.status)}
                    <span class="font-bold text-center text-sh-blue">${c.orders}</span>
                    <span class="font-bold text-green-600">${escapeHtml(c.spent)}</span>
                    <span class="text-[12px]">${escapeHtml(c.last_order)}</span>
                </div>`).join('')}
        </div>`;
}

function admRenderDist() {
    const el = document.getElementById('adm-dist');
    if (!el) return;
    const totalStock = ADM_INVENTORY.reduce((s, i) => s + i.qty, 0);

    el.innerHTML = `
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3 mb-5">
            ${admStat('Distribution Companies', ADM_DISTRIBUTORS.length, '🏭', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('Total SKUs', ADM_INVENTORY.length, '📦', 'text-green-600', 'bg-green-50', 'In system')}
            ${admStat('Total Stock', totalStock, '🏗', 'text-amber-600', 'bg-amber-50', 'units')}
        </div>
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            ${ADM_DISTRIBUTORS.map((co) => {
                const bank = ADM_COMPANY_BANKS[co.id] ?? {};
                const items = ADM_INVENTORY.filter((i) => i.company_id === co.id);
                const stock = items.reduce((s, i) => s + i.qty, 0);
                return `
                <div class="sh-adm-table-wrap overflow-hidden">
                    <div class="bg-gradient-to-br from-sh-navy to-sh-blue px-4 py-4 flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-[10px] bg-white/15 text-xl">🏭</div>
                        <div><div class="text-base font-black text-white">${escapeHtml(co.name)}</div><div class="text-[11px] text-white/50">${escapeHtml(co.location)} · ${escapeHtml(co.tier)}</div></div>
                    </div>
                    ${[
                        ['Contact Email', co.contact_email ?? '—'],
                        ['SKUs in system', `${items.length} products`],
                        ['Total stock', `${stock} units`],
                        ['Bank', bank.bank ?? '—'],
                        ['Account', `${bank.acct ?? '—'} (${bank.method ?? 'ACH'})`],
                        ['API connected', co.api_connected ? '✅ Yes' : '❌ No'],
                        ['Status', '✅ Verified'],
                    ].map(([k, v]) => `<div class="flex justify-between border-b border-sh-s50 px-4 py-2 text-[13px]"><span class="text-sh-s500">${escapeHtml(k)}</span><span class="font-semibold">${escapeHtml(String(v))}</span></div>`).join('')}
                    <div class="flex gap-2 p-3">
                        <a href="${escapeHtml(config.distribute_url ?? '/distribute')}" class="sh-adm-btn sh-adm-btn-blue flex-1 text-center">View Portal →</a>
                        <button type="button" class="sh-adm-btn sh-adm-btn-green flex-1" data-adm-action="init-payout" data-company="${escapeHtml(co.name)}">💸 Payout</button>
                    </div>
                </div>`;
            }).join('')}
        </div>`;
}

function admRenderOrders() {
    const el = document.getElementById('adm-orders');
    if (!el) return;
    el.innerHTML = `
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-4 mb-5">
            ${admStat('Total Orders', ADM_ORDERS.length, '📦', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('Delivered', ADM_ORDERS.filter((o) => o.status === 'DELIVERED').length, '✅', 'text-green-600', 'bg-green-50')}
            ${admStat('In Transit', ADM_ORDERS.filter((o) => o.status === 'IN_TRANSIT').length, '🔄', 'text-amber-600', 'bg-amber-50')}
            ${admStat('Processing', ADM_ORDERS.filter((o) => o.status === 'PROCESSING').length, '⏳', 'text-violet-600', 'bg-violet-50')}
        </div>
        <div class="sh-adm-table-wrap overflow-x-auto">
            <div class="sh-adm-table-head sh-adm-orders-grid">Order # · Product · Customer · Qty · Total · Delivery · Payout</div>
            ${ADM_ORDERS.map((o) => `
                <div class="sh-adm-table-row sh-adm-orders-grid">
                    <span class="font-mono text-[11px] font-bold text-sh-blue">${escapeHtml(o.id)}</span>
                    <div><div class="font-semibold text-[13px]">${escapeHtml(o.product)}</div><div class="text-[11px] text-sh-s400">${escapeHtml(o.date)}</div></div>
                    <span class="text-[12px]">${escapeHtml(o.customer)}</span>
                    <span class="font-semibold text-center">${o.qty}</span>
                    <span class="font-bold text-sh-blue">$${o.total}</span>
                    ${admStatusBadge(o.status)}
                    ${admStatusBadge(o.payout_status)}
                </div>`).join('')}
        </div>`;
}

function admRenderDistOrders() {
    const el = document.getElementById('adm-distorders');
    if (!el) return;
    const totalValue = ADM_ORDER_LEDGER.reduce((s, r) => s + r.total_paid, 0);
    const byCompany = {};
    ADM_ORDER_LEDGER.forEach((r) => {
        if (!byCompany[r.source_company]) byCompany[r.source_company] = { count: 0, value: 0 };
        byCompany[r.source_company].count += 1;
        byCompany[r.source_company].value += r.total_paid;
    });

    el.innerHTML = `
        <div class="sh-adm-confidential-banner mb-5">
            <div class="text-lg shrink-0">🔒</div>
            <div><div class="font-bold text-white">Distribution Sourcing Ledger — Admin Eyes Only</div><div class="text-[12px] text-sh-s400 mt-0.5">Shows exactly which distributor supplied each seller import. Never visible to sellers.</div></div>
        </div>
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-5 mb-5">
            ${admStat('Total Orders', ADM_ORDER_LEDGER.length, '🏭', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('Fulfilled', ADM_ORDER_LEDGER.filter((r) => r.status === 'FULFILLED').length, '✅', 'text-green-600', 'bg-green-50')}
            ${admStat('In Transit', ADM_ORDER_LEDGER.filter((r) => r.status === 'IN_TRANSIT').length, '🔄', 'text-amber-600', 'bg-amber-50')}
            ${admStat('Pending', ADM_ORDER_LEDGER.filter((r) => r.status === 'PENDING_FULFILLMENT').length, '⏳', 'text-violet-600', 'bg-violet-50', 'Needs action')}
            ${admStat('Total Value', `$${totalValue.toLocaleString()}`, '💰', 'text-red-600', 'bg-red-50')}
        </div>
        <div class="grid grid-cols-1 gap-3.5 lg:grid-cols-3 mb-5">
            ${Object.entries(byCompany).map(([co, data]) => {
                const bank = Object.values(ADM_COMPANY_BANKS).find((b) => b.name === co) ?? {};
                return `
                <div class="sh-adm-table-wrap overflow-hidden">
                    <div class="bg-sh-blue px-4 py-3.5"><div class="font-bold text-white">${escapeHtml(co)}</div><div class="text-[11px] text-white/55">${data.count} orders · $${Math.round(data.value).toLocaleString()} total</div></div>
                    <div class="p-3 text-[12px] space-y-1">
                        <div class="flex justify-between"><span class="text-sh-s500">Bank</span><strong>${escapeHtml(bank.bank ?? '—')}</strong></div>
                        <div class="flex justify-between"><span class="text-sh-s500">Account</span><strong class="font-mono">${escapeHtml(bank.acct ?? '—')}</strong></div>
                    </div>
                    <div class="px-3 pb-3"><button type="button" class="sh-adm-btn sh-adm-btn-blue sh-adm-btn-full" data-adm-action="init-payout" data-company="${escapeHtml(co)}">💸 Initiate Payout</button></div>
                </div>`;
            }).join('')}
        </div>
        <div class="sh-adm-table-wrap overflow-x-auto">
            <div class="sh-adm-table-head sh-adm-ledger-grid">Ledger · Order · Seller · Product · Source · Qty · Value · Status · Payout</div>
            ${ADM_ORDER_LEDGER.map((r) => `
                <div class="sh-adm-table-row sh-adm-ledger-grid">
                    <span class="font-mono text-[11px] font-bold text-sh-blue">${escapeHtml(r.ledger_id)}</span>
                    <span class="font-mono text-[11px]">${escapeHtml(r.order_id)}</span>
                    <span class="text-[12px]">${escapeHtml(r.seller_name)}</span>
                    <span class="text-[12px]">${escapeHtml(r.product_name)}</span>
                    <span class="text-[12px]">${escapeHtml(r.source_company)}</span>
                    <span class="text-center font-semibold">${r.qty}</span>
                    <span class="font-bold text-sh-blue">$${Number(r.total_paid).toFixed(2)}</span>
                    ${admStatusBadge(r.status)}
                    ${admStatusBadge(r.payout_status)}
                </div>`).join('')}
        </div>`;
}

function admRenderPayouts() {
    const el = document.getElementById('adm-payouts');
    if (!el) return;
    const sellerRevenue = ADM_SELLERS.reduce((s, sl) => s + sl.revenue, 0);
    const driverEarnings = ADM_DRIVERS.reduce((s, d) => s + parseMoney(d.earnings), 0);
    const crsTotal = ADM_ORDERS.reduce((s, o) => s + o.total * 0.03, 0);
    const shPlatform = Math.round(sellerRevenue * 0.10);

    el.innerHTML = `
        <div class="sh-adm-policy-banner mb-5">
            <div class="text-[26px] shrink-0">🔒</div>
            <div>
                <div class="font-black text-white text-lg mb-1">Delivery-Confirmed Payout Policy</div>
                <p class="text-[13px] text-white/75 leading-relaxed">All payouts are triggered automatically when CRS marks delivery as <strong class="text-green-400">DELIVERED</strong>. Sellers paid every <strong class="text-green-400">Friday</strong>.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-4 mb-5">
            ${admStat('Seller Payouts', `$${sellerRevenue.toLocaleString()}`, '🏪', 'text-green-600', 'bg-green-50', '100% of base price')}
            ${admStat('Driver Earnings', `$${Math.round(driverEarnings).toLocaleString()}`, '🚛', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('CRS Delivery', `$${Math.round(crsTotal).toLocaleString()}`, '🔄', 'text-amber-600', 'bg-amber-50', '3% per delivery')}
            ${admStat('Platform Net', `$${shPlatform.toLocaleString()}`, '💰', 'text-violet-600', 'bg-violet-50', '10% SH fee')}
        </div>
        <div class="sh-adm-table-wrap">
            <div class="sh-adm-table-head">📊 Recent Payout History</div>
            ${ADM_PAYOUTS.map((p) => `
                <div class="sh-adm-list-row">
                    <div><div class="font-semibold text-[13px]">${escapeHtml(p.id)}</div><div class="text-[11px] text-sh-s400">${escapeHtml(p.date)} · ${escapeHtml(p.method)} · Ref: ${escapeHtml(p.ref)}</div></div>
                    <div class="flex items-center gap-3"><span class="font-bold text-green-600">$${p.amount.toFixed(2)}</span>${admStatusBadge(p.status)}</div>
                </div>`).join('')}
        </div>`;
}

function admRenderDelivery() {
    const el = document.getElementById('adm-delivery');
    if (!el) return;
    const inTransit = ADM_DRIVERS.filter((d) => d.status === 'In Transit');

    el.innerHTML = `
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-4 mb-5">
            ${admStat('Active Routes', inTransit.length, '🚛', 'text-amber-600', 'bg-amber-50', 'Live now')}
            ${admStat('Drivers Available', ADM_DRIVERS.filter((d) => d.status === 'Active').length, '✅', 'text-green-600', 'bg-green-50')}
            ${admStat('Total Delivered', ADM_DRIVERS.reduce((s, d) => s + d.deliveries, 0), '📦', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('CRS Integration', 'Live', '🔄', 'text-green-600', 'bg-green-50', 'Webhook active')}
        </div>
        ${inTransit.length ? `
            <div class="font-bold text-[15px] mb-3">🔴 Live Deliveries Right Now</div>
            <div class="grid grid-cols-1 gap-3.5 lg:grid-cols-2 mb-5">
                ${inTransit.map((d) => `
                    <div class="sh-adm-card p-4">
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-amber-200 bg-amber-50 text-lg">🚛</div>
                            <div class="flex-1"><div class="font-bold text-[13px]">${escapeHtml(d.name)}</div><div class="text-[11px] text-sh-s400">${escapeHtml(d.company)}</div></div>
                            ${admStatusBadge('In Transit')}
                        </div>
                        <div class="h-1.5 bg-sh-s100 rounded overflow-hidden mb-2"><div class="h-full w-[65%] bg-gradient-to-r from-sh-blue to-sh-orange rounded"></div></div>
                        <div class="text-[12px] text-sh-s500">${d.deliveries} total deliveries · ★${d.rating}</div>
                    </div>`).join('')}
            </div>` : '<div class="sh-adm-empty mb-5">No active deliveries right now</div>'}
        <div class="font-bold text-[15px] mb-3">Carrier Integrations</div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            ${ADM_DELIVERY.map((c) => `
                <div class="sh-adm-card p-3.5 text-center">
                    <div class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-[9px] text-sm font-bold text-white" style="background:${escapeHtml(c.color)}">${escapeHtml(c.carrier[0])}</div>
                    <div class="font-bold text-[12px] mb-1">${escapeHtml(c.carrier)}</div>
                    ${admStatusBadge(c.status)}
                </div>`).join('')}
        </div>`;
}

function admRenderFulfillment() {
    const el = document.getElementById('adm-fulfillment');
    if (!el) return;
    const pending = ADM_ORDER_LEDGER.filter((r) => r.status === 'PENDING_FULFILLMENT').length;
    const filtered = ffFilter === 'all' ? ADM_ORDER_LEDGER : ADM_ORDER_LEDGER.filter((r) => r.status === ffFilter);

    el.innerHTML = `
        <div class="sh-adm-fulfill-header mb-5">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-sh-orange to-orange-700 text-[22px]">📦</div>
                <div><div class="text-[26px] font-black text-white">Fulfillment Source Routing</div><div class="text-[12px] text-white/45">Admin only · Maps every seller order to its distribution source</div></div>
            </div>
            <div class="sh-adm-confidential-tag">🔒 CONFIDENTIAL</div>
        </div>
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-4 mb-5">
            ${admStat('Total Orders', ADM_ORDER_LEDGER.length, '📦', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('Pending Fulfillment', pending, '⏳', 'text-amber-600', 'bg-amber-50', pending > 0 ? 'Action needed' : 'All clear')}
            ${admStat('In Transit', ADM_ORDER_LEDGER.filter((r) => r.status === 'IN_TRANSIT').length, '🚛', 'text-violet-600', 'bg-violet-50')}
            ${admStat('Total Sourced', `$${ADM_ORDER_LEDGER.reduce((s, r) => s + r.wholesale * r.qty, 0).toLocaleString()}`, '💰', 'text-green-600', 'bg-green-50')}
        </div>
        <div class="flex flex-wrap gap-2 mb-4">
            <select id="ff-filter" class="sh-adm-input max-w-[200px]">
                <option value="all" ${ffFilter === 'all' ? 'selected' : ''}>All statuses</option>
                <option value="PENDING_FULFILLMENT" ${ffFilter === 'PENDING_FULFILLMENT' ? 'selected' : ''}>Pending</option>
                <option value="IN_TRANSIT" ${ffFilter === 'IN_TRANSIT' ? 'selected' : ''}>In Transit</option>
                <option value="FULFILLED" ${ffFilter === 'FULFILLED' ? 'selected' : ''}>Fulfilled</option>
            </select>
            <button type="button" class="sh-adm-btn sh-adm-btn-blue" data-adm-action="refresh-fulfillment">🔄 Refresh</button>
        </div>
        <div class="sh-adm-table-wrap overflow-x-auto">
            <div class="sh-adm-table-head sh-adm-ledger-grid">Ledger · Order · Seller · Product · Source · Qty · Wholesale · Status · Action</div>
            ${filtered.map((r) => `
                <div class="sh-adm-table-row sh-adm-ledger-grid">
                    <span class="font-mono text-[11px] font-bold text-sh-blue">${escapeHtml(r.ledger_id)}</span>
                    <span class="font-mono text-[11px]">${escapeHtml(r.order_id)}</span>
                    <span class="text-[12px]">${escapeHtml(r.seller_name)}</span>
                    <span class="text-[12px]">${escapeHtml(r.product_name)}</span>
                    <span class="text-[12px]">${escapeHtml(r.source_company)}</span>
                    <span class="text-center font-semibold">${r.qty}</span>
                    <span class="font-bold">$${(r.wholesale * r.qty).toFixed(2)}</span>
                    ${admStatusBadge(r.status)}
                    <div>${r.status === 'PENDING_FULFILLMENT' ? `<button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-green" data-adm-action="fulfill-order" data-id="${escapeHtml(r.ledger_id)}">Fulfill</button>` : '—'}</div>
                </div>`).join('')}
        </div>`;

    document.getElementById('ff-filter')?.addEventListener('change', (e) => {
        ffFilter = e.target.value;
        admRenderFulfillment();
    });
}

function admRenderAnalytics() {
    const el = document.getElementById('adm-analytics');
    if (!el) return;
    const totalRev = ADM_SELLERS.reduce((s, sl) => s + sl.revenue, 0);
    const totalOrd = ADM_CUSTOMERS.reduce((s, c) => s + c.orders, 0);
    const rated = ADM_SELLERS.filter((s) => s.rating > 0);
    const avgRating = rated.length ? (rated.reduce((s, sl) => s + sl.rating, 0) / rated.length).toFixed(1) : '0';

    const cats = {};
    ADM_INVENTORY.forEach((i) => { cats[i.category] = (cats[i.category] || 0) + i.qty; });
    const maxQty = Math.max(...Object.values(cats), 1);

    const types = {};
    ADM_SELLERS.forEach((s) => { types[s.type] = (types[s.type] || 0) + 1; });
    const maxType = Math.max(...Object.values(types), 1);

    el.innerHTML = `
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-4 mb-6">
            ${admStat('Platform Revenue', `$${totalRev.toLocaleString()}`, '💰', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('Avg Order Value', `$${Math.round(totalRev / (totalOrd || 1)).toLocaleString()}`, '📊', 'text-green-600', 'bg-green-50')}
            ${admStat('Seller Conversion', `${Math.round(ADM_SELLERS.filter((s) => s.sales > 0).length / ADM_SELLERS.length * 100)}%`, '📈', 'text-amber-600', 'bg-amber-50')}
            ${admStat('Avg Rating', `${avgRating} ★`, '⭐', 'text-violet-600', 'bg-violet-50')}
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div class="sh-adm-card p-5">
                <div class="font-bold text-[14px] mb-4">📦 Inventory by Category</div>
                ${Object.entries(cats).map(([cat, units]) => {
                    const pct = Math.round(units / maxQty * 100);
                    return `<div class="mb-3"><div class="flex justify-between text-[13px] mb-1"><span>${escapeHtml(cat)}</span><span class="font-bold text-sh-blue">${units} units</span></div><div class="h-2 bg-sh-s100 rounded overflow-hidden"><div class="h-full bg-gradient-to-r from-sh-blue to-sh-orange rounded" style="width:${pct}%"></div></div></div>`;
                }).join('')}
            </div>
            <div class="sh-adm-card p-5">
                <div class="font-bold text-[14px] mb-4">🏪 Sellers by Type</div>
                ${Object.entries(types).map(([type, count]) => {
                    const pct = Math.round(count / maxType * 100);
                    return `<div class="mb-3"><div class="flex justify-between text-[13px] mb-1"><span>${escapeHtml(type)}</span><span class="font-bold text-green-600">${count} sellers</span></div><div class="h-2 bg-sh-s100 rounded overflow-hidden"><div class="h-full bg-gradient-to-r from-green-600 to-green-400 rounded" style="width:${pct}%"></div></div></div>`;
                }).join('')}
            </div>
        </div>`;
}

function admRenderShopProds() {
    const el = document.getElementById('adm-shopprods');
    if (!el) return;
    const editProd = ADM_SHOP_EDIT_ID !== null ? ADM_SHOP_PRODUCTS.find((p) => p.id === ADM_SHOP_EDIT_ID) : null;

    el.innerHTML = `
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-[340px_1fr]">
            <div class="sh-adm-card p-5">
                <div class="font-bold text-[15px] mb-4">${editProd ? '✏️ Edit Product' : '+ Add Shop Product'}</div>
                <div class="flex flex-col gap-2.5">
                    <div><label class="sh-adm-label">Product Name *</label><input id="asp-name" class="sh-adm-input" value="${escapeHtml(editProd?.name ?? '')}"></div>
                    <div><label class="sh-adm-label">Seller</label><input id="asp-seller" class="sh-adm-input" value="${escapeHtml(editProd?.seller ?? 'SmooveHub Official')}"></div>
                    <div class="grid grid-cols-2 gap-2">
                        <div><label class="sh-adm-label">Price</label><input id="asp-price" type="number" class="sh-adm-input" value="${editProd?.price ?? ''}"></div>
                        <div><label class="sh-adm-label">Qty</label><input id="asp-qty" type="number" class="sh-adm-input" value="${editProd?.qty ?? ''}"></div>
                    </div>
                    <div><label class="sh-adm-label">Category</label><input id="asp-cat" class="sh-adm-input" value="${escapeHtml(editProd?.category ?? 'Living')}"></div>
                    <button type="button" class="sh-adm-btn sh-adm-btn-blue sh-adm-btn-full" data-adm-action="save-shop-prod">${editProd ? 'Update Product' : 'Add Product'}</button>
                    ${editProd ? '<button type="button" class="sh-adm-btn sh-adm-btn-outline sh-adm-btn-full" data-adm-action="cancel-edit-prod">Cancel Edit</button>' : ''}
                </div>
            </div>
            <div class="sh-adm-table-wrap overflow-x-auto">
                <div class="sh-adm-table-head sh-adm-shop-grid">ID · Product · Seller · Category · Price · Qty · Status · Actions</div>
                ${ADM_SHOP_PRODUCTS.map((p) => `
                    <div class="sh-adm-table-row sh-adm-shop-grid">
                        <span class="font-mono text-[11px] font-bold text-sh-blue">${p.id}</span>
                        <span class="font-semibold">${escapeHtml(p.name)}</span>
                        <span class="text-[12px]">${escapeHtml(p.seller)}</span>
                        <span class="text-[12px]">${escapeHtml(p.category)}</span>
                        <span class="font-bold text-sh-blue">$${p.price}</span>
                        <span class="font-semibold text-center">${p.qty}</span>
                        ${admStatusBadge(p.status)}
                        <div class="flex gap-1">
                            <button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-blue" data-adm-action="edit-shop-prod" data-id="${p.id}">Edit</button>
                            <button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-red" data-adm-action="remove-shop-prod" data-id="${p.id}">Del</button>
                        </div>
                    </div>`).join('')}
            </div>
        </div>`;
}

function admRenderAPIHub() {
    const el = document.getElementById('adm-apihub');
    if (!el) return;
    const adminKey = `sk_admin_${(ADM_USER ?? 'admin').split('@')[0]}_master`;

    el.innerHTML = `
        <div class="sh-adm-api-hero mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-[13px] bg-gradient-to-br from-red-600 to-red-800 text-2xl">🔌</div>
                <div><div class="text-2xl font-black text-white">API Hub</div><div class="text-[13px] text-white/50">All platform endpoints in one place</div></div>
            </div>
            <div class="rounded-lg bg-white/8 p-3 mb-2">
                <div class="text-[11px] uppercase tracking-wide text-white/45 mb-1">Admin Master Key</div>
                <code class="text-[13px] text-blue-300 break-all">${escapeHtml(adminKey)}</code>
                <button type="button" class="mt-2 sh-adm-btn sh-adm-btn-ghost sh-adm-btn-sm" data-adm-action="copy-key" data-key="${escapeHtml(adminKey)}">📋 Copy Key</button>
            </div>
        </div>
        ${ADM_API_SECTIONS.map((section) => `
            <div class="sh-adm-table-wrap mb-5 overflow-hidden">
                <div class="flex items-center gap-3 border-b border-sh-s200 px-5 py-3.5" style="background:${section.color}15">
                    <div class="flex h-9 w-9 items-center justify-center rounded-[10px] text-lg text-white" style="background:${escapeHtml(section.color)}">${section.icon}</div>
                    <div class="font-black text-[18px]">${escapeHtml(section.title)}</div>
                </div>
                ${section.endpoints.map((ep) => `
                    <div class="flex flex-col gap-2 border-b border-sh-s50 px-4 py-3 sm:flex-row sm:items-center">
                        <span class="sh-adm-method-${ep.method.toLowerCase()} w-fit">${ep.method}</span>
                        <code class="flex-1 font-mono text-[12px] font-bold">${escapeHtml(ep.path)}</code>
                        <span class="text-[12px] text-sh-s500">${escapeHtml(ep.desc)}</span>
                    </div>`).join('')}
            </div>`).join('')}`;
}

function admRenderFileManager() {
    const el = document.getElementById('adm-filemanager');
    if (!el) return;

    el.innerHTML = `
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3 mb-5">
            ${admStat('Total Files', ADM_FILE_RECORDS.length, '🗂', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${admStat('Delivery Photos', ADM_FILE_RECORDS.filter((f) => f.type.includes('Photo')).length, '📷', 'text-green-600', 'bg-green-50')}
            ${admStat('Customer Records', ADM_FILE_RECORDS.filter((f) => f.type.includes('ID') || f.type.includes('BOL')).length, '📄', 'text-violet-600', 'bg-violet-50')}
        </div>
        <div class="sh-adm-table-wrap">
            <div class="sh-adm-table-head sh-adm-files-grid">ID · Type · Order · Customer · Date · Size · Actions</div>
            ${ADM_FILE_RECORDS.map((f) => `
                <div class="sh-adm-table-row sh-adm-files-grid">
                    <span class="font-mono text-[11px] font-bold text-sh-blue">${escapeHtml(f.id)}</span>
                    <span class="text-[12px]">${escapeHtml(f.type)}</span>
                    <span class="font-mono text-[11px]">${escapeHtml(f.order)}</span>
                    <span class="text-[12px]">${escapeHtml(f.customer)}</span>
                    <span class="text-[12px]">${escapeHtml(f.date)}</span>
                    <span class="text-[12px]">${escapeHtml(f.size)}</span>
                    <button type="button" class="sh-adm-mini-btn sh-adm-mini-btn-blue" data-adm-action="view-file" data-id="${escapeHtml(f.id)}">View</button>
                </div>`).join('')}
        </div>`;
}

function admRenderSettings() {
    const el = document.getElementById('adm-settings');
    if (!el) return;

    el.innerHTML = `
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div class="sh-adm-card p-5">
                <div class="font-bold text-[15px] mb-4">🔐 Admin Accounts</div>
                ${Object.keys(ADM_CREDENTIALS).map((em) => `
                    <div class="flex items-center justify-between border-b border-sh-s50 py-2.5">
                        <div><div class="font-semibold text-[13px]">${escapeHtml(em)}</div><div class="text-[11px] text-sh-s400">Platform Administrator</div></div>
                        ${admStatusBadge('Active')}
                    </div>`).join('')}
                <button type="button" class="sh-adm-btn sh-adm-btn-blue mt-4" data-adm-action="add-admin">+ Add Admin User</button>
            </div>
            <div class="sh-adm-card p-5">
                <div class="font-bold text-[15px] mb-4">🛡 Platform Security</div>
                ${['JWT Authentication', 'OAuth 2.0', 'AES-256 Encryption', 'PCI-DSS Compliant', 'SSL/TLS Active', 'Rate Limiting', 'Role-Based Access Control'].map((s) => `
                    <div class="flex items-center gap-2 border-b border-sh-s50 py-2 text-[13px]"><span class="text-green-600">✓</span>${escapeHtml(s)}</div>`).join('')}
            </div>
        </div>`;
}

async function adminLogin() {
    const em = document.getElementById('adm-email')?.value.trim().toLowerCase() ?? '';
    const pw = document.getElementById('adm-pass')?.value ?? '';
    const err = document.getElementById('adm-err');
    const submit = document.querySelector('[data-adm-action="login"]');

    if (!em || !pw) {
        if (err) { err.textContent = 'Enter email and password.'; err.classList.remove('hidden'); }
        return;
    }

    submit?.setAttribute('disabled', 'disabled');

    try {
        const user = await portalLogin(em, pw);

        if (!userHasAnyRole(user, ADMIN_ROLES)) {
            await portalLogout();
            if (err) {
                err.textContent = 'This account is not an admin.';
                err.classList.remove('hidden');
            }
            showToast('Access denied', 'Admin role required.', 'error');
            return;
        }

        err?.classList.add('hidden');
        ADM_USER = user.email ?? em;
        persistSession();
        const nameEl = document.getElementById('adm-user-name');
        if (nameEl) nameEl.textContent = (user.name || ADM_USER).split('@')[0];
        showPortal();
        admTab('adm-overview');
        showToast('Welcome back!', 'Admin portal access granted.', 'success');
    } catch (error) {
        if (err) {
            err.textContent = portalAuthErrorMessage(error);
            err.classList.remove('hidden');
        }
        showToast('Sign in failed', portalAuthErrorMessage(error), 'error');
    } finally {
        submit?.removeAttribute('disabled');
    }
}

async function adminLogout() {
    await portalLogout();
    ADM_USER = null;
    persistSession();
    hidePortal();
    showToast('Signed out', 'You have been logged out of the admin portal.', 'success');
}

function showAdminForgot() {
    const email = document.getElementById('adm-email')?.value.trim().toLowerCase() ?? '';
    const wrap = document.getElementById('adm-forgot-wrap');
    if (!email || !ADM_CREDENTIALS[email]) {
        showToast('Invalid email', 'Enter a registered admin email first.', 'error');
        return;
    }
    const code = Math.floor(100000 + Math.random() * 900000);
    if (wrap) {
        wrap.classList.remove('hidden');
        wrap.innerHTML = `Reset code sent to <strong>${escapeHtml(email)}</strong>: <strong>${code}</strong> (demo — expires in 15 min)`;
    }
    showToast('Reset code sent', 'Check your email for the verification code.', 'success');
}

async function restoreSession() {
    const apiUser = getCurrentUser() || await restorePortalUser();

    if (userHasAnyRole(apiUser, ADMIN_ROLES)) {
        ADM_USER = apiUser.email ?? 'admin@smoovehub.com';
        persistSession();
        const nameEl = document.getElementById('adm-user-name');
        if (nameEl) nameEl.textContent = (apiUser.name || ADM_USER).split('@')[0];
        showPortal();
        admTab('adm-overview');
        return true;
    }

    localStorage.removeItem(STORAGE_SESSION);
    return false;
}

function handleAdmAction(action, target) {
    const id = target?.dataset?.id;
    const tab = target?.dataset?.tab;
    const company = target?.dataset?.company;
    const key = target?.dataset?.key;

    switch (action) {
        case 'login': adminLogin(); break;
        case 'logout': adminLogout(); break;
        case 'fill-demo':
            if (document.getElementById('adm-email')) document.getElementById('adm-email').value = 'admin@smoovehub.com';
            if (document.getElementById('adm-pass')) document.getElementById('adm-pass').value = 'Admin2025!';
            break;
        case 'forgot-password': showAdminForgot(); break;
        case 'switch-tab': if (tab) admTab(tab); break;
        case 'approve-seller': {
            const s = ADM_SELLERS.find((x) => x.id === id);
            if (s) { s.status = 'Active'; admRenderSellers(); showToast(`${s.name} approved!`, 'Seller is now live on SmooveHub.', 'success'); }
            break;
        }
        case 'suspend-seller': {
            const s = ADM_SELLERS.find((x) => x.id === id);
            if (s) { s.status = 'Suspended'; admRenderSellers(); showToast('Seller suspended', 'Account has been suspended.', 'success'); }
            break;
        }
        case 'reinstate-seller': {
            const s = ADM_SELLERS.find((x) => x.id === id);
            if (s) { s.status = 'Active'; admRenderSellers(); showToast(`${s.name} reinstated!`, 'Seller account is active again.', 'success'); }
            break;
        }
        case 'view-seller': {
            const s = ADM_SELLERS.find((x) => x.id === id);
            if (s) showToast(s.name, `${s.email} · ${s.city} · Revenue $${s.revenue.toLocaleString()} · Stripe ${s.stripe}`, 'success');
            break;
        }
        case 'approve-driver': {
            const d = ADM_DRIVERS.find((x) => x.id === id);
            if (d) { d.status = 'Active'; admRenderDrivers(); showToast(`${d.name} approved!`, 'Driver is now active in the CRS network.', 'success'); }
            break;
        }
        case 'suspend-driver': {
            const d = ADM_DRIVERS.find((x) => x.id === id);
            if (d) { d.status = 'Suspended'; admRenderDrivers(); showToast('Driver suspended', 'Account suspended pending review.', 'success'); }
            break;
        }
        case 'reinstate-driver': {
            const d = ADM_DRIVERS.find((x) => x.id === id);
            if (d) { d.status = 'Active'; admRenderDrivers(); showToast(`${d.name} reinstated!`, 'Driver account is active again.', 'success'); }
            break;
        }
        case 'view-driver': {
            const d = ADM_DRIVERS.find((x) => x.id === id);
            if (d) showToast(d.name, `${d.company} · ${d.deliveries} deliveries · ★${d.rating || 'N/A'}`, 'success');
            break;
        }
        case 'init-payout':
            showToast('Payout initiated!', `Supplier payout triggered for ${company}.`, 'success');
            break;
        case 'fulfill-order': {
            const r = ADM_ORDER_LEDGER.find((x) => x.ledger_id === id);
            if (r) { r.status = 'IN_TRANSIT'; admRenderFulfillment(); showToast('Order fulfilled', `${r.product_name} marked in transit.`, 'success'); }
            break;
        }
        case 'refresh-fulfillment':
            ffFilter = document.getElementById('ff-filter')?.value ?? 'all';
            admRenderFulfillment();
            showToast('Refreshed', 'Fulfillment ledger updated.', 'success');
            break;
        case 'save-shop-prod': {
            const name = document.getElementById('asp-name')?.value.trim() ?? '';
            const seller = document.getElementById('asp-seller')?.value.trim() ?? 'SmooveHub Official';
            const price = parseFloat(document.getElementById('asp-price')?.value ?? '0');
            const qty = parseInt(document.getElementById('asp-qty')?.value ?? '0', 10);
            const category = document.getElementById('asp-cat')?.value.trim() ?? 'Living';
            if (!name || price <= 0) { showToast('Fill required fields', 'Name and price are required.', 'error'); return; }
            if (ADM_SHOP_EDIT_ID !== null) {
                const p = ADM_SHOP_PRODUCTS.find((x) => x.id === ADM_SHOP_EDIT_ID);
                if (p) Object.assign(p, { name, seller, price, qty, category });
                ADM_SHOP_EDIT_ID = null;
            } else {
                ADM_SHOP_PRODUCTS.unshift({ id: Date.now(), name, seller, category, condition: 'BRAND_NEW', price, qty, status: 'Active' });
            }
            persistShopProducts();
            admRenderShopProds();
            showToast('Product saved!', `${name} is now in the shop catalog.`, 'success');
            break;
        }
        case 'edit-shop-prod':
            ADM_SHOP_EDIT_ID = parseInt(id, 10);
            admRenderShopProds();
            break;
        case 'cancel-edit-prod':
            ADM_SHOP_EDIT_ID = null;
            admRenderShopProds();
            break;
        case 'remove-shop-prod': {
            ADM_SHOP_PRODUCTS = ADM_SHOP_PRODUCTS.filter((p) => p.id !== parseInt(id, 10));
            persistShopProducts();
            admRenderShopProds();
            showToast('Product removed', 'Listing has been deleted.', 'success');
            break;
        }
        case 'copy-key':
            navigator.clipboard?.writeText(key ?? '');
            showToast('Copied!', 'API key copied to clipboard.', 'success');
            break;
        case 'view-file':
            showToast('File opened', `Viewing ${id} (demo).`, 'success');
            break;
        case 'add-admin':
            showToast('Admin added (demo)', 'New admin user would be created in production.', 'success');
            break;
        default: break;
    }
}

function bindEvents() {
    document.querySelectorAll('[data-adm-tab]').forEach((btn) => {
        btn.addEventListener('click', () => admTab(btn.dataset.admTab));
    });

    document.addEventListener('click', (event) => {
        const actionEl = event.target.closest('[data-adm-action]');
        if (!actionEl || !document.getElementById('admin-portal-config')) return;
        const inPortal = actionEl.closest('#admin-main') || actionEl.closest('#admin-login-overlay');
        if (!inPortal) return;
        event.preventDefault();
        handleAdmAction(actionEl.dataset.admAction, actionEl);
    });

    document.getElementById('adm-pass')?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') adminLogin();
    });

    window.addEventListener('smoovehub:auth-updated', () => {
        if (!ADM_USER) restoreSession();
    });
}

export function initAdminPortal() {
    if (!loadConfig()) return;
    bindEvents();
    Promise.resolve(restoreSession()).then((ok) => {
        if (!ok) hidePortal();
    });
}
