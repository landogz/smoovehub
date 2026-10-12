import { initAdminPortal } from './admin-portal.js';
import { initContactPage } from './contact-page.js';
import { initDesignMe } from './design-me.js';
import { initDesignRoom } from './design-room.js';
import { initDistributePortal } from './distribute-portal.js';
import { initDriverWizard } from './driver-wizard.js';
import { initQuoteWizard } from './quote-wizard.js';

function initContactForm() {
    initContactPage();
}

function initQuoteForm() {
    initQuoteWizard();
}

function initDriverForm() {
    initDriverWizard();
}

function initSellersSearch() {
    const input = document.getElementById('sellers-search');
    const grid = document.getElementById('sellers-grid');
    const count = document.getElementById('sellers-count');

    if (!input || !grid) {
        return;
    }

    input.addEventListener('input', () => {
        const query = input.value.trim().toLowerCase();
        let visible = 0;

        grid.querySelectorAll('[data-seller-card]').forEach((card) => {
            const haystack = [
                card.getAttribute('data-seller-name'),
                card.getAttribute('data-seller-category'),
                card.textContent,
            ].join(' ').toLowerCase();
            const show = !query || haystack.includes(query);
            card.classList.toggle('hidden', !show);

            if (show) {
                visible += 1;
            }
        });

        if (count) {
            count.textContent = String(visible);
        }

        document.getElementById('sellers-empty')?.classList.toggle('hidden', visible > 0);
    });
}

function initDesignMePage() {
    initDesignMe();
}

function initDesignRoomPage() {
    initDesignRoom();
}

function initDistributePortalPage() {
    initDistributePortal();
}

function initAdminPortalPage() {
    initAdminPortal();
}

export function initSiteForms() {
    initContactForm();
    initQuoteForm();
    initDriverForm();
    initDesignMePage();
    initDesignRoomPage();
    initDistributePortalPage();
    initAdminPortalPage();
    initSellersSearch();
}
