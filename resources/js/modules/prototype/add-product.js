import axios from 'axios';
import { closeModal, openModal } from '../../shared/modal.js';
import { showToast } from '../landing/toast.js';
import { dashTab, refreshDashboard } from './dashboard.js';

function productPayload() {
    const name = document.getElementById('ap-name')?.value.trim();
    const price = parseFloat(document.getElementById('ap-price')?.value);
    const cond = document.getElementById('ap-cond')?.value;
    const cat = document.getElementById('ap-cat')?.value;
    const qty = parseInt(document.getElementById('ap-qty')?.value, 10);
    const desc = document.getElementById('ap-desc')?.value.trim();
    const sameDay = document.querySelector('input[name="same-day"]:checked')?.value === 'yes';

    return {
        name,
        base_price: price,
        condition: cond,
        category: cat,
        quantity: qty,
        description: desc || null,
        same_day_delivery: sameDay,
    };
}

function resetProductForm() {
    document.getElementById('ap-product-id').value = '';
    document.getElementById('ap-name').value = '';
    document.getElementById('ap-price').value = '';
    document.getElementById('ap-cond').value = 'BRAND_NEW';
    document.getElementById('ap-cat').value = 'RESIDENTIAL';
    document.getElementById('ap-qty').value = '1';
    document.getElementById('ap-desc').value = '';
    const noRadio = document.querySelector('input[name="same-day"][value="no"]');

    if (noRadio) {
        noRadio.checked = true;
    }

    const title = document.getElementById('add-product-modal-title');

    if (title) {
        title.textContent = '+ Add New Product';
    }

    const submit = document.querySelector('[data-add-product-submit]');

    if (submit) {
        submit.textContent = 'List Product →';
    }
}

export function openProductEditor(product) {
    const title = document.getElementById('add-product-modal-title');

    if (title) {
        title.textContent = product ? 'Edit Product' : '+ Add New Product';
    }

    const submit = document.querySelector('[data-add-product-submit]');

    if (submit) {
        submit.textContent = product ? 'Save changes →' : 'List Product →';
    }

    document.getElementById('ap-err')?.classList.add('hidden');
    document.getElementById('ap-product-id').value = product?.id ?? '';
    document.getElementById('ap-name').value = product?.name ?? '';
    document.getElementById('ap-price').value = product?.original ? String(product.original) : '';
    document.getElementById('ap-cond').value = product?.condition ?? 'BRAND_NEW';
    document.getElementById('ap-cat').value = product?.category ?? 'RESIDENTIAL';
    document.getElementById('ap-qty').value = String(product?.quantity ?? 1);
    document.getElementById('ap-desc').value = product?.description ?? '';
    const sameValue = product?.same_day ? 'yes' : 'no';
    const radio = document.querySelector(`input[name="same-day"][value="${sameValue}"]`);

    if (radio) {
        radio.checked = true;
    }

    openModal('add-product-modal');
}

export async function addProduct() {
    const payload = productPayload();
    const err = document.getElementById('ap-err');
    const uuid = document.getElementById('ap-product-id')?.value.trim();

    if (!payload.name || !payload.base_price || !payload.condition || !payload.category || !payload.quantity) {
        err.textContent = 'Please fill in all required fields.';
        err.classList.remove('hidden');
        return;
    }

    err.classList.add('hidden');

    try {
        if (uuid) {
            await axios.put(`/api/v1/seller/products/${uuid}`, payload);
            showToast('Product updated', `${payload.name} has been saved.`, 'success');
        } else {
            await axios.post('/api/v1/seller/products', payload);
            showToast('Product Listed! 🎉', `${payload.name} is now live.`, 'success');
        }
    } catch (error) {
        showToast('Listing failed', error.response?.data?.message ?? 'Unable to save product. Sign in as a seller first.', 'error');
        return;
    }

    closeModal('add-product-modal');
    resetProductForm();
    refreshDashboard();
    dashTab('products');
}

export function initAddProduct() {
    document.querySelectorAll('[data-modal-open="add-product-modal"]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            resetProductForm();
            document.getElementById('ap-err')?.classList.add('hidden');
        });
    });

    document.querySelector('[data-add-product-submit]')?.addEventListener('click', addProduct);

    document.querySelector('[data-photo-upload]')?.addEventListener('click', () => {
        showToast('Photo upload', 'Connect to Cloudinary in production.', 'warn');
    });
}
