/**
 * Reusable toast notifications for SmooveHub.
 */
export function showToast(title, message = '', type = 'success') {
    const wrap = document.getElementById('toast-wrap');

    if (!wrap) {
        return;
    }

    const icons = {
        success: '✅',
        error: '❌',
        warn: '⚠️',
    };

    const toast = document.createElement('div');
    toast.className = `sh-toast sh-toast-${type === 'error' ? 'error' : type === 'warn' ? 'warn' : 'success'}`;

    toast.innerHTML = `
        <span class="text-lg leading-none">${icons[type] ?? '📢'}</span>
        <div>
            <div class="sh-toast-title">${title}</div>
            ${message ? `<div class="sh-toast-msg">${message}</div>` : ''}
        </div>
    `;

    wrap.appendChild(toast);

    window.setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(120%)';
        toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        window.setTimeout(() => toast.remove(), 400);
    }, 4000);
}
