import { initMemberAccount } from '../member/account.js';
import { initAddProduct, openProductEditor } from './add-product.js';
import { initDashboard, removeProduct, saveSellerSettings } from './dashboard.js';
import { initDolores } from './dolores.js';
import { initNotifications } from './notifications.js';
import { initPortals, initDistLoginButtons } from './portals.js';
import { initSellerSignup } from './seller-signup.js';
import {
    initFeaturedPurchaseCards,
    initShopPage,
    loadCatalog,
    setCatalogProducts,
    updateCartBadge,
} from './shop.js';
import { initStoreTabs } from './store.js';
import { loadState } from './state.js';

export function initPrototype() {
    const bootstrap = document.getElementById('prototype-bootstrap');

    if (bootstrap) {
        try {
            setCatalogProducts(JSON.parse(bootstrap.textContent ?? '[]'));
        } catch {
            setCatalogProducts([]);
        }
    }

    initSellerSignup();
    initAddProduct();
    initNotifications();
    initDolores();
    initStoreTabs();
    initPortals();
    initDistLoginButtons();
    initDashboard();
    initMemberAccount();
    initShopPage();
    initFeaturedPurchaseCards();
    loadCatalog();

    updateCartBadge(loadState().cartCount);

    window.addEventListener('smoovehub:edit-product', (event) => {
        openProductEditor(event.detail?.product);
    });

    window.addEventListener('smoovehub:remove-product', (event) => {
        removeProduct(event.detail?.id);
    });

    window.addEventListener('smoovehub:save-settings', saveSellerSettings);
}
