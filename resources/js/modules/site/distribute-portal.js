import { getCurrentUser } from '../auth/auth.js';
import {
    portalAuthErrorMessage,
    portalLogin,
    portalLogout,
    restorePortalUser,
    userHasAnyRole,
} from '../auth/portal-auth.js';
import { showToast } from '../landing/toast.js';
import { hideOverlayModal, showOverlayModal, showPortalDashboard, showPortalLogin } from './portal-screen.js';

const DIST_ROLES = ['distribution-partner'];

const STORAGE_SESSION = 'smoovehub_dist_session';
const STORAGE_INVENTORY = 'smoovehub_dist_inventory';
const STORAGE_ACCOUNTS = 'smoovehub_dist_accounts';
const STORAGE_LEADBOARD = 'crs_leadboard';
const STORAGE_LOADBOARD = 'crs_loadboard';

let config = {};
let DIST_INVENTORY = [];
let DIST_ORDERS = [];
let DIST_PAYOUTS_DATA = [];
let DIST_DELIVERY_INTEGRATIONS = [];
let DIST_ACCOUNTS = {};
let DIST_CRS_PRICING = {};
let DIST_USER = null;
let DIST_BANK = null;
let DIST_CARD = null;
let DIST_API_KEYS = { primary: '', webhook: '' };
let DIST_ACTIVE_TAB = 'overview';
let DIST_CSV_PREVIEW = [];
let DIST_ACTIVE_PAYOUT_TAB = 'bank';
let dqActiveTab = 'quote';
let dqLastQuote = null;
let homeUrl = '/';

const DIST_SH_MARKUP = 0.40;
const DIST_SELLER_MARKUP = 0.08;

function loadConfig() {
    const el = document.getElementById('distribute-portal-config');
    if (!el) {
        return false;
    }

    config = JSON.parse(el.textContent || '{}');
    DIST_ORDERS = [...(config.orders ?? [])];
    DIST_PAYOUTS_DATA = [...(config.payouts ?? [])];
    DIST_DELIVERY_INTEGRATIONS = [...(config.delivery_integrations ?? [])];
    DIST_ACCOUNTS = { ...(config.demo_accounts ?? {}) };
    DIST_CRS_PRICING = config.crs_pricing ?? {};
    homeUrl = config.home_url ?? '/';

    const savedAccounts = localStorage.getItem(STORAGE_ACCOUNTS);
    if (savedAccounts) {
        try {
            DIST_ACCOUNTS = { ...DIST_ACCOUNTS, ...JSON.parse(savedAccounts) };
        } catch {
            /* ignore */
        }
    }

    const savedInventory = localStorage.getItem(STORAGE_INVENTORY);
    if (savedInventory) {
        try {
            DIST_INVENTORY = JSON.parse(savedInventory);
        } catch {
            DIST_INVENTORY = [...(config.inventory ?? [])];
        }
    } else {
        DIST_INVENTORY = [...(config.inventory ?? [])];
    }

    const savedKeys = localStorage.getItem('smoovehub_dist_api_keys');
    if (savedKeys) {
        try {
            DIST_API_KEYS = JSON.parse(savedKeys);
        } catch {
            DIST_API_KEYS = {
                primary: `sk_dist_${Math.random().toString(36).slice(2, 18)}`,
                webhook: `whk_${Math.random().toString(36).slice(2, 18)}`,
            };
        }
    } else {
        DIST_API_KEYS = {
            primary: `sk_dist_${Math.random().toString(36).slice(2, 18)}`,
            webhook: `whk_${Math.random().toString(36).slice(2, 18)}`,
        };
        persistApiKeys();
    }

    return true;
}

function persistInventory() {
    localStorage.setItem(STORAGE_INVENTORY, JSON.stringify(DIST_INVENTORY));
}

function persistAccounts() {
    localStorage.setItem(STORAGE_ACCOUNTS, JSON.stringify(
        Object.fromEntries(
            Object.entries(DIST_ACCOUNTS).filter(([email]) => !config.demo_accounts?.[email])
        )
    ));
}

function persistApiKeys() {
    localStorage.setItem('smoovehub_dist_api_keys', JSON.stringify(DIST_API_KEYS));
}

