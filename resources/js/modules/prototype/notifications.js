import {
    loadState,
    markAllNotificationsRead,
    markNotificationRead,
    unreadNotificationCount,
} from './state.js';

function renderNotifPanel() {
    const list = document.getElementById('notif-list');
    const state = loadState();

    if (!list) {
        return;
    }

    if (!state.notifications.length) {
        list.innerHTML = '<div class="px-4 py-8 text-center text-[13px] text-sh-s400">No notifications yet</div>';
        return;
    }

    list.innerHTML = state.notifications.slice(0, 8).map((notif) => `
        <button type="button" class="sh-notif-item ${notif.unread ? 'sh-notif-item-unread' : ''}" data-notif-id="${notif.id}">
            <span class="sh-notif-icon">${notif.icon ?? '🔔'}</span>
            <span class="min-w-0 flex-1 text-left">
                <span class="block text-[13px] font-semibold text-sh-s900">${notif.title}</span>
                <span class="block text-xs text-sh-s500">${notif.msg}</span>
                <span class="block text-[11px] text-sh-s400">${notif.time}</span>
            </span>
        </button>`).join('');
}

function updateNotifCount() {
    const count = unreadNotificationCount();
    document.querySelectorAll('[data-notif-count]').forEach((dot) => {
        dot.textContent = String(count);
        dot.classList.toggle('hidden', count <= 0);
    });
}

export function toggleNotifPanel() {
    document.getElementById('notif-panel')?.classList.toggle('hidden');
}

export function initNotifications() {
    const toggle = document.getElementById('notif-toggle');
    const panel = document.getElementById('notif-panel');

    if (!toggle || !panel) {
        return;
    }

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        panel.classList.toggle('hidden');
    });

    document.getElementById('notif-mark-all')?.addEventListener('click', () => {
        markAllNotificationsRead();
        updateNotifCount();
        renderNotifPanel();
    });

    panel.addEventListener('click', (event) => {
        const item = event.target.closest('[data-notif-id]');

        if (item) {
            markNotificationRead(Number(item.dataset.notifId));
            updateNotifCount();
            renderNotifPanel();
        }
    });

    document.addEventListener('click', (event) => {
        if (!panel.contains(event.target) && !toggle.contains(event.target)) {
            panel.classList.add('hidden');
        }
    });

    window.addEventListener('smoovehub:notifications-updated', () => {
        updateNotifCount();
        renderNotifPanel();
    });

    updateNotifCount();
    renderNotifPanel();
}
