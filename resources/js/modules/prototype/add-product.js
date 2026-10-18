import axios from 'axios';
import { closeModal } from '../../shared/modal.js';
import { showToast } from '../landing/toast.js';
import { dashTab, refreshDashboard } from './dashboard.js';

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
        await axios.post('/api/v1/seller/products', payload);
    } catch (error) {
        showToast('Listing failed', error.response?.data?.message ?? 'Unable to list product. Sign in as a seller first.', 'error');
        return;
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

