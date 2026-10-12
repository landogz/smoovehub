export function initStoreTabs() {
    const tabs = document.querySelectorAll('[data-store-tab]');
    const panels = document.querySelectorAll('[data-store-panel]');

    if (!tabs.length) {
        return;
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const key = tab.dataset.storeTab;

            tabs.forEach((item) => {
                const active = item.dataset.storeTab === key;
                item.classList.toggle('sh-nav-link-active', active);
                item.classList.toggle('border-sh-blue', active);
                item.classList.toggle('border-transparent', !active);
                item.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.storePanel !== key);
            });
        });
    });
}
