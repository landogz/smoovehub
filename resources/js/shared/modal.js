/**
 * Reusable modal open/close helpers for SmooveHub.
 */
export function openModal(id) {
    const modal = document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.add('sh-modal-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('sh-modal-active');

    const focusTarget = modal.querySelector('[data-modal-focus]')
        ?? modal.querySelector('input, button, select, textarea');

    window.setTimeout(() => focusTarget?.focus(), 50);
}

export function closeModal(id) {
    const modal = document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.remove('sh-modal-open');
    modal.setAttribute('aria-hidden', 'true');

    if (!document.querySelector('.sh-modal-bg.sh-modal-open')) {
        document.body.classList.remove('sh-modal-active');
    }
}

export function switchModal(fromId, toId) {
    closeModal(fromId);
    openModal(toId);
}

export function initModalSystem() {
    document.querySelectorAll('[data-modal-open]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const target = trigger.getAttribute('data-modal-open');

            if (target) {
                openModal(target);
            }
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const target = trigger.getAttribute('data-modal-close')
                ?? trigger.closest('.sh-modal-bg')?.id;

            if (target) {
                closeModal(target);
            }
        });
    });

    document.querySelectorAll('.sh-modal-bg').forEach((backdrop) => {
        backdrop.addEventListener('click', (event) => {
            if (event.target === backdrop) {
                closeModal(backdrop.id);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        const openModalEl = document.querySelector('.sh-modal-bg.sh-modal-open');

        if (openModalEl) {
            closeModal(openModalEl.id);
        }
    });
}