function persistSession() {
    if (DIST_USER) {
        localStorage.setItem(STORAGE_SESSION, JSON.stringify(DIST_USER));
    } else {
        localStorage.removeItem(STORAGE_SESSION);
    }
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function distCalcPricing(supplierPrice, condition = 'BRAND_NEW') {
    const wholesale = +Number(supplierPrice).toFixed(2);
    const shMarkupRate = condition === 'BRAND_NEW' ? DIST_SH_MARKUP : 0.10;
    const shMarkupAmt = +(wholesale * shMarkupRate).toFixed(2);
    const shInternal = +(wholesale + shMarkupAmt).toFixed(2);
    const sellerMarkupAmt = +(shInternal * DIST_SELLER_MARKUP).toFixed(2);
    const sellerPrice = +(shInternal + sellerMarkupAmt).toFixed(2);

    return { price: sellerPrice, wholesale, shInternal, sellerMarkupAmt };
}

function statCard(label, val, icon, colorClass, bgClass) {
    return `<div class="sh-dist-stat-card">
        <div class="flex items-center gap-2.5 mb-2.5">
            <div class="sh-dist-stat-icon ${bgClass}">${icon}</div>
            <div class="text-[12px] font-semibold uppercase tracking-wide text-sh-s400">${escapeHtml(label)}</div>
        </div>
        <div class="sh-dist-stat-value ${colorClass}">${escapeHtml(val)}</div>
    </div>`;
}

function statusColor(status) {
    const map = { DELIVERED: 'text-green-600', IN_TRANSIT: 'text-sh-blue', PROCESSING: 'text-amber-600', PENDING: 'text-sh-s400', PAID: 'text-green-600' };
    return map[status] ?? 'text-sh-s400';
}

function qtyColor(qty) {
    if (qty <= 5) return 'text-red-600';
    if (qty <= 15) return 'text-amber-600';
    return 'text-green-600';
}

function showPortal() {
    showPortalDashboard('dist-login-overlay', 'dist-main-content');
}

function hidePortal() {
    showPortalLogin('dist-login-overlay', 'dist-main-content');
}

function updateUserDisplay() {
    const company = DIST_USER?.company ?? '—';
    const role = DIST_USER?.role ?? '—';
    const initials = DIST_USER?.initials ?? '?';

    ['dist-user-display', 'dist-sidebar-name'].forEach((id) => {
        const el = document.getElementById(id);
        if (el) el.textContent = company;
    });

    const roleEl = document.getElementById('dist-user-role');
    if (roleEl) roleEl.textContent = role;

    const avatar = document.getElementById('dist-avatar-letter');
    if (avatar) avatar.textContent = initials;
}

function distSwitchTab(tab) {
    DIST_ACTIVE_TAB = tab;

    document.querySelectorAll('[data-dist-tab]').forEach((el) => {
        el.classList.toggle('sh-dist-nav-item-active', el.dataset.distTab === tab);
    });

    document.querySelectorAll('.sh-dist-panel').forEach((panel) => {
        panel.classList.toggle('hidden', panel.id !== `dp-${tab}`);
    });

    const renders = {
        overview: distRenderOverview,
        inventory: distRenderInventory,
        upload: distRenderUpload,
        orders: distRenderOrders,
        payout: distRenderPayout,
        delivery: distRenderDelivery,
        dquote: distRenderDeliveryQuote,
        api: distRenderAPI,
        analytics: distRenderAnalytics,
        settings: distRenderSettings,
    };

    renders[tab]?.();
}

function distRenderOverview() {
    const el = document.getElementById('dp-overview');
    if (!el) return;

    const totalRevenue = DIST_ORDERS.reduce((s, o) => s + o.total, 0);
    const pendingPay = DIST_PAYOUTS_DATA.filter((p) => p.status !== 'PAID').reduce((s, p) => s + p.amount, 0);
    const activeInv = DIST_INVENTORY.reduce((s, i) => s + i.qty, 0);

    el.innerHTML = `
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-7">
            ${statCard('Total Revenue', `$${totalRevenue.toLocaleString()}`, '📦', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${statCard('Active Inventory', `${activeInv} units`, '🏭', 'text-green-600', 'bg-green-50')}
            ${statCard('Pending Payout', `$${pendingPay.toFixed(2)}`, '💰', 'text-amber-600', 'bg-amber-50')}
            ${statCard('Total Orders', String(DIST_ORDERS.length), '🛒', 'text-violet-600', 'bg-violet-50')}
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mb-6">
            <div class="sh-dist-card">
                <div class="sh-dist-card-head">📋 Recent Orders</div>
                ${DIST_ORDERS.slice(0, 4).map((o) => `
                    <div class="sh-dist-list-row">
                        <div>
                            <div class="font-semibold text-sh-s900">${escapeHtml(o.id)}</div>
                            <div class="text-[11px] text-sh-s400 mt-0.5">${escapeHtml(o.product.substring(0, 30))}…</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-sh-s900">$${o.total}</div>
                            <div class="text-[11px] font-semibold ${statusColor(o.status)}">${escapeHtml(o.status.replace('_', ' '))}</div>
                        </div>
                    </div>
                `).join('')}
            </div>
            <div class="sh-dist-card">
                <div class="sh-dist-card-head">🏭 Inventory Snapshot</div>
                ${DIST_INVENTORY.slice(0, 4).map((item) => {
                    const p = distCalcPricing(item.supplier_price);
                    return `
                        <div class="sh-dist-list-row">
                            <div>
                                <div class="font-semibold text-sh-s900">${escapeHtml(item.name.substring(0, 28))}…</div>
                                <div class="text-[11px] text-sh-s400">${escapeHtml(item.sku)}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold ${qtyColor(item.qty)}">${item.qty} units</div>
                                <div class="text-[11px] text-sh-s400">Reseller: $${p.price}</div>
                            </div>
                        </div>`;
                }).join('')}
            </div>
        </div>
        <div class="sh-dist-crm-strip">
            <div>
                <div class="sh-dist-crm-title">🔄 CRS Integration Active</div>
                <div class="text-[12px] text-white/50 mt-0.5">Competitive Relocation Service CRM · Real-time sync</div>
            </div>
            <div class="flex flex-wrap gap-2.5">
                <a href="${escapeHtml(config.crm_url ?? '#')}" target="_blank" rel="noopener" class="sh-dist-btn sh-dist-btn-orange">Open CRM Portal →</a>
                <button type="button" class="sh-dist-btn sh-dist-btn-ghost" data-dist-action="switch-tab" data-tab="delivery">Delivery Dashboard</button>
            </div>
        </div>`;
}

function distRenderInventory() {
    const el = document.getElementById('dp-inventory');
    if (!el) return;

    el.innerHTML = `
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4">
            <div>
                <h3 class="sh-dist-page-title">Warehouse Inventory</h3>
                <p class="text-[13px] text-sh-s500 mt-0.5">${DIST_INVENTORY.length} products · Supplier prices visible to sellers only</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="sh-dist-btn sh-dist-btn-orange" data-dist-action="switch-tab" data-tab="upload">+ Upload Inventory</button>
                <button type="button" class="sh-dist-btn sh-dist-btn-blue" data-dist-action="add-item-prompt">+ Add Single Item</button>
            </div>
        </div>
        <div class="sh-dist-table-wrap overflow-x-auto">
            <div class="sh-dist-table-head sh-dist-inv-grid">
                <span>SKU</span><span>Product</span><span>Category</span><span>Supplier $</span><span>Reseller $</span><span>Warehouse</span><span>Qty</span><span>ETA</span><span>Actions</span>
            </div>
            ${DIST_INVENTORY.map((item, idx) => {
                const p = distCalcPricing(item.supplier_price);
                const skuShort = item.sku.split('-').slice(1).join('-') || item.sku;
                return `
                    <div class="sh-dist-table-row sh-dist-inv-grid">
                        <span class="font-mono text-[11px] font-bold text-sh-blue">${escapeHtml(skuShort)}</span>
                        <div>
                            <div class="font-semibold text-sh-s900">${escapeHtml(item.name)}</div>
                            <div class="text-[11px] text-sh-s400 mt-0.5">${escapeHtml(item.brand)} · ${escapeHtml(item.material)} · ${escapeHtml(item.color)}</div>
                        </div>
                        <span class="sh-dist-pill">${escapeHtml(item.category)}</span>
                        <span class="font-bold text-green-600">$${item.supplier_price}</span>
                        <span class="font-bold text-amber-600">$${p.price}</span>
                        <span class="text-[11px] text-sh-s600">${escapeHtml(item.warehouse)}</span>
                        <span class="font-bold ${qtyColor(item.qty)}">${item.qty}</span>
                        <span class="text-[11px] text-sh-s500">${escapeHtml(item.eta)}</span>
                        <div class="flex gap-1">
                            <button type="button" class="sh-dist-mini-btn sh-dist-mini-btn-blue" data-dist-action="edit-item" data-idx="${idx}">Edit</button>
                            <button type="button" class="sh-dist-mini-btn sh-dist-mini-btn-red" data-dist-action="delete-item" data-idx="${idx}">Del</button>
                        </div>
                    </div>`;
            }).join('')}
        </div>`;
}

function distRenderUpload() {
    const el = document.getElementById('dp-upload');
    if (!el) return;

    const categories = (config.categories ?? []).map((c) => `<option>${escapeHtml(c)}</option>`).join('');

    el.innerHTML = `
        <h3 class="sh-dist-page-title mb-1">Inventory Upload Center</h3>
        <p class="text-[13px] text-sh-s500 mb-6">Upload via CSV/XLSX, manual entry, or sync via Open API</p>
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 mb-7">
            <div class="sh-dist-card p-5">
                <div class="text-[28px] mb-2.5">📊</div>
                <div class="font-bold text-sh-s900 mb-1">CSV / XLSX Upload</div>
                <p class="text-[12px] text-sh-s500 mb-4 leading-relaxed">Bulk upload your product catalog using a spreadsheet. Download our template to get started.</p>
                <button type="button" id="csv-drop-zone" class="sh-dist-drop-zone mb-3" data-dist-action="simulate-csv">
                    <div class="text-[28px] mb-1.5">📎</div>
                    <div class="text-[13px] text-sh-s500">Click to upload CSV / XLSX</div>
                    <div class="text-[11px] text-sh-s400 mt-0.5">Max 10MB · 10,000 rows</div>
                </button>
                <button type="button" class="sh-dist-btn sh-dist-btn-outline sh-dist-btn-full" data-dist-action="download-template">⬇ Download CSV Template</button>
            </div>
            <div class="sh-dist-card p-5">
                <div class="text-[28px] mb-2.5">✏️</div>
                <div class="font-bold text-sh-s900 mb-1">Manual Entry</div>
                <p class="text-[12px] text-sh-s500 mb-4 leading-relaxed">Add single products one at a time with full metadata.</p>
                <div class="flex flex-col gap-2">
                    <div><label class="sh-dist-label">Product Name *</label><input id="mu-name" class="sh-dist-input" placeholder="Japandi Dining Table"></div>
                    <div class="grid grid-cols-2 gap-2">
                        <div><label class="sh-dist-label">SKU *</label><input id="mu-sku" class="sh-dist-input" placeholder="SKU-001"></div>
                        <div><label class="sh-dist-label">Category</label><select id="mu-cat" class="sh-dist-input">${categories}</select></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div><label class="sh-dist-label">Supplier Price *</label><input id="mu-price" type="number" class="sh-dist-input" placeholder="0.00"></div>
                        <div><label class="sh-dist-label">Qty *</label><input id="mu-qty" type="number" class="sh-dist-input" placeholder="0"></div>
                    </div>
                    <button type="button" class="sh-dist-btn sh-dist-btn-blue sh-dist-btn-full" data-dist-action="save-manual-item">+ Add to Inventory</button>
                </div>
            </div>
            <div class="sh-dist-card p-5">
                <div class="text-[28px] mb-2.5">🔌</div>
                <div class="font-bold text-sh-s900 mb-1">Open API Sync</div>
                <p class="text-[12px] text-sh-s500 mb-4 leading-relaxed">Connect your ERP or WMS via REST API for real-time sync.</p>
                <div class="rounded-lg border border-sh-s200 bg-sh-s50 p-3 mb-3">
                    <div class="text-[10px] font-bold uppercase tracking-wide text-sh-s400 mb-1.5">Your API Key</div>
                    <div id="up-api-key" class="font-mono text-[12px] font-semibold text-sh-blue break-all">${escapeHtml(DIST_API_KEYS.primary)}</div>
                </div>
                <div class="rounded-lg bg-sh-navy p-3 mb-3">
                    <div class="text-[10px] text-white/40 mb-1">POST /api/v1/inventory/create</div>
                    <div class="font-mono text-[11px] text-green-400 leading-relaxed">curl -X POST https://api.smoovehub.com/v1/ -H "Authorization: Bearer YOUR_KEY"</div>
                </div>
                <button type="button" class="sh-dist-btn sh-dist-btn-orange sh-dist-btn-full" data-dist-action="switch-tab" data-tab="api">View Full API Docs →</button>
            </div>
        </div>
        <div id="csv-preview-area" class="sh-dist-card hidden">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-4 py-3.5 border-b border-sh-s200">
                <div class="font-bold text-[14px] text-sh-s900">📊 CSV Preview — <span id="csv-row-count">0</span> rows detected</div>
                <div class="flex gap-2">
                    <button type="button" class="sh-dist-btn sh-dist-btn-green" data-dist-action="import-csv">✅ Import All</button>
                    <button type="button" class="sh-dist-btn sh-dist-btn-outline" data-dist-action="cancel-csv">Cancel</button>
                </div>
            </div>
            <div id="csv-preview-table" class="overflow-x-auto p-4"></div>
        </div>`;
}

function distRenderOrders() {
    const el = document.getElementById('dp-orders');
    if (!el) return;

    el.innerHTML = `
        <h3 class="sh-dist-page-title mb-4">Order Management</h3>
        <div class="sh-dist-table-wrap overflow-x-auto">
            <div class="sh-dist-table-head sh-dist-orders-grid">
                <span>Order #</span><span>Product</span><span>Reseller</span><span>Date</span><span>Total</span><span>Delivery</span><span>Payout</span>
            </div>
            ${DIST_ORDERS.map((o) => `
                <div class="sh-dist-table-row sh-dist-orders-grid">
                    <span class="font-mono text-[12px] font-bold text-sh-blue">${escapeHtml(o.id)}</span>
                    <div>
                        <div class="font-semibold text-sh-s900">${escapeHtml(o.product)}</div>
                        <div class="text-[11px] text-sh-s400">Qty: ${o.qty}</div>
                    </div>
                    <span class="text-[12px] text-sh-s600">${escapeHtml(o.customer)}</span>
                    <span class="text-[12px] text-sh-s600">${escapeHtml(o.date)}</span>
                    <span class="font-bold text-sh-s900">$${o.total}</span>
                    <span class="text-[11px] font-bold ${statusColor(o.status)}">${escapeHtml(o.status.replace('_', ' '))}</span>
                    <span class="text-[11px] font-bold ${statusColor(o.payout_status)}">${escapeHtml(o.payout_status)}</span>
                </div>
            `).join('')}
        </div>`;
}

function distRenderPayout() {
    const el = document.getElementById('dp-payout');
    if (!el) return;

    const totalPaid = DIST_PAYOUTS_DATA.filter((p) => p.status === 'PAID').reduce((s, p) => s + p.amount, 0);
    const totalPend = DIST_PAYOUTS_DATA.filter((p) => p.status !== 'PAID').reduce((s, p) => s + p.amount, 0);
    const methodLabel = DIST_BANK ? 'Bank on file' : DIST_CARD ? 'Card on file' : 'Not set';

    const methodBlock = !DIST_BANK && !DIST_CARD
        ? `<div class="sh-dist-empty-payout">
            <div class="text-[28px] mb-2">🏦</div>
            <div class="font-semibold text-sh-s600 mb-1">No payout method on file</div>
            <p class="text-[12px] text-sh-s400 mb-3.5">Add your bank account to receive automatic payouts on delivery confirmation.</p>
            <button type="button" class="sh-dist-btn sh-dist-btn-blue sh-dist-btn-sm" data-dist-action="open-payout-modal">+ Add Payout Method</button>
           </div>`
        : `<div class="rounded-lg border border-green-200 bg-green-50 p-3.5 text-green-700 font-semibold">✅ Payout method configured. Funds release automatically on each delivery confirmation.</div>`;

    el.innerHTML = `
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
            <div>
                <h3 class="sh-dist-page-title">Payout Dashboard</h3>
                <p class="text-[13px] text-sh-s500">Triggered on CRS delivery confirmation · PCI-DSS compliant · AES-256 encrypted</p>
            </div>
            <button type="button" class="sh-dist-btn sh-dist-btn-blue" data-dist-action="open-payout-modal">🏦 Manage Payout Methods</button>
        </div>
        <div class="sh-dist-policy-banner mb-5">
            <div class="text-[22px] shrink-0">🔒</div>
            <div>
                <div class="font-bold text-white mb-1">Delivery-Confirmed Payout Policy</div>
                <p class="text-[12px] text-white/70 leading-relaxed mb-2.5">Your supplier cost is released automatically when CRS marks the delivery as <strong class="text-green-400">DELIVERED</strong>. ACH transfers typically clear within <strong class="text-green-400">1–2 business days</strong>.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="sh-dist-policy-pill sh-dist-policy-pill-green">✓ Auto-triggered on CRS webhook</span>
                    <span class="sh-dist-policy-pill sh-dist-policy-pill-green">✓ Simultaneous with seller payout</span>
                    <span class="sh-dist-policy-pill sh-dist-policy-pill-blue">⚡ ACH clears within 1–2 business days</span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3 mb-5">
            ${statCard('Total Paid Out', `$${totalPaid.toLocaleString()}`, '💸', 'text-green-600', 'bg-green-50')}
            ${statCard('Pending Release', `$${totalPend.toFixed(2)}`, '⏳', 'text-amber-600', 'bg-amber-50')}
            ${statCard('Payout Method', methodLabel, '🏦', 'text-sh-blue', 'bg-sh-blue-lt')}
        </div>
        <div class="sh-dist-card p-4 mb-5 bg-sh-s50">
            <div class="text-[11px] font-bold uppercase tracking-wide text-sh-s400 mb-2.5">🔄 When does your payout release?</div>
            <div class="flex flex-wrap items-center gap-1 text-[12px] text-sh-s700">
                <span class="sh-dist-flow-step">1 Seller purchases item</span>
                <span class="text-sh-s400">→</span>
                <span class="sh-dist-flow-step">2 CRS driver picks up</span>
                <span class="text-sh-s400">→</span>
                <span class="sh-dist-flow-step">3 Delivery confirmed</span>
                <span class="text-sh-s400">→</span>
                <span class="sh-dist-flow-step sh-dist-flow-step-green">4 Supplier cost released → ACH</span>
            </div>
        </div>
        <div class="sh-dist-card p-4 mb-5">
            <div class="font-bold text-[13px] text-sh-s900 mb-3.5">💳 Active Payout Method</div>
            ${methodBlock}
        </div>
        <div class="sh-dist-table-wrap">
            <div class="sh-dist-card-head">📊 Payout History</div>
            <div class="sh-dist-table-head sh-dist-payout-grid">
                <span>Payout ID</span><span>Date</span><span>Method</span><span>Orders</span><span>Amount</span><span>Status</span>
            </div>
            ${DIST_PAYOUTS_DATA.map((p) => {
                const badge = p.status === 'PAID' ? 'sh-dist-status-paid' : 'sh-dist-status-pending';
                return `
                    <div class="sh-dist-table-row sh-dist-payout-grid">
                        <span class="font-mono text-[11px] font-bold text-sh-blue">${escapeHtml(p.id)}</span>
                        <span class="text-[12px] text-sh-s600">${escapeHtml(p.date)}</span>
                        <span class="text-[12px] text-sh-s600">${escapeHtml(p.method)}</span>
                        <span class="font-semibold">${p.orders}</span>
                        <span class="font-bold text-sh-s900">$${p.amount.toFixed(2)}</span>
                        <span><span class="${badge}">${escapeHtml(p.status)}</span></span>
                    </div>`;
            }).join('')}
        </div>`;
}

function distRenderDelivery() {
    const el = document.getElementById('dp-delivery');
    if (!el) return;

    const active = DIST_ORDERS.filter((o) => o.status === 'IN_TRANSIT' || o.status === 'PROCESSING');

    el.innerHTML = `
        <h3 class="sh-dist-page-title mb-4">Delivery Integration Hub</h3>
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3 mb-6">
            ${statCard('Active Shipments', '3', '🚛', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${statCard('Delivered (30d)', '12', '✅', 'text-green-600', 'bg-green-50')}
            ${statCard('Avg Delivery Time', '2.4 days', '⏱', 'text-amber-600', 'bg-amber-50')}
        </div>
        <div class="sh-dist-table-wrap mb-5">
            <div class="sh-dist-card-head">🚛 Carrier Integrations</div>
            ${DIST_DELIVERY_INTEGRATIONS.map((c) => {
                const connected = c.status === 'Connected';
                return `
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-4 py-3.5 border-b border-sh-s50">
                        <div class="flex items-center gap-3">
                            <div class="sh-dist-carrier-icon" style="background:${escapeHtml(c.color)}">${escapeHtml(c.carrier[0])}</div>
                            <div>
                                <div class="font-semibold text-[14px] text-sh-s900">${escapeHtml(c.carrier)}</div>
                                <div class="text-[11px] text-sh-s400 font-mono">${c.api_key ? `API: ${escapeHtml(c.api_key)}` : 'Not connected'}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            ${c.tracking_url ? `<a href="${escapeHtml(c.tracking_url)}" target="_blank" rel="noopener" class="text-[12px] font-semibold text-sh-blue">Track →</a>` : ''}
                            <span class="${connected ? 'sh-dist-status-paid' : 'sh-dist-status-pending'}">${escapeHtml(c.status)}</span>
                            ${connected ? '' : `<button type="button" class="sh-dist-mini-btn sh-dist-mini-btn-blue" data-dist-action="connect-carrier" data-carrier="${escapeHtml(c.carrier)}">Connect</button>`}
                        </div>
                    </div>`;
            }).join('')}
        </div>
        <div class="sh-dist-table-wrap">
            <div class="sh-dist-card-head">📍 Active Shipment Tracking</div>
            ${active.length ? active.map((o) => {
                const pct = o.status === 'IN_TRANSIT' ? 70 : 35;
                return `
                    <div class="px-4 py-3.5 border-b border-sh-s50">
                        <div class="flex justify-between mb-2">
                            <div><span class="font-bold text-[13px] text-sh-s900">${escapeHtml(o.id)}</span><span class="text-[12px] text-sh-s400 ml-2">${escapeHtml(o.product)}</span></div>
                            <span class="text-[12px] font-semibold text-sh-blue">${escapeHtml(o.status.replace('_', ' '))}</span>
                        </div>
                        <div class="h-1.5 bg-sh-s100 rounded overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-sh-blue to-sh-orange rounded" style="width:${pct}%"></div>
                        </div>
                        <div class="flex justify-between mt-1 text-[11px] text-sh-s400"><span>Order placed</span><span>In transit</span><span>Delivered</span></div>
                    </div>`;
            }).join('') : '<div class="p-6 text-center text-[13px] text-sh-s400">No active shipments right now.</div>'}
        </div>`;
}

function distRenderDeliveryQuote() {
    const el = document.getElementById('dp-dquote');
    if (!el) return;

    const leadboard = JSON.parse(localStorage.getItem(STORAGE_LEADBOARD) || '[]');
    const loadboard = JSON.parse(localStorage.getItem(STORAGE_LOADBOARD) || '[]');

    const tabBtn = (id, label, count, accent) => {
        const active = dqActiveTab === id;
        const badge = count > 0 ? `<span class="sh-dist-dq-badge" style="background:${accent}">${count}</span>` : '';
        return `<button type="button" class="sh-dist-dq-tab ${active ? 'sh-dist-dq-tab-active' : ''}" data-dist-action="dq-tab" data-tab="${id}">${label}${badge}</button>`;
    };

    const header = `<div class="border-b border-sh-s200 mb-6 flex flex-wrap">${tabBtn('quote', '📦 New Quote', 0, '#F97316')}${tabBtn('leadboard', '🔥 Lead Board', leadboard.length, '#F97316')}${tabBtn('loadboard', '🚛 Load Board', loadboard.length, '#16A34A')}</div>`;

    if (dqActiveTab === 'quote') {
        el.innerHTML = header + buildQuoteForm();
        return;
    }

    if (dqActiveTab === 'leadboard') {
        el.innerHTML = header + buildBoardList(leadboard, 'lead');
        return;
    }

    el.innerHTML = header + buildBoardList(loadboard, 'load');
}

function buildQuoteForm() {
    return `
        <div class="max-w-3xl">
            <div class="mb-5">
                <h2 class="text-xl font-extrabold text-sh-s900 mb-1">📦 Delivery Quote</h2>
                <p class="text-[13px] text-sh-s500">Generate an itemized CRS delivery quote. Unpaid quotes go to the Lead Board. Paid jobs go to the Load Board.</p>
            </div>
            <div class="sh-dist-card p-5 mb-4">
                <div class="font-bold text-[13px] text-sh-s900 mb-3.5">👤 Customer Information</div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 mb-3">
                    <div><label class="sh-dist-label">Customer Name *</label><input id="dq-cust-name" class="sh-dist-input" placeholder="Jane Smith"></div>
                    <div><label class="sh-dist-label">Phone *</label><input id="dq-cust-phone" class="sh-dist-input" placeholder="(214) 555-0100"></div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div><label class="sh-dist-label">Email</label><input id="dq-cust-email" type="email" class="sh-dist-input" placeholder="customer@email.com"></div>
                    <div><label class="sh-dist-label">Move Date</label><input id="dq-move-date" type="date" class="sh-dist-input"></div>
                </div>
            </div>
            <div class="sh-dist-card p-5 mb-4">
                <div class="font-bold text-[13px] text-sh-s900 mb-3.5">📍 Pickup & Delivery Locations</div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 mb-3">
                    <div><label class="sh-dist-label">Pickup Address / City *</label><input id="dq-from" class="sh-dist-input" placeholder="Dallas, TX 75201"></div>
                    <div><label class="sh-dist-label">Delivery Address / City *</label><input id="dq-to" class="sh-dist-input" placeholder="Houston, TX 77001"></div>
                </div>
                <div><label class="sh-dist-label">Items Being Delivered</label><input id="dq-items-desc" class="sh-dist-input" placeholder="e.g. Sofa, Dining Table x4 chairs"></div>
            </div>
            <div class="sh-dist-card p-5 mb-4">
                <div class="font-bold text-[13px] text-sh-s900 mb-3.5">📐 Size & Weight</div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div><label class="sh-dist-label">No. of Items</label><input id="dq-qty" type="number" min="1" value="1" class="sh-dist-input"></div>
                    <div><label class="sh-dist-label">Total Weight (lbs)</label><input id="dq-weight" type="number" min="0" value="0" class="sh-dist-input"></div>
                    <div><label class="sh-dist-label">Approx. Distance (mi)</label><input id="dq-dist" type="number" min="1" value="30" class="sh-dist-input"></div>
                </div>
            </div>
            <div class="sh-dist-card p-5 mb-4">
                <div class="font-bold text-[13px] text-sh-s900 mb-3.5">🏠 Access & Surcharges</div>
                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                    ${dqCheckbox('dq-inhome', 'In-Home Placement', '+$65 flat', '🏠')}
                    ${dqCheckbox('dq-stairs', 'Stairs', '+$75/flight over 3rd', '🪜')}
                    ${dqCheckbox('dq-elevator', 'Elevator', '+$75/floor over 3rd', '🛗')}
                    ${dqCheckbox('dq-longcarry', 'Long Carry (>75ft)', '+$100 flat', '🏃')}
                    ${dqCheckbox('dq-fragile', 'Fragile Items', '+$35', '🫙')}
                    ${dqCheckbox('dq-removal', 'Removal + Delivery', '+$50', '🗑️')}
                </div>
                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-3 mt-2.5">
                    <div><label class="sh-dist-label">Stair flights</label><input id="dq-stair-flights" type="number" min="0" value="0" class="sh-dist-input"></div>
                    <div><label class="sh-dist-label">Elevator floors</label><input id="dq-elev-floors" type="number" min="0" value="0" class="sh-dist-input"></div>
                    <div><label class="sh-dist-label">Waiting time (min)</label><input id="dq-wait-min" type="number" min="0" value="0" class="sh-dist-input"></div>
                </div>
            </div>
            <div class="flex flex-col gap-2.5 sm:flex-row">
                <button type="button" class="sh-dist-btn sh-dist-btn-blue flex-[2] py-3.5" data-dist-action="dq-calculate">⚡ Calculate Quote</button>
                <button type="button" class="sh-dist-btn sh-dist-btn-outline flex-1 py-3.5" data-dist-action="dq-reset">↺ Reset</button>
            </div>
            <div id="dq-result" class="mt-4"></div>
        </div>`;
}

function dqCheckbox(id, label, sub, icon) {
    return `<label class="flex items-center gap-2.5 rounded-lg border-[1.5px] border-sh-s200 p-3 cursor-pointer">
        <input type="checkbox" id="${id}" class="h-4 w-4 shrink-0 cursor-pointer">
        <div><div class="font-semibold text-[13px]">${icon} ${escapeHtml(label)}</div><div class="text-[11px] text-sh-s500">${escapeHtml(sub)}</div></div>
    </label>`;
}

function buildBoardList(board, type) {
    if (!board.length) {
        return `<div class="sh-dist-card p-8 text-center text-[13px] text-sh-s500">No ${type === 'lead' ? 'leads' : 'loads'} on the board yet. Calculate a quote and send it here.</div>`;
    }

    return `<div class="sh-dist-table-wrap">${board.map((q) => `
        <div class="px-4 py-3.5 border-b border-sh-s50">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="font-bold text-sh-s900">${escapeHtml(q.customer)} · $${Number(q.total).toFixed(2)}</div>
                    <div class="text-[12px] text-sh-s500">${escapeHtml(q.from)} → ${escapeHtml(q.to)}</div>
                </div>
                <span class="sh-dist-pill">${escapeHtml(q.board_id ?? q.id ?? '')}</span>
            </div>
        </div>
    `).join('')}</div>`;
}

function distRenderAPI() {
    const el = document.getElementById('dp-api');
    if (!el) return;

    const endpoints = [
        { method: 'POST', path: '/api/v1/inventory/create', desc: 'Create a new inventory item' },
        { method: 'PUT', path: '/api/v1/inventory/update/:id', desc: 'Update existing inventory item' },
        { method: 'DELETE', path: '/api/v1/inventory/remove/:id', desc: 'Remove item from inventory' },
        { method: 'GET', path: '/api/v1/inventory/list', desc: 'List all inventory items' },
        { method: 'GET', path: '/api/v1/orders/list', desc: 'List all orders for distributor' },
        { method: 'POST', path: '/api/v1/payout/create', desc: 'Request a payout' },
    ];

    const methodClass = { GET: 'sh-dist-method-get', POST: 'sh-dist-method-post', PUT: 'sh-dist-method-put', DELETE: 'sh-dist-method-delete' };

    el.innerHTML = `
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between mb-5">
            <div>
                <h3 class="sh-dist-page-title">Open API Documentation</h3>
                <p class="text-[13px] text-sh-s500">REST API · OAuth 2.0 · JSON · Rate limited: 1000 req/hr · v1.0</p>
            </div>
            <button type="button" class="sh-dist-btn sh-dist-btn-outline" data-dist-action="regen-key">🔄 Regenerate API Key</button>
        </div>
        <div class="sh-dist-api-keys mb-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <div class="text-[11px] uppercase tracking-wide text-white/45 mb-1.5">Primary API Key</div>
                    <div id="api-key-display" class="font-mono text-[13px] text-blue-300 break-all">${escapeHtml(DIST_API_KEYS.primary)}</div>
                    <button type="button" class="mt-2 sh-dist-btn sh-dist-btn-ghost sh-dist-btn-sm" data-dist-action="copy-key">📋 Copy Key</button>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wide text-white/45 mb-1.5">Webhook Secret</div>
                    <div id="webhook-key-display" class="font-mono text-[13px] text-green-300 break-all">${escapeHtml(DIST_API_KEYS.webhook)}</div>
                    <div class="text-[11px] text-white/35 mt-1.5">Base URL: ${escapeHtml(config.api_base_url ?? 'https://api.smoovehub.com')}</div>
                </div>
            </div>
        </div>
        <div class="sh-dist-table-wrap">
            <div class="sh-dist-card-head">Endpoints</div>
            ${endpoints.map((ep) => `
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center px-4 py-3 border-b border-sh-s50">
                    <span class="${methodClass[ep.method] ?? ''}">${ep.method}</span>
                    <code class="text-[13px] font-mono text-sh-blue flex-1">${escapeHtml(ep.path)}</code>
                    <span class="text-[12px] text-sh-s500">${escapeHtml(ep.desc)}</span>
                </div>
            `).join('')}
        </div>
        <div class="sh-dist-card p-4 mt-5">
            <div class="font-bold text-[13px] text-sh-s900 mb-3">Webhook Configuration</div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div><label class="sh-dist-label">Webhook URL</label><input class="sh-dist-input" placeholder="https://yourapp.com/webhooks/smoovehub"></div>
                <div><label class="sh-dist-label">Events</label><select class="sh-dist-input"><option>All events</option><option>order.delivered</option><option>payout.sent</option></select></div>
            </div>
            <button type="button" class="sh-dist-btn sh-dist-btn-blue mt-3" data-dist-action="save-webhook">Save Webhook</button>
        </div>`;
}

function distRenderAnalytics() {
    const el = document.getElementById('dp-analytics');
    if (!el) return;

    const totalRev = DIST_ORDERS.reduce((s, o) => s + o.total, 0);
    const avgOrder = DIST_ORDERS.length ? Math.round(totalRev / DIST_ORDERS.length) : 0;
    const cats = {};
    DIST_INVENTORY.forEach((i) => { cats[i.category] = (cats[i.category] || 0) + i.qty; });
    const totalUnits = Object.values(cats).reduce((s, v) => s + v, 0);

    el.innerHTML = `
        <h3 class="sh-dist-page-title mb-5">Performance Analytics</h3>
        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-4 mb-6">
            ${statCard('Total Revenue', `$${totalRev.toLocaleString()}`, '📈', 'text-sh-blue', 'bg-sh-blue-lt')}
            ${statCard('Orders Placed', String(DIST_ORDERS.length), '🛒', 'text-green-600', 'bg-green-50')}
            ${statCard('Avg Order Value', `$${avgOrder}`, '💰', 'text-amber-600', 'bg-amber-50')}
            ${statCard('Active SKUs', String(DIST_INVENTORY.length), '🏭', 'text-violet-600', 'bg-violet-50')}
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-[1.5fr_1fr]">
            <div class="sh-dist-card p-5">
                <div class="font-bold text-[14px] text-sh-s900 mb-4">📊 Inventory by Category</div>
                ${Object.entries(cats).map(([cat, units]) => {
                    const pct = totalUnits ? Math.round(units / totalUnits * 100) : 0;
                    return `<div class="mb-3">
                        <div class="flex justify-between text-[13px] mb-1"><span class="font-medium text-sh-s900">${escapeHtml(cat)}</span><span class="font-bold text-sh-s400">${units} units (${pct}%)</span></div>
                        <div class="h-2 bg-sh-s100 rounded overflow-hidden"><div class="h-full bg-gradient-to-r from-sh-blue to-sh-orange rounded" style="width:${pct}%"></div></div>
                    </div>`;
                }).join('')}
            </div>
            <div class="sh-dist-card p-5">
                <div class="font-bold text-[14px] text-sh-s900 mb-4">🏆 Top Selling Items</div>
                ${DIST_ORDERS.slice(0, 5).map((o, i) => {
                    const colors = ['bg-sh-orange', 'bg-sh-blue', 'bg-green-600', 'bg-amber-600', 'bg-violet-600'];
                    return `<div class="flex items-center gap-2.5 mb-2.5">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-black text-white ${colors[i]}">${i + 1}</div>
                        <div class="min-w-0 flex-1"><div class="truncate text-[12px] font-semibold text-sh-s900">${escapeHtml(o.product)}</div><div class="text-[11px] text-sh-s400">$${o.total}</div></div>
                    </div>`;
                }).join('')}
            </div>
        </div>`;
}

function distRenderSettings() {
    const el = document.getElementById('dp-settings');
    if (!el) return;

    el.innerHTML = `
        <h3 class="sh-dist-page-title mb-5">Company Settings</h3>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div class="sh-dist-card p-5">
                <div class="font-bold text-[15px] text-sh-s900 mb-4">🏭 Company Profile</div>
                <div class="mb-3"><label class="sh-dist-label">Company Name</label><input id="set-company-name" class="sh-dist-input" value="${escapeHtml(DIST_USER?.company ?? '')}"></div>
                <div class="mb-3"><label class="sh-dist-label">Contact Email</label><input id="set-company-email" class="sh-dist-input" value="${escapeHtml(DIST_USER?.email ?? '')}"></div>
                <div class="mb-3"><label class="sh-dist-label">Business Phone</label><input class="sh-dist-input" placeholder="(555) 000-0000"></div>
                <div class="mb-4"><label class="sh-dist-label">Business Address</label><input class="sh-dist-input" placeholder="123 Warehouse Dr, Dallas TX 75201"></div>
                <button type="button" class="sh-dist-btn sh-dist-btn-blue" data-dist-action="save-profile">Save Profile</button>
            </div>
            <div class="sh-dist-card p-5">
                <div class="font-bold text-[15px] text-sh-s900 mb-3.5">💰 Pricing & Markup Settings</div>
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-3.5 mb-3.5">
                    <div class="font-semibold text-[13px] text-amber-700 mb-1">⚠️ Pricing Protection Active</div>
                    <p class="text-[12px] text-amber-900 leading-relaxed">Supplier prices are <strong>never visible</strong> to customers. Only authenticated resellers see the reseller markup price.</p>
                </div>
                <div class="mb-4">
                    <label class="sh-dist-label">Reseller Markup Rate</label>
                    <div class="flex items-center gap-2"><input id="set-markup" type="number" class="sh-dist-input max-w-[100px]" value="8" min="0" max="50"><span class="font-semibold text-sh-s600">%</span></div>
                    <p class="text-[11px] text-sh-s400 mt-1">Default: 8% applied to all supplier prices</p>
                </div>
                <button type="button" class="sh-dist-btn sh-dist-btn-orange" data-dist-action="update-markup">Update Markup Rate</button>
            </div>
        </div>`;
}

async function distLogin() {
    const em = document.getElementById('dist-email')?.value.trim().toLowerCase() ?? '';
    const pw = document.getElementById('dist-pass')?.value ?? '';
    const err = document.getElementById('dist-login-err');
    const submit = document.querySelector('[data-dist-action="login"]');

    if (!em || !pw) {
        if (err) { err.textContent = 'Enter email and password.'; err.classList.remove('hidden'); }
        return;
    }

    submit?.setAttribute('disabled', 'disabled');

    try {
        const user = await portalLogin(em, pw);

        if (!userHasAnyRole(user, DIST_ROLES)) {
            await portalLogout();
            if (err) {
                err.textContent = 'This account is not a distribution partner.';
                err.classList.remove('hidden');
            }
            showToast('Access denied', 'Distribution partner role required.', 'error');
            return;
        }

        const demo = DIST_ACCOUNTS[em] ?? {};
        const companyFromApi = user.distribution_company?.company_name
            ?? user.name
            ?? 'Distribution Partner';

        err?.classList.add('hidden');
        DIST_USER = {
            email: user.email ?? em,
            company: demo.company ?? companyFromApi,
            role: demo.role ?? 'Distribution Partner',
            initials: demo.initials ?? user.avatar_initials ?? 'DP',
            tier: demo.tier ?? user.distribution_company?.tier ?? 'Starter',
        };
        persistSession();
        showPortal();
        updateUserDisplay();
        distSwitchTab('overview');
        showToast('Welcome back!', `${DIST_USER.company} 🏭`, 'success');
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

function distRegister() {
    const biz = document.getElementById('dist-reg-biz')?.value.trim() ?? '';
    const em = document.getElementById('dist-reg-em')?.value.trim().toLowerCase() ?? '';
    const pw = document.getElementById('dist-reg-pw')?.value ?? '';
    const err = document.getElementById('dist-reg-err');

    if (!biz || !em || !pw) {
        if (err) { err.textContent = 'Fill all required fields.'; err.classList.remove('hidden'); }
        return;
    }
    if (pw.length < 8) {
        if (err) { err.textContent = 'Password must be 8+ characters.'; err.classList.remove('hidden'); }
        return;
    }
    if (DIST_ACCOUNTS[em]) {
        if (err) { err.textContent = 'Email already registered.'; err.classList.remove('hidden'); }
        return;
    }

    const initials = biz.split(' ').slice(0, 2).map((w) => w[0]?.toUpperCase() ?? '').join('');
    DIST_ACCOUNTS[em] = { pass: pw, company: biz, role: 'Distribution Partner', initials, tier: 'Starter' };
    persistAccounts();
    DIST_USER = { email: em, ...DIST_ACCOUNTS[em] };
    persistSession();
    showPortal();
    updateUserDisplay();
    distSwitchTab('overview');
    showToast('Account created!', 'Welcome to SmooveHub Distribution 🏭', 'success');
}

async function distLogout() {
    await portalLogout();
    DIST_USER = null;
    persistSession();
    hidePortal();
    document.getElementById('dist-login-panel')?.classList.remove('hidden');
    document.getElementById('dist-register-panel')?.classList.add('hidden');
    showToast('Signed out', 'You have been logged out of the distribution portal.', 'success');
}

function distOpenPayoutModal() {
    showOverlayModal('dist-payout-modal');
    distPayoutTab('bank');
}

function distClosePayoutModal() {
    hideOverlayModal('dist-payout-modal');
}

function distPayoutTab(tab) {
    DIST_ACTIVE_PAYOUT_TAB = tab;
    ['bank', 'card'].forEach((t) => {
        document.getElementById(`dpt-${t}`)?.classList.toggle('sh-dist-payout-tab-active', t === tab);
        document.getElementById(`dptp-${t}`)?.classList.toggle('hidden', t !== tab);
    });
}

function distSaveBankPayout() {
    const holder = document.getElementById('dp-bank-holder')?.value.trim() ?? '';
    const acct = document.getElementById('dp-bank-acct')?.value.trim() ?? '';
    const routing = document.getElementById('dp-bank-route')?.value.trim() ?? '';
    const bank = document.getElementById('dp-bank-name')?.value.trim() ?? '';

    if (!holder || !acct || !routing || !bank) {
        showToast('Fill all bank fields', 'All bank fields are required.', 'error');
        return;
    }
    if (!/^\d{9}$/.test(routing)) {
        showToast('Invalid routing number', 'Must be exactly 9 digits.', 'error');
        return;
    }

    DIST_BANK = { holder, account: acct, routing, bank };
    DIST_CARD = null;
    distClosePayoutModal();
    distRenderPayout();
    showToast('Bank account saved!', 'Payouts will be sent via ACH every Friday.', 'success');
}

function distSaveCardPayout() {
    const name = document.getElementById('dp-card-name')?.value.trim() ?? '';
    const num = document.getElementById('dp-card-num')?.value.trim().replace(/\s/g, '') ?? '';
    const exp = document.getElementById('dp-card-exp')?.value.trim() ?? '';
    const cvv = document.getElementById('dp-card-cvv')?.value.trim() ?? '';

    if (!name || !num || !exp || !cvv) {
        showToast('Fill all card fields', 'All card fields are required.', 'error');
        return;
    }

    DIST_CARD = { name, last4: num.slice(-4), exp };
    DIST_BANK = null;
    distClosePayoutModal();
    distRenderPayout();
    showToast('Card saved securely!', 'Tokenized · PCI-DSS compliant.', 'success');
}

function distSaveManualItem() {
    const name = document.getElementById('mu-name')?.value.trim() ?? '';
    const sku = document.getElementById('mu-sku')?.value.trim() ?? '';
    const price = parseFloat(document.getElementById('mu-price')?.value ?? '0');
    const qty = parseInt(document.getElementById('mu-qty')?.value ?? '0', 10);
    const cat = document.getElementById('mu-cat')?.value ?? 'Other';

    if (!name || !sku || price <= 0) {
        showToast('Fill all required fields', 'Name, SKU and price required.', 'error');
        return;
    }

    DIST_INVENTORY.unshift({
        id: `INV-${Date.now().toString(36).toUpperCase()}`,
        sku, upc: '', name, brand: '', category: cat,
        supplier_price: price, qty, warehouse: 'Dallas TX WH-A',
        eta: '3–5 days', condition: 'BRAND_NEW', material: '', color: '',
    });
    persistInventory();
    showToast('Product added!', `${name} is now in your inventory.`, 'success');
    ['mu-name', 'mu-sku', 'mu-price', 'mu-qty'].forEach((id) => {
        const input = document.getElementById(id);
        if (input) input.value = '';
    });
}

function distSimulateCSV() {
    DIST_CSV_PREVIEW = [
        { sku: 'SKU-NEW-001', name: 'Modern Accent Chair', category: 'Living', supplier_price: 220, qty: 25, warehouse: 'Dallas TX WH-A' },
        { sku: 'SKU-NEW-002', name: 'Glass Coffee Table', category: 'Living', supplier_price: 180, qty: 14, warehouse: 'Houston TX WH-B' },
        { sku: 'SKU-NEW-003', name: 'Outdoor Patio Set', category: 'Outdoor', supplier_price: 590, qty: 8, warehouse: 'Atlanta GA WH-C' },
    ];

    const area = document.getElementById('csv-preview-area');
    const cnt = document.getElementById('csv-row-count');
    const tbl = document.getElementById('csv-preview-table');
    if (!area || !cnt || !tbl) return;

    cnt.textContent = String(DIST_CSV_PREVIEW.length);
    tbl.innerHTML = `<table class="w-full border-collapse text-[13px] mt-3">
        <thead><tr class="bg-sh-s50">${['SKU', 'Product Name', 'Category', 'Supplier Price', 'Reseller Price', 'Qty', 'Warehouse'].map((h) => `<th class="px-3 py-2 text-left text-[11px] font-bold uppercase text-sh-s400">${h}</th>`).join('')}</tr></thead>
        <tbody>${DIST_CSV_PREVIEW.map((r) => {
            const p = distCalcPricing(r.supplier_price);
            return `<tr class="border-t border-sh-s50">
                <td class="px-3 py-2 font-mono text-[11px] text-sh-blue">${escapeHtml(r.sku)}</td>
                <td class="px-3 py-2 font-semibold">${escapeHtml(r.name)}</td>
                <td class="px-3 py-2"><span class="sh-dist-pill">${escapeHtml(r.category)}</span></td>
                <td class="px-3 py-2 font-bold text-green-600">$${r.supplier_price}</td>
                <td class="px-3 py-2 font-bold text-amber-600">$${p.price}</td>
                <td class="px-3 py-2 font-semibold">${r.qty}</td>
                <td class="px-3 py-2 text-[12px] text-sh-s600">${escapeHtml(r.warehouse)}</td>
            </tr>`;
        }).join('')}</tbody></table>`;
    area.classList.remove('hidden');
}

function distImportCSV() {
    DIST_CSV_PREVIEW.forEach((r) => {
        DIST_INVENTORY.push({
            id: `INV-${Date.now().toString(36).toUpperCase()}`,
            sku: r.sku, upc: '', name: r.name, brand: '', category: r.category,
            supplier_price: r.supplier_price, qty: r.qty, warehouse: r.warehouse,
            eta: '3–5 days', condition: 'BRAND_NEW', material: '', color: '',
        });
    });
    persistInventory();
    document.getElementById('csv-preview-area')?.classList.add('hidden');
    DIST_CSV_PREVIEW = [];
    showToast('CSV imported!', `${DIST_INVENTORY.length} total products in inventory.`, 'success');
    distSwitchTab('inventory');
}

function distDownloadTemplate() {
    let csv = 'SKU,UPC,Product Name,Brand,Category,Description,Material,Color,Dimensions,Weight (lbs),Supplier Price,Qty,Warehouse,ETA,Assembly Required,Warranty\n';
    csv += 'SKU-001,012345678901,Sample Product,BrandName,Dining,Product description,Wood,Brown,60x30x30,80,250,10,Dallas TX WH-A,3-5 days,true,1 year\n';
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'smoovehub_inventory_template.csv';
    a.click();
    showToast('Template downloaded!', 'Fill in your products and re-upload.', 'success');
}

function distDeleteItem(idx) {
    const item = DIST_INVENTORY[idx];
    if (!item) return;
    DIST_INVENTORY.splice(idx, 1);
    persistInventory();
    distRenderInventory();
    showToast('Item removed', `${item.name} removed from inventory.`, 'success');
}

function distEditItem(idx) {
    const item = DIST_INVENTORY[idx];
    if (!item) return;
    const newQty = prompt(`Update quantity for ${item.name} (current: ${item.qty})`, String(item.qty));
    if (newQty !== null && !Number.isNaN(parseInt(newQty, 10))) {
        DIST_INVENTORY[idx].qty = parseInt(newQty, 10);
        persistInventory();
        distRenderInventory();
        showToast('Stock updated', 'Inventory quantity has been updated.', 'success');
    }
}

function distAddItemPrompt() {
    const name = prompt('Product name:');
    if (!name) return;
    const price = parseFloat(prompt('Supplier price (USD):') || '0');
    const qty = parseInt(prompt('Quantity:') || '0', 10);
    DIST_INVENTORY.push({
        id: `INV-${Date.now().toString(36).toUpperCase()}`,
        sku: `SKU-${Math.random().toString(36).slice(2, 6).toUpperCase()}`,
        upc: '', name, brand: '', category: 'Other',
        supplier_price: price, qty, warehouse: 'Dallas TX WH-A',
        eta: '3–5 days', condition: 'BRAND_NEW', material: '', color: '',
    });
    persistInventory();
    distRenderInventory();
    showToast('Product added', 'New item added to inventory.', 'success');
}

function dqGetVal(id, fallback = '') {
    return document.getElementById(id)?.value || fallback;
}

function dqChecked(id) {
    return document.getElementById(id)?.checked ?? false;
}

function dqCalculate() {
    const name = dqGetVal('dq-cust-name');
    const from = dqGetVal('dq-from');
    const to = dqGetVal('dq-to');

    if (!from || !to) {
        showToast('Missing info', 'Enter pickup and delivery locations.', 'error');
        return;
    }

    const qty = Math.max(1, parseInt(dqGetVal('dq-qty', '1'), 10) || 1);
    const weight = parseFloat(dqGetVal('dq-weight', '0')) || 0;
    const dist = Math.max(1, parseInt(dqGetVal('dq-dist', '30'), 10) || 30);
    const waitMin = parseInt(dqGetVal('dq-wait-min', '0'), 10) || 0;
    const stairFl = parseInt(dqGetVal('dq-stair-flights', '0'), 10) || 0;
    const elevFl = parseInt(dqGetVal('dq-elev-floors', '0'), 10) || 0;

    const baseRates = DIST_CRS_PRICING.crs_base ?? { UNDER_25: 75, UNDER_50: 120, UNDER_100: 190, UNDER_200: 290, OVER_200: 390 };
    const baseRate = dist <= 25 ? baseRates.UNDER_25 : dist <= 50 ? baseRates.UNDER_50 : dist <= 100 ? baseRates.UNDER_100 : dist <= 200 ? baseRates.UNDER_200 : baseRates.OVER_200;
    const base = baseRate * qty;

    const fees = DIST_CRS_PRICING.fees ?? {};
    let sur = 0;
    const surLines = [];

    if (dqChecked('dq-inhome')) { sur += 65; surLines.push({ l: 'In-Home Placement', a: 65 }); }
    if (dqChecked('dq-stairs') && stairFl > 0) {
        const freeFlights = fees.FREE_STAIR_FLIGHTS ?? 3;
        const over = Math.max(0, stairFl - freeFlights);
        if (over > 0) { const sf = over * (fees.STAIRS_OVER_FREE ?? 75); sur += sf; surLines.push({ l: `Stairs (${over} flights over ${freeFlights} free)`, a: sf }); }
    }
    if (dqChecked('dq-elevator') && elevFl > 0) {
        const freeFloors = fees.FREE_ELEV_FLOORS ?? 3;
        const over = Math.max(0, elevFl - freeFloors);
        if (over > 0) { const ef = over * (fees.ELEV_OVER_FREE ?? 75); sur += ef; surLines.push({ l: `Elevator (${over} floors over ${freeFloors} free)`, a: ef }); }
    }
    if (dqChecked('dq-longcarry')) { sur += 100; surLines.push({ l: 'Long Carry (>75ft)', a: 100 }); }
    if (dqChecked('dq-fragile')) { sur += 35; surLines.push({ l: 'Fragile Item Handling', a: 35 }); }
    if (dqChecked('dq-removal')) { sur += 50; surLines.push({ l: 'Removal + Delivery', a: 50 }); }
    if (weight > 500) { const ws = Math.round((weight - 500) * 0.10); sur += ws; surLines.push({ l: `Heavy Item Surcharge (${weight}lbs)`, a: ws }); }
    if (waitMin > 30) { const wt = Math.ceil((waitMin - 30) / 60) * 150; sur += wt; surLines.push({ l: `Waiting Time (${waitMin}min)`, a: wt }); }

    const subtotal = base + sur;
    const procFee = Math.round(subtotal * (fees.PROCESSING ?? 0.03) * 100) / 100;
    const grandTotal = subtotal + procFee;

    dqLastQuote = {
        id: `Q-${Date.now()}`,
        customer: name || 'Customer',
        phone: dqGetVal('dq-cust-phone'),
        email: dqGetVal('dq-cust-email'),
        from, to,
        items: dqGetVal('dq-items-desc'),
        qty, dist, weight, total: grandTotal,
        created: new Date().toISOString(),
    };

    const resultEl = document.getElementById('dq-result');
    if (!resultEl) return;

    resultEl.innerHTML = `
        <div class="sh-dist-quote-result">
            <div class="text-[12px] font-bold uppercase tracking-wide text-white/60 mb-3">📋 CRS DELIVERY QUOTE — ${escapeHtml((name || 'Customer').toUpperCase())}</div>
            <div class="text-[11px] text-white/50 mb-2.5">From: <strong class="text-white">${escapeHtml(from)}</strong> → To: <strong class="text-white">${escapeHtml(to)}</strong></div>
            <div class="flex justify-between py-2 border-b border-white/10 text-[13px]"><span>Base delivery (${qty} item${qty > 1 ? 's' : ''}, ~${dist} mi)</span><span class="font-semibold">$${base.toFixed(2)}</span></div>
            ${surLines.map((s) => `<div class="flex justify-between py-1.5 border-b border-white/8 text-[12px] text-white/80"><span>${escapeHtml(s.l)}</span><span>${s.a > 0 ? `+$${s.a.toFixed(2)}` : 'FREE'}</span></div>`).join('')}
            <div class="flex justify-between py-1.5 border-b border-white/8 text-[12px] text-white/80"><span>SmooveHub processing (3%)</span><span>+$${procFee.toFixed(2)}</span></div>
            <div class="flex justify-between pt-3 text-lg font-black"><span>TOTAL ESTIMATE</span><span class="text-amber-200 text-2xl">$${grandTotal.toFixed(2)}</span></div>
        </div>
        <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 mt-3">
            <button type="button" class="sh-dist-dq-action sh-dist-dq-action-lead" data-dist-action="dq-lead">🔥 Send to Lead Board<div class="text-[11px] font-medium opacity-80">Unpaid — awaiting payment</div></button>
            <button type="button" class="sh-dist-dq-action sh-dist-dq-action-load" data-dist-action="dq-load">🚛 Send to Load Board<div class="text-[11px] font-medium opacity-80">Paid & booked — dispatch job</div></button>
        </div>`;
    resultEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function dqSendToBoard(board) {
    if (!dqLastQuote) {
        showToast('No quote', 'Calculate a quote first.', 'error');
        return;
    }

    const q = { ...dqLastQuote, board_id: `${board === 'lead' ? 'LB' : 'LD'}-${Date.now()}`, status: board === 'lead' ? 'unpaid' : 'paid' };
    const key = board === 'lead' ? STORAGE_LEADBOARD : STORAGE_LOADBOARD;
    const list = JSON.parse(localStorage.getItem(key) || '[]');
    list.unshift(q);
    localStorage.setItem(key, JSON.stringify(list));
    showToast('Quote sent!', `Added to ${board === 'lead' ? 'Lead' : 'Load'} Board.`, 'success');
    dqActiveTab = board === 'lead' ? 'leadboard' : 'loadboard';
    distRenderDeliveryQuote();
}

async function restoreSession() {
    const apiUser = getCurrentUser() || await restorePortalUser();

    if (userHasAnyRole(apiUser, DIST_ROLES)) {
        const demo = DIST_ACCOUNTS[apiUser.email] ?? {};
        DIST_USER = {
            email: apiUser.email,
            company: demo.company
                ?? apiUser.distribution_company?.company_name
                ?? apiUser.name
                ?? 'Distribution Partner',
            role: demo.role ?? 'Distribution Partner',
            initials: demo.initials ?? apiUser.avatar_initials ?? 'DP',
            tier: demo.tier ?? apiUser.distribution_company?.tier ?? 'Enterprise',
        };
        persistSession();
        showPortal();
        updateUserDisplay();
        distSwitchTab('overview');
        return true;
    }

    localStorage.removeItem(STORAGE_SESSION);
    return false;
}

function handleDistAction(action, target) {
    const tab = target?.dataset?.tab;
    const idx = parseInt(target?.dataset?.idx ?? '-1', 10);

    switch (action) {
        case 'login': distLogin(); break;
        case 'register': distRegister(); break;
        case 'logout': distLogout(); break;
        case 'show-register':
            document.getElementById('dist-login-panel')?.classList.add('hidden');
            document.getElementById('dist-register-panel')?.classList.remove('hidden');
            break;
        case 'show-login':
            document.getElementById('dist-register-panel')?.classList.add('hidden');
            document.getElementById('dist-login-panel')?.classList.remove('hidden');
            break;
        case 'fill-demo':
            if (document.getElementById('dist-email')) document.getElementById('dist-email').value = 'dist@acme.com';
            if (document.getElementById('dist-pass')) document.getElementById('dist-pass').value = 'Dist2025!';
            break;
        case 'switch-tab': if (tab) distSwitchTab(tab); break;
        case 'open-payout-modal': distOpenPayoutModal(); break;
        case 'close-payout-modal': distClosePayoutModal(); break;
        case 'payout-tab': distPayoutTab(tab); break;
        case 'save-bank-payout': distSaveBankPayout(); break;
        case 'save-card-payout': distSaveCardPayout(); break;
        case 'save-manual-item': distSaveManualItem(); break;
        case 'simulate-csv': distSimulateCSV(); break;
        case 'import-csv': distImportCSV(); break;
        case 'cancel-csv': document.getElementById('csv-preview-area')?.classList.add('hidden'); break;
        case 'download-template': distDownloadTemplate(); break;
        case 'delete-item': if (idx >= 0) distDeleteItem(idx); break;
        case 'edit-item': if (idx >= 0) distEditItem(idx); break;
        case 'add-item-prompt': distAddItemPrompt(); break;
        case 'connect-carrier': showToast('Connecting…', `Connecting to ${target.dataset.carrier}`, 'success'); break;
        case 'regen-key':
            DIST_API_KEYS.primary = `sk_dist_${Math.random().toString(36).slice(2, 18)}`;
            persistApiKeys();
            distRenderAPI();
            showToast('API key regenerated!', 'Update your integrations with the new key.', 'success');
            break;
        case 'copy-key':
            navigator.clipboard?.writeText(DIST_API_KEYS.primary);
            showToast('Copied!', 'API key copied to clipboard.', 'success');
            break;
        case 'save-webhook': showToast('Webhook saved!', 'Your webhook endpoint has been saved.', 'success'); break;
        case 'save-profile': showToast('Profile saved!', 'Company profile updated.', 'success'); break;
        case 'update-markup': showToast('Markup updated!', 'All reseller prices recalculated.', 'success'); break;
        case 'dq-tab': dqActiveTab = tab; distRenderDeliveryQuote(); break;
        case 'dq-calculate': dqCalculate(); break;
        case 'dq-reset':
            dqLastQuote = null;
            document.getElementById('dq-result').innerHTML = '';
            distRenderDeliveryQuote();
            break;
        case 'dq-lead': dqSendToBoard('lead'); break;
        case 'dq-load': dqSendToBoard('load'); break;
        default: break;
    }
}

function bindEvents() {
    document.querySelectorAll('[data-dist-tab]').forEach((btn) => {
        btn.addEventListener('click', () => distSwitchTab(btn.dataset.distTab));
    });

    document.addEventListener('click', (event) => {
        const actionEl = event.target.closest('[data-dist-action]');
        if (!actionEl || !document.getElementById('distribute-portal-config')) {
            return;
        }

        const inPortal = actionEl.closest('#dist-main-content')
            || actionEl.closest('#dist-login-overlay')
            || actionEl.closest('#dist-payout-modal');

        if (!inPortal) {
            return;
        }

        event.preventDefault();
        handleDistAction(actionEl.dataset.distAction, actionEl);
    });

    document.getElementById('dist-pass')?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') distLogin();
    });

    window.addEventListener('smoovehub:auth-updated', () => {
        if (!DIST_USER) restoreSession();
    });
}

export function initDistributePortal() {
    if (!loadConfig()) return;

    bindEvents();

    Promise.resolve(restoreSession()).then((ok) => {
        if (!ok) {
            hidePortal();
        }
    });
}
