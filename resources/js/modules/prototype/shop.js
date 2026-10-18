import axios from 'axios';
import { closeModal, openModal } from '../../shared/modal.js';
import { showToast } from '../landing/toast.js';
import {
    CONDITION_LABELS,
    formatMoney,
    loadState,
    pushNotification,
    saveState,
} from './state.js';

let catalogProducts = [];
let currentPurchaseProduct = null;
let currentCheckoutQuote = null;

export function setCatalogProducts(products = []) {
    catalogProducts = products;
}

export function getAllProducts() {
    const state = loadState();
    const merged = [...catalogProducts];

    state.sellerProducts.forEach((product) => {
        if (!merged.find((item) => String(item.id) === String(product.id))) {
            merged.push(product);
        }
    });

    return merged;
}

export function findProduct(id) {
    return getAllProducts().find((item) => String(item.id) === String(id));
}

function renderProductCard(product, conditionLabels) {
    const condition = conditionLabels[product.condition] ?? { label: 'Item', class: 'sh-badge-slate' };
    const original = product.original > 0 ? formatMoney(product.original) : '';

    return `
        <article class="sh-product-card cursor-pointer" data-purchase-id="${product.id}" role="button" tabindex="0" aria-label="Buy ${product.name}">
            <div class="sh-product-image">
                <span aria-hidden="true">${product.icon ?? '🛋️'}</span>
                <div class="absolute left-2.5 top-2.5 flex flex-col gap-1">
                    <span class="sh-badge ${condition.class}">${condition.label}</span>
                    ${product.trending ? '<span class="sh-badge sh-badge-orange">🔥</span>' : ''}
                    ${product.same_day ? '<span class="sh-badge sh-badge-green">⚡</span>' : ''}
                </div>
                <span class="absolute right-2.5 top-2.5 flex h-[30px] w-[30px] items-center justify-center rounded-full bg-white/90 text-[13px]">❤️</span>
            </div>
            <div class="sh-product-info">
                <div class="sh-product-seller">${product.seller} ✓</div>
                <h3 class="sh-product-name">${product.name}</h3>
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <span class="sh-product-price">${formatMoney(product.price)}</span>
                        ${original ? `<span class="sh-product-original">${original}</span>` : ''}
                    </div>
                    <span class="sh-product-cta">Buy →</span>
                </div>
            </div>
        </article>
    `;
}

function applyShopFilters(products) {
    const section = document.getElementById('shop-product-grid')?.closest('section');
    const activeFilters = [];

    section?.querySelectorAll('[data-shop-filter]').forEach((input) => {
        if (input.checked) {
            activeFilters.push(input.dataset.shopFilter);
        }
    });

    if (!activeFilters.length) {
        return products;
    }

    return products.filter((product) => activeFilters.every((filter) => {
        if (filter === 'same_day') {
            return product.same_day;
        }

        if (filter === 'delivery') {
            return true;
        }

        if (['COMMERCIAL', 'RESIDENTIAL', 'APPLIANCES'].includes(filter)) {
            return product.category === filter;
        }

        return product.condition === filter;
    }));
}

function sortProducts(products, sortBy) {
    const list = [...products];

    switch (sortBy) {
    case 'Newest':
        return list.reverse();
    case 'Price: Low–High':
        return list.sort((a, b) => a.price - b.price);
    case 'Price: High–Low':
        return list.sort((a, b) => b.price - a.price);
    case 'Trending':
        return list.sort((a, b) => Number(b.trending) - Number(a.trending));
    default:
        return list;
    }
}

export function refreshShopGrid(conditionLabels = {}) {
    const grid = document.getElementById('shop-product-grid');
    const countEl = document.getElementById('shop-result-count');
    const sortEl = document.getElementById('shop-sort');

    if (!grid) {
        return;
    }

    let products = applyShopFilters(getAllProducts());
    products = sortProducts(products, sortEl?.value ?? 'Featured');

    grid.innerHTML = products.length
        ? products.map((product) => renderProductCard(product, conditionLabels)).join('')
        : '<p class="col-span-full py-12 text-center text-[13px] text-sh-s500">No products match your filters.</p>';

    if (countEl) {
        countEl.textContent = String(products.length);
    }
}

function applyQuoteToModal(quote) {
    const dtype = document.getElementById('pm-dtype')?.value ?? 'CURBSIDE';

    document.getElementById('pm-base-price').textContent = formatMoney(quote.listing_price);
    document.getElementById('pm-markup-amt').textContent = quote.markup_display ?? 'Included';
    document.getElementById('pm-proc-fee').textContent = formatMoney(quote.processing_fee);
    document.getElementById('pm-del-fee').textContent = formatMoney(quote.delivery_fee);
    document.getElementById('pm-del-pct').textContent = formatMoney(quote.delivery_coordination_fee);
    document.getElementById('pm-total').textContent = formatMoney(quote.total);
    document.getElementById('pm-inhome-row')?.classList.toggle('hidden', dtype !== 'IN_HOME');
}

export async function pmRecalc() {
    if (!currentPurchaseProduct) {
        return;
    }

    try {
        const zip = document.getElementById('pm-zip')?.value.trim() ?? '';
        const response = await axios.post('/api/v1/orders/quote', {
            product_id: currentPurchaseProduct.id,
            delivery_type: document.getElementById('pm-dtype')?.value ?? 'CURBSIDE',
            ...(/^\d{5}$/.test(zip) ? { delivery_zip: zip } : {}),
        });

        currentCheckoutQuote = response.data?.data?.quote ?? null;

        if (currentCheckoutQuote) {
            applyQuoteToModal(currentCheckoutQuote);
        }
    } catch {
        showToast('Quote unavailable', 'Could not calculate checkout totals. Please try again.', 'error');
    }
}

