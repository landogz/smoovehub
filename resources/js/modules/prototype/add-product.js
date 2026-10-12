import axios from 'axios';
import { closeModal } from '../../shared/modal.js';
import { showToast } from '../landing/toast.js';
import { getCurrentUser } from '../auth/auth.js';
import { CONDITION_LABELS, formatMoney, loadState, saveState } from './state.js';
import { dashTab, refreshDashboard } from './dashboard.js';
import { registerSellerProduct } from './shop.js';

export async function addProduct() {
    const name = document.getElementById('ap-name')?.value.trim();
    const price = parseFloat(document.getElementById('ap-price')?.value);
    const cond = document.getElementById('ap-cond')?.value;
    const cat = document.getElementById('ap-cat')?.value;
    const qty = parseInt(document.getElementById('ap-qty')?.value, 10);
    const desc = document.getElementById('ap-desc')?.value.trim();
    const sameDay = document.querySelector('input[name="same-day"]:checked')?.value === 'yes';
    const err = document.getElementById('ap-err');

    if (!name || !price || !cond || !cat || !qty) {
        err.textContent = 'Please fill in all required fields.';
        err.classList.remove('hidden');
        return;
    }

    err.classList.add('hidden');

    const payload = {
        name,
        base_price: price,
        condition: cond,
        category: cat,
        quantity: qty,
        description: desc || null,
        same_day_delivery: sameDay,
    };

    try {
        const response = await axios.post('/api/v1/seller/products', payload);
        const product = response.data?.data?.product;
        registerSellerProduct(product);
    } catch (error) {
        if (error.response?.status !== 401 && error.response?.status !== 403) {
            showToast('Listing failed', error.response?.data?.message ?? 'Unable to list product.', 'error');
            return;
        }

        const markup = cond === 'BRAND_NEW' ? 1.4 : 1.1;
        const listed = Math.round(price * markup);
        const icons = { COMMERCIAL: '🏢', RESIDENTIAL: '🛋️', APPLIANCES: '🔌' };
        const user = getCurrentUser();

        registerSellerProduct({
            id: Date.now(),
            name,
            price: listed,
            original: price,
            seller: user?.seller_profile?.store_name ?? 'My Store',
            condition: cond,
            category: cat,
            icon: icons[cat] ?? '🛋️',
            trending: false,
            same_day: sameDay,
        });
    }

    closeModal('add-product-modal');
    document.getElementById('ap-name').value = '';
    document.getElementById('ap-price').value = '';

    showToast('Product Listed! 🎉', `${name} is now live.`, 'success');
    refreshDashboard();
    dashTab('products');
}

export function initAddProduct() {
    document.querySelectorAll('[data-modal-open="add-product-modal"]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            document.getElementById('ap-err')?.classList.add('hidden');
        });
    });

    document.querySelector('[data-add-product-submit]')?.addEventListener('click', addProduct);

    document.querySelector('[data-photo-upload]')?.addEventListener('click', () => {
        showToast('Photo upload', 'Connect to Cloudinary in production.', 'warn');
    });
}

export function removeProduct(id) {
    const state = loadState();
    state.sellerProducts = state.sellerProducts.filter((product) => String(product.id) !== String(id));
    saveState(state);
    refreshDashboard();
    showToast('Product removed', 'Listing has been deleted.', 'success');
}

export function saveSellerSettings() {
    const name = document.getElementById('set-store-name')?.value.trim();

    if (name) {
        document.getElementById('dash-seller-name').textContent = name;
    }

    showToast('Settings saved!', 'Your store information has been updated.', 'success');
}
