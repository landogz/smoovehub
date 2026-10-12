const RESPONSES = [
    {
        match: /sofa|sectional|couch/i,
        text: 'Browse our residential section for sofas and sectionals from verified sellers. Same-day delivery is available in select ZIP codes.',
        action: { label: 'Shop sofas →', href: '/shop' },
    },
    {
        match: /delivery|quote|ship/i,
        text: 'Use our free CRS delivery quote tool — enter pickup/delivery ZIPs, door dimensions, and access details for an instant estimate.',
        action: { label: 'Get a quote →', href: '/quote' },
    },
    {
        match: /sell|seller|store/i,
        text: 'Create your seller account in under 5 minutes. You keep 100% of your price — SmooveHub adds its fee on top, paid by the buyer.',
        action: { label: 'Start selling →', href: '/sell' },
    },
    {
        match: /driver|drive|earn/i,
        text: 'Join the SmooveHub driver network. Flexible routes, weekly payouts, and CRS-coordinated deliveries.',
        action: { label: 'Become a driver →', href: '/drive' },
    },
    {
        match: /track|order/i,
        text: 'All SmooveHub orders are CRM-tracked. After purchase you receive SMS and email updates from driver assignment through delivery.',
        action: { label: 'Contact support →', href: '/contact' },
    },
];

function appendMessage(text, role = 'bot', action = null) {
    const container = document.getElementById('dolores-msgs');

    if (!container) {
        return;
    }

    const wrap = document.createElement('div');
    wrap.className = `sh-dolores-msg ${role === 'user' ? 'sh-dolores-msg-user' : ''}`;

    let actionHtml = '';

    if (action) {
        actionHtml = `<a href="${action.href}" class="mt-2 inline-block text-xs font-semibold text-sh-blue underline">${action.label}</a>`;
    }

    wrap.innerHTML = `
        <div class="sh-dolores-bubble ${role === 'user' ? 'sh-dolores-bubble-user' : 'sh-dolores-bubble-bot'}">
            ${text}${actionHtml}
        </div>`;

    container.appendChild(wrap);
    container.scrollTop = container.scrollHeight;
}

function botReply(input) {
    const match = RESPONSES.find((item) => item.match.test(input));

    window.setTimeout(() => {
        if (match) {
            appendMessage(match.text, 'bot', match.action);
        } else {
            appendMessage(
                "I'm Ms. Dolores, your furniture assistant! I can help you shop, get delivery quotes, sell furniture, or join as a driver. What would you like to do?",
                'bot',
            );
        }
    }, 600);
}

function sendDoloresMessage(text) {
    const trimmed = text.trim();

    if (!trimmed) {
        return;
    }

    appendMessage(trimmed, 'user');
    botReply(trimmed);
}

export function initDolores() {
    const fab = document.getElementById('dolores-fab');
    const win = document.getElementById('dolores-win');
    const input = document.getElementById('dolores-inp');

    if (!fab || !win) {
        return;
    }

    fab.addEventListener('click', () => {
        const open = win.classList.toggle('sh-dolores-win-open');
        fab.setAttribute('aria-expanded', open ? 'true' : 'false');

        if (open) {
            input?.focus();
        }
    });

    document.getElementById('dolores-close')?.addEventListener('click', () => {
        win.classList.remove('sh-dolores-win-open');
        fab.setAttribute('aria-expanded', 'false');
    });

    document.getElementById('dolores-send')?.addEventListener('click', () => {
        sendDoloresMessage(input.value);
        input.value = '';
    });

    input?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            sendDoloresMessage(input.value);
            input.value = '';
        }
    });

    document.querySelectorAll('[data-dolores-quick]').forEach((button) => {
        button.addEventListener('click', () => {
            sendDoloresMessage(button.dataset.doloresQuick);
        });
    });
}