export function openPurchase(prodId) {
    const product = findProduct(prodId);

    if (!product) {
        showToast('Product unavailable', 'This item could not be found.', 'error');
        return;
    }

    currentPurchaseProduct = product;
    currentCheckoutQuote = null;

    document.getElementById('pm-product-info').innerHTML = `
        <div class="flex items-center gap-3">
            <div class="text-4xl" aria-hidden="true">${product.icon ?? '🛋️'}</div>
            <div>
                <div class="font-semibold text-sm">${product.name}</div>
                <div class="mt-0.5 text-xs text-sh-s400">${product.seller} · ${CONDITION_LABELS[product.condition] ?? product.condition}</div>
            </div>
        </div>`;

    document.getElementById('pm-addr').value = '';
    document.getElementById('pm-zip').value = '';
    pmRecalc();
    openModal('purchase-modal');
}

export async function completePurchase() {
    const addr = document.getElementById('pm-addr')?.value.trim();
    const zip = document.getElementById('pm-zip')?.value.trim();
    const dtype = document.getElementById('pm-dtype')?.value ?? 'CURBSIDE';

    if (!addr || !/^\d{5}$/.test(zip)) {
        showToast('Missing information', 'Please enter delivery address and a valid 5-digit ZIP.', 'error');
        return;
    }

    const product = currentPurchaseProduct;
    const submit = document.querySelector('[data-purchase-submit]');

    if (submit) {
        submit.disabled = true;
    }

    try {
        const response = await axios.post('/api/v1/orders/purchase', {
            product_id: product.id,
            delivery_address: addr,
            delivery_zip: zip,
            delivery_type: dtype,
        });

        const order = response.data?.data?.order;
        const orderNum = order?.order_number;

        if (!orderNum) {
            throw new Error('Missing order number');
        }

        finalizePurchase(orderNum, product, addr, order?.total ?? product.price);
    } catch (error) {
        const message = error.response?.data?.message ?? 'The order could not be placed. Please try again.';
        showToast('Order not placed', message, 'error');
    } finally {
        if (submit) {
            submit.disabled = false;
        }
    }
}

function finalizePurchase(orderNum, product, addr, amount) {
    const state = loadState();

    state.orders.push({
        id: Date.now(),
        orderNum,
        product: product.name,
        productId: product.id,
        seller: product.seller,
        amount,
        status: 'Confirmed',
        date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
        addr,
    });

    state.cartCount += 1;
    saveState(state);

    closeModal('purchase-modal');
    updateCartBadge(state.cartCount);

    showToast('Order placed', `Order ${orderNum} saved. Stripe payment confirmation ships in Phase 3.`, 'success');

    pushNotification({
        type: 'new_order',
        icon: '🛒',
        title: `New order: ${orderNum}`,
        msg: `${product.name} sold for ${formatMoney(amount)}. Customer address: ${addr}`,
    });

    window.dispatchEvent(new CustomEvent('smoovehub:notifications-updated'));

    window.setTimeout(() => {
        showToast('📦 CRM Updated', `Order ${orderNum} pushed to CRS Loadboard. Driver assignment in progress.`, 'success');
    }, 1500);

    window.dispatchEvent(new CustomEvent('smoovehub:dashboard-refresh'));
}

export function updateCartBadge(count) {
    document.querySelectorAll('[data-cart-count]').forEach((el) => {
        el.textContent = String(count);
        el.classList.toggle('hidden', count <= 0);
    });
}

export function initShopPage() {
    const grid = document.getElementById('shop-product-grid');

    if (!grid) {
        return;
    }

    const conditionLabels = JSON.parse(grid.dataset.conditionLabels ?? '{}');

    refreshShopGrid(conditionLabels);

    grid.addEventListener('click', (event) => {
        const card = event.target.closest('[data-purchase-id]');

        if (card) {
            openPurchase(card.dataset.purchaseId);
        }
    });

    document.querySelectorAll('[data-shop-filter]').forEach((input) => {
        input.addEventListener('change', () => refreshShopGrid(conditionLabels));
    });

    document.getElementById('shop-sort')?.addEventListener('change', () => refreshShopGrid(conditionLabels));

    initPurchaseModal();
    updateCartBadge(loadState().cartCount);
}

export function initFeaturedPurchaseCards() {
    initPurchaseModal();

    document.body.addEventListener('click', (event) => {
        const card = event.target.closest('[data-purchase-id]');

        if (card && !document.getElementById('shop-product-grid')?.contains(card)) {
            openPurchase(card.dataset.purchaseId);
        }
    });
}

function initPurchaseModal() {
    if (document.body.dataset.pmBound === '1') {
        return;
    }

    document.body.dataset.pmBound = '1';

    document.querySelectorAll('[data-pm-recalc]').forEach((el) => {
        el.addEventListener('input', pmRecalc);
        el.addEventListener('change', pmRecalc);
    });

    document.querySelector('[data-purchase-submit]')?.addEventListener('click', completePurchase);
}

export async function loadCatalog() {
    try {
        const response = await axios.get('/api/v1/catalog/products');
        setCatalogProducts(response.data?.data?.products ?? []);

        const grid = document.getElementById('shop-product-grid');

        if (grid) {
            refreshShopGrid(JSON.parse(grid.dataset.conditionLabels ?? '{}'));
        }
    } catch {
        // Static Blade-rendered cards remain available.
    }
}

export function registerSellerProduct(product) {
    const state = loadState();
    state.sellerProducts.push(product);
    saveState(state);
}
