import axios from 'axios';
import { getCurrentUser, openAuthModal } from '../auth/auth.js';
import { restorePortalUser } from '../auth/portal-auth.js';
import { showToast } from '../landing/toast.js';
import { formatMoney } from '../prototype/state.js';

function paymentBadge(status) {
    if (status === 'paid') {
        return 'sh-badge sh-badge-green';
    }

    return 'sh-badge sh-badge-amber';
}

function renderOrders(orders) {
    const list = document.getElementById('member-orders-list');

    if (!list) {
        return;
    }

    if (!orders.length) {
        list.innerHTML = '<p class="py-6 text-center text-[13px] text-sh-s400">No orders yet. Shop furniture and they will show up here.</p>';
        return;
    }

    list.innerHTML = orders.map((order) => `
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-sh-s100 px-4 py-3">
            <div>
                <div class="text-sm font-semibold text-sh-s900">${order.order_number}</div>
                <div class="text-[13px] text-sh-s500">${order.product}</div>
                <div class="text-xs text-sh-s400">${order.date ?? ''}${order.delivery_zip ? ` · ${order.delivery_zip}` : ''}</div>
            </div>
            <div class="text-right">
                <div class="font-bold text-sh-s900">${formatMoney(order.total)}</div>
                <span class="${paymentBadge(order.payment_status)}">${order.payment_status}</span>
            </div>
        </div>`).join('');
}

async function loadMemberAccount() {
    try {
        const response = await axios.get('/api/v1/member/account');
        const data = response.data?.data ?? {};
        const greet = document.getElementById('member-greet');
        const ordersEl = document.getElementById('member-stat-orders');
        const unpaidEl = document.getElementById('member-stat-unpaid');

        if (greet) {
            greet.textContent = data.member?.name ?? 'Member';
        }

        if (ordersEl) {
            ordersEl.textContent = String(data.stats?.orders ?? 0);
        }

        if (unpaidEl) {
            unpaidEl.textContent = String(data.stats?.unpaid ?? 0);
        }

        renderOrders(data.orders ?? []);
    } catch (error) {
        if (error.response?.status === 401 || error.response?.status === 403) {
            showToast('Member sign-in required', 'Sign in with the account you use to shop.', 'warn');
            openAuthModal('login');
            return;
        }

        showToast('Account unavailable', error.response?.data?.message ?? 'Could not load your orders.', 'error');
    }
}

export function initMemberAccount() {
    if (!document.getElementById('member-account')) {
        return;
    }

    Promise.resolve(restorePortalUser()).then((user) => {
        if (!user && !getCurrentUser()) {
            showToast('Member sign-in required', 'Sign in with the account you use to shop.', 'warn');
            openAuthModal('login');
        }

        loadMemberAccount();
    });
}
