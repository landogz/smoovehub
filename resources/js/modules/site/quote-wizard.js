import axios from 'axios';
import { showToast } from '../landing/toast.js';

function value(id) {
    return document.getElementById(id)?.value.trim() ?? '';
}

function numberValue(id, fallback = 0) {
    const parsed = parseFloat(document.getElementById(id)?.value ?? '');

    return Number.isFinite(parsed) ? parsed : fallback;
}

function intValue(id, fallback = 0) {
    const parsed = parseInt(document.getElementById(id)?.value ?? '', 10);

    return Number.isFinite(parsed) ? parsed : fallback;
}

function showQuoteError(message) {
    const el = document.getElementById('q-err');

    if (!el) {
        return;
    }

    el.textContent = message;
    el.classList.remove('hidden');
}

function hideQuoteError() {
    document.getElementById('q-err')?.classList.add('hidden');
}

function renderLineItems(items) {
    const container = document.getElementById('q-rlines');

    if (!container) {
        return;
    }

    container.innerHTML = (items ?? []).map((item) => {
        const tagClass = {
            base: 'sh-quote-tag-base',
            inc: 'sh-quote-tag-inc',
            sur: 'sh-quote-tag-sur',
            fee: 'sh-quote-tag-fee',
            info: 'sh-quote-tag-info',
        }[item.type] ?? 'sh-quote-tag-info';

        let amountHtml = `<div class="text-sm font-bold text-sh-s900">$${Number(item.amount).toFixed(2)}</div>`;

        if (item.type === 'inc') {
            amountHtml = '<span class="font-bold text-green-600">FREE</span>';
        } else if (item.type === 'info') {
            amountHtml = '<span class="text-xs text-sh-s500">On-site</span>';
        }

        return `
            <div class="sh-quote-line-item">
                <div>
                    <div class="text-[13px] text-sh-s800">${item.name}</div>
                    ${item.note ? `<div class="mt-px text-[11px] text-sh-s400">${item.note}</div>` : ''}
                    <span class="sh-quote-tag ${tagClass}">${item.type}</span>
                </div>
                ${amountHtml}
            </div>
        `;
    }).join('');
}

function renderPills(pills) {
    const container = document.getElementById('q-rpills');

    if (!container) {
        return;
    }

    container.innerHTML = (pills ?? []).map((pill) => `
        <span class="${pill.warn ? 'sh-quote-pill-warn' : 'sh-quote-pill-ok'}">${pill.text}</span>
    `).join('');
}

function renderSummary(rows) {
    const container = document.getElementById('access-summary-grid');

    if (!container) {
        return;
    }

    container.innerHTML = (rows ?? []).map((row) => `
        <div>
            <div class="text-[10px] uppercase tracking-[0.5px] text-sh-s400">${row.label}</div>
            <div class="mt-px text-[13px] font-semibold text-sh-s900">${row.value}</div>
        </div>
    `).join('');
}

function renderEstimate(data) {
    document.getElementById('estimate-card')?.classList.remove('hidden');
    document.getElementById('access-summary-card')?.classList.remove('hidden');

    const quoteId = document.getElementById('q-rid');
    const quoteSub = document.getElementById('q-rsub');
    const quoteExp = document.getElementById('q-rexp');
    const quoteTotal = document.getElementById('q-rtotal');
    const callout = document.getElementById('q-rcall');

    if (quoteId) {
        quoteId.textContent = data.quote_id;
    }

    if (quoteSub) {
        quoteSub.textContent = `${data.miles} mi · ${data.distance_label}`;
    }

    if (quoteExp) {
        quoteExp.textContent = `Valid ${data.expires_at}`;
    }

    if (quoteTotal) {
        quoteTotal.textContent = `$${Number(data.estimate_total).toFixed(2)}`;
    }

    if (callout) {
        callout.classList.toggle('hidden', !data.requires_phone_quote);
    }

    renderPills(data.pills);
    renderLineItems(data.line_items);
    renderSummary(data.summary);
}

function loadCrsPanel(url, { openTab = true } = {}) {
    document.getElementById('crs-open-tab-link')?.setAttribute('href', url);
    document.getElementById('crs-fallback-link')?.setAttribute('href', url);

    document.getElementById('crs-placeholder')?.classList.add('hidden');
    document.getElementById('crs-iframe-wrap')?.classList.remove('hidden');

    const iframe = document.getElementById('crs-iframe');

    if (iframe) {
        iframe.src = url;
    }

    if (openTab) {
        window.setTimeout(() => {
            window.open(url, '_blank', 'noopener,noreferrer');
        }, 600);
    }
}

function buildPayload(state) {
    return {
        pickup_zip: value('q-fz'),
        delivery_zip: value('q-tz'),
        door_width: numberValue('q-dw', 36),
        door_height: numberValue('q-dh', 80),
        weight: value('q-wt') || null,
        pieces: value('q-pc') || null,
        stairs_enabled: state.stairs,
        stair_flights: state.stairs ? intValue('q-fl', 1) : 0,
        stair_type: document.getElementById('q-st')?.value ?? 'standard',
        elevator_enabled: state.elevator,
        elevator_floors: state.elevator ? intValue('q-ef', 1) : 0,
        elevator_width: state.elevator ? numberValue('q-ev', 0) : null,
        carry_distance: numberValue('q-carry', 0),
        delivery_type: state.deliveryType,
        name: value('q-nm') || null,
        email: value('q-em') || null,
        phone: value('q-ph') || null,
    };
}

