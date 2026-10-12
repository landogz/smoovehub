const STORAGE_KEY = 'smoovehub_prototype_state';

const defaultState = () => ({
    sellerProducts: [],
    orders: [],
    purchases: [],
    notifications: [],
    cartCount: 0,
});

let memoryState = null;

export function loadState() {
    if (memoryState) {
        return memoryState;
    }

    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        memoryState = raw ? { ...defaultState(), ...JSON.parse(raw) } : defaultState();
    } catch {
        memoryState = defaultState();
    }

    return memoryState;
}

export function saveState(state) {
    memoryState = state;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
}

export function pushNotification(notif) {
    const state = loadState();
    state.notifications.unshift({
        id: Date.now(),
        unread: true,
        time: 'Just now',
        ...notif,
    });
    saveState(state);
    return state.notifications;
}

export function markNotificationRead(id) {
    const state = loadState();
    const item = state.notifications.find((n) => n.id === id);

    if (item) {
        item.unread = false;
        saveState(state);
    }
}

export function markAllNotificationsRead() {
    const state = loadState();
    state.notifications.forEach((n) => {
        n.unread = false;
    });
    saveState(state);
}

export function unreadNotificationCount() {
    return loadState().notifications.filter((n) => n.unread).length;
}

export const CONDITION_LABELS = {
    BRAND_NEW: 'New',
    THRIFT: 'Thrift',
    FLOOR_MODEL: 'Floor',
    REFURBISHED: 'Refurb',
};

export function formatMoney(value) {
    return `$${Number(value).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 })}`;
}
