import { showPortalDashboard } from '../site/portal-screen.js';
import { showToast } from '../landing/toast.js';

export function initPortals() {
    /* Tab engines handled by distribute-portal.js and admin-portal.js */
}

export function initDistLoginButtons() {
    document.querySelector('[data-dist-login]')?.addEventListener('click', () => {
        showPortalDashboard('dist-login-overlay', 'dist-main-content');
        showToast('Portal access', 'Distribution portal access granted.', 'success');
    });
}