export function initQuoteWizard() {
    const page = document.getElementById('quote-page');

    if (!page) {
        return;
    }

    const state = {
        stairs: false,
        elevator: false,
        deliveryType: 'CURBSIDE',
        estimateVisible: false,
        submitting: false,
    };

    const checkNarrow = (width) => {
        const parsed = parseFloat(width);
        const warn = document.getElementById('narrow-warn');

        if (!warn) {
            return;
        }

        warn.classList.toggle('hidden', !(Number.isFinite(parsed) && parsed < 32));

        if (!warn.classList.contains('hidden') && !warn.textContent.trim()) {
            warn.textContent = '⚠️ Narrow door detected (<32"). A $40 surcharge may apply at CRS confirmation.';
        }
    };

    const toggleAccess = (bodyId, switchId, type) => {
        const body = document.getElementById(bodyId);
        const toggle = document.getElementById(switchId);
        const on = !toggle?.classList.contains('sh-switch-on');

        toggle?.classList.toggle('sh-switch-on', on);

        if (body) {
            body.style.maxHeight = on ? `${body.scrollHeight + 60}px` : '0';
            body.style.borderTopWidth = on ? '1px' : '0';
        }

        if (type === 'stairs') {
            state.stairs = on;
        }

        if (type === 'elev') {
            state.elevator = on;
        }

        if (state.estimateVisible) {
            buildAndOpenCrs({ silent: true });
        }
    };

    const setDeliveryType = (type) => {
        state.deliveryType = type;

        const curbside = document.getElementById('do-c');
        const inHome = document.getElementById('do-h');

        curbside?.classList.toggle('sh-quote-delivery-active', type === 'CURBSIDE');
        inHome?.classList.toggle('sh-quote-delivery-active', type === 'IN_HOME');

        if (state.estimateVisible) {
            buildAndOpenCrs({ silent: true });
        }
    };

    const qLiveCalc = () => {
        if (state.estimateVisible) {
            buildAndOpenCrs({ silent: true });
        }
    };

    const buildAndOpenCrs = async ({ silent = false } = {}) => {
        if (state.submitting) {
            return;
        }

        const pickupZip = value('q-fz');
        const deliveryZip = value('q-tz');

        if (!/^\d{5}$/.test(pickupZip) || !/^\d{5}$/.test(deliveryZip)) {
            if (!silent) {
                showQuoteError('Please enter valid 5-digit pickup and delivery ZIP codes.');
            }

            return;
        }

        hideQuoteError();
        state.submitting = true;

        const button = document.getElementById('crs-go-btn');
        button?.setAttribute('disabled', 'disabled');

        try {
            const response = await axios.post('/api/v1/site/quote-estimate', buildPayload(state));

            if (!response.data?.status) {
                throw new Error(response.data?.message ?? 'Unable to calculate estimate.');
            }

            const data = response.data.data;
            renderEstimate(data);
            loadCrsPanel(data.crs_url, { openTab: !silent });
            state.estimateVisible = true;

            if (!silent) {
                showToast('Opening CRS delivery quote page', 'Your estimate and CRS panel are ready.', 'success');

                window.setTimeout(() => {
                    document.getElementById('crs-iframe-wrap')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 800);

                if (button) {
                    button.textContent = '✅ Sent to CRS — click to refresh';
                    button.classList.add('sh-quote-btn-success');

                    window.setTimeout(() => {
                        button.textContent = '🚛 Get CRS Delivery Quote →';
                        button.classList.remove('sh-quote-btn-success');
                    }, 5000);
                }
            }
        } catch (error) {
            const message = error.response?.data?.message ?? error.message ?? 'Unable to calculate estimate.';
            showQuoteError(message);
            showToast('Quote failed', message, 'error');
        } finally {
            state.submitting = false;
            button?.removeAttribute('disabled');
        }
    };

    document.getElementById('q-dw')?.addEventListener('input', (event) => {
        checkNarrow(event.target.value);
        qLiveCalc();
    });

    document.getElementById('q-dh')?.addEventListener('input', qLiveCalc);
    document.getElementById('q-fz')?.addEventListener('input', qLiveCalc);
    document.getElementById('q-tz')?.addEventListener('input', qLiveCalc);

    ['q-fl', 'q-st', 'q-ef', 'q-ev', 'q-carry', 'q-wt', 'q-pc'].forEach((id) => {
        document.getElementById(id)?.addEventListener('input', qLiveCalc);
        document.getElementById(id)?.addEventListener('change', qLiveCalc);
    });

    document.querySelector('[data-quote-toggle="stairs"]')?.addEventListener('click', () => {
        toggleAccess('stairs-body', 'stairs-sw', 'stairs');
    });

    document.querySelector('[data-quote-toggle="elev"]')?.addEventListener('click', () => {
        toggleAccess('elev-body', 'elev-sw', 'elev');
    });

    document.getElementById('do-c')?.addEventListener('click', () => setDeliveryType('CURBSIDE'));
    document.getElementById('do-h')?.addEventListener('click', () => setDeliveryType('IN_HOME'));
    document.getElementById('crs-go-btn')?.addEventListener('click', () => buildAndOpenCrs());

    checkNarrow(document.getElementById('q-dw')?.value ?? '36');
    setDeliveryType('CURBSIDE');
}
