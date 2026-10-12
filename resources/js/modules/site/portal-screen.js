/**
 * Toggle portal login vs dashboard screens.
 * Uses inline display (with priority) so layout CSS cannot override .hidden.
 */
export function showPortalDashboard(overlayId, mainId) {
    const overlay = document.getElementById(overlayId);
    const main = document.getElementById(mainId);

    overlay?.classList.add('hidden');
    overlay?.style.setProperty('display', 'none', 'important');
    overlay?.setAttribute('aria-hidden', 'true');

    main?.classList.remove('hidden');
    main?.style.setProperty('display', 'flex', 'important');
    main?.setAttribute('aria-hidden', 'false');
}

export function showPortalLogin(overlayId, mainId) {
    const overlay = document.getElementById(overlayId);
    const main = document.getElementById(mainId);

    overlay?.classList.remove('hidden');
    overlay?.style.setProperty('display', 'flex', 'important');
    overlay?.setAttribute('aria-hidden', 'false');

    main?.classList.add('hidden');
    main?.style.setProperty('display', 'none', 'important');
    main?.setAttribute('aria-hidden', 'true');
}

export function showOverlayModal(modalId) {
    const modal = document.getElementById(modalId);
    modal?.classList.remove('hidden');
    modal?.style.setProperty('display', 'flex', 'important');
    modal?.setAttribute('aria-hidden', 'false');
}

export function hideOverlayModal(modalId) {
    const modal = document.getElementById(modalId);
    modal?.classList.add('hidden');
    modal?.style.setProperty('display', 'none', 'important');
    modal?.setAttribute('aria-hidden', 'true');
}
