import axios from 'axios';
import { showToast } from '../landing/toast.js';

const TRUCK_TYPES = ['boxtruck', 'semitruck'];

const VEHICLE_LABELS = {
    car: '🚗 Car / SUV',
    van: '🚐 Cargo Van',
    pickup: '🛻 Pickup Truck',
    boxtruck: '🚛 Box Truck',
    semitruck: '🚚 Semi / 18-Wheeler',
};

const PAYOUT_LABELS = {
    ach: '🏦 ACH Direct Deposit',
    stripe: '💳 Stripe Connect',
    zelle: '⚡ Zelle',
    venmo: '💸 Venmo',
};

const PAYOUT_LABELS_PLAIN = {
    ach: 'ACH Direct Deposit',
    stripe: 'Stripe Connect',
    zelle: 'Zelle',
    venmo: 'Venmo',
};

const VEHICLE_LABELS_PLAIN = {
    car: 'Car / SUV',
    van: 'Cargo Van',
    pickup: 'Pickup Truck',
    boxtruck: 'Box Truck',
    semitruck: 'Semi / 18-Wheeler',
};

function value(id) {
    return document.getElementById(id)?.value.trim() ?? '';
}

function showStepError(step, message) {
    const el = document.getElementById(`drv-err-${step}`);

    if (!el) {
        return;
    }

    el.textContent = message;
    el.classList.remove('hidden');
}

function hideStepError(step) {
    document.getElementById(`drv-err-${step}`)?.classList.add('hidden');
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

export function initDriverWizard() {
    const formWrap = document.getElementById('driver-form-wrap');

    if (!formWrap) {
        return;
    }

    let currentStep = 1;
    let vehicleType = '';
    let payoutMethod = '';
    let submitting = false;

    const setProgressStep = (step) => {
        [1, 2, 3].forEach((index) => {
            const dot = document.getElementById(`drv-step-${index}-dot`);
            const label = document.querySelector(`[data-step-lbl="${index}"]`);
            const bar = document.querySelector(`[data-prog-bar="${index}"]`);

            if (!dot) {
                return;
            }

            dot.classList.remove('sh-drive-prog-dot-active', 'sh-drive-prog-dot-done', 'sh-drive-prog-dot-future');

            if (index < step) {
                dot.classList.add('sh-drive-prog-dot-done');
                dot.textContent = '✓';
                bar?.classList.remove('sh-drive-prog-bar-active', 'sh-drive-prog-bar-future');
                bar?.classList.add('sh-drive-prog-bar-done');
            } else if (index === step) {
                dot.classList.add('sh-drive-prog-dot-active');
                dot.textContent = index === 3 ? '✓' : String(index);

                if (bar && index < 3) {
                    bar.classList.remove('sh-drive-prog-bar-future', 'sh-drive-prog-bar-done');
                    bar.classList.add('sh-drive-prog-bar-active');
                }
            } else {
                dot.classList.add('sh-drive-prog-dot-future');
                dot.textContent = index === 3 ? '✓' : String(index);
                bar?.classList.remove('sh-drive-prog-bar-active', 'sh-drive-prog-bar-done');
                bar?.classList.add('sh-drive-prog-bar-future');
            }

            label?.classList.toggle('sh-drive-step-lbl-active', index === step);
        });
    };

    const showPanel = (step) => {
        [1, 2, 3].forEach((index) => {
            document.getElementById(`drv-s${index}`)?.classList.toggle('hidden', index !== step);
        });

        currentStep = step;
        setProgressStep(step);
    };

    const selectVehicle = (type) => {
        vehicleType = type;
        document.getElementById('drv-vehicle-type').value = type;

        document.querySelectorAll('[data-vehicle-type]').forEach((button) => {
            button.classList.toggle('sh-drive-vt-active', button.getAttribute('data-vehicle-type') === type);
        });

        const dotWrap = document.getElementById('drv-dot-wrap');
        const needsDot = TRUCK_TYPES.includes(type);

        dotWrap?.classList.toggle('hidden', !needsDot);

        if (!needsDot) {
            const dotInput = document.getElementById('drv-dot');

            if (dotInput) {
                dotInput.value = '';
            }
        }
    };

    const selectPayout = (method) => {
        payoutMethod = method;
        document.getElementById('drv-payout-method').value = method;

        document.querySelectorAll('[data-payout-method]').forEach((button) => {
            button.classList.toggle('sh-drive-pm-active', button.getAttribute('data-payout-method') === method);
        });

        ['ach', 'stripe', 'zelle', 'venmo'].forEach((key) => {
            document.getElementById(`drv-pf-${key}`)?.classList.toggle('hidden', key !== method);
        });
    };

    const validateStepOne = () => {
        const firstName = value('drv-fn');
        const lastName = value('drv-ln');
        const email = value('drv-email');
        const phone = value('drv-phone');

        if (!firstName || !lastName) {
            showStepError(1, 'Please enter your first and last name.');
            return false;
        }

        if (!email || !isValidEmail(email)) {
            showStepError(1, 'Please enter a valid email address.');
            return false;
        }

        if (!phone) {
            showStepError(1, 'Please enter your phone number.');
            return false;
        }

        if (!vehicleType) {
            showStepError(1, 'Please select your vehicle type.');
            return false;
        }

        if (TRUCK_TYPES.includes(vehicleType) && !value('drv-dot')) {
            showStepError(1, 'A DOT number is required for Box Trucks and Semi / 18-Wheelers.');
            return false;
        }

        hideStepError(1);

        return true;
    };

    const validateStepTwo = () => {
        if (!payoutMethod) {
            showStepError(2, 'Please select a payout method.');
            return false;
        }

        if (payoutMethod === 'ach') {
            const holder = value('drv-bk-holder');
            const bankName = value('drv-bk-name');
            const routing = value('drv-bk-routing');
            const account = value('drv-bk-account');

            if (!holder) {
                showStepError(2, 'Please enter the account holder name.');
                return false;
            }

            if (!bankName) {
                showStepError(2, 'Please enter your bank name.');
                return false;
            }

            if (!routing || routing.length !== 9) {
                showStepError(2, 'Please enter a valid 9-digit routing number.');
                return false;
            }

            if (!account || account.length < 6) {
                showStepError(2, 'Please enter your bank account number.');
                return false;
            }
        } else if (payoutMethod === 'stripe') {
            const stripeEmail = value('drv-stripe-email');

            if (!stripeEmail || !isValidEmail(stripeEmail)) {
                showStepError(2, 'Please enter a valid Stripe email address.');
                return false;
            }
        } else if (payoutMethod === 'zelle') {
            if (!value('drv-zelle')) {
                showStepError(2, 'Please enter your Zelle phone or email.');
                return false;
            }
        } else if (payoutMethod === 'venmo') {
            if (!value('drv-venmo')) {
                showStepError(2, 'Please enter your Venmo handle.');
                return false;
            }
        }

        hideStepError(2);

        return true;
    };

    const payoutDetail = () => {
        if (payoutMethod === 'ach') {
            const bankName = value('drv-bk-name');
            const routing = value('drv-bk-routing');
            const account = value('drv-bk-account');

            return `${bankName} · Routing: ••${routing.slice(-3)} · Account: ••••${account.slice(-4)}`;
        }

        if (payoutMethod === 'stripe') {
            return value('drv-stripe-email');
        }

        if (payoutMethod === 'zelle') {
            return value('drv-zelle');
        }

        if (payoutMethod === 'venmo') {
            return value('drv-venmo');
        }

        return '';
    };

    const buildReview = () => {
        const rows = [
            ['Name', `${value('drv-fn')} ${value('drv-ln')}`],
            ['Email', value('drv-email')],
            ['Phone', value('drv-phone')],
            ['Vehicle', VEHICLE_LABELS[vehicleType] ?? vehicleType],
            ['MC Number', value('drv-mc') || '—'],
        ];

        if (TRUCK_TYPES.includes(vehicleType)) {
            rows.push(['DOT Number', value('drv-dot')]);
        }

        const detail = payoutDetail();
        const payoutLabel = PAYOUT_LABELS[payoutMethod] ?? payoutMethod;
        rows.push(['Payout', detail ? `${payoutLabel} — ${detail}` : payoutLabel]);

        const card = document.getElementById('drv-review-card');

        if (!card) {
            return;
        }

        card.innerHTML = rows.map(([label, text]) => `
            <div class="sh-drive-review-row">
                <span class="sh-drive-review-label">${label}</span>
                <span class="sh-drive-review-value">${text}</span>
            </div>
        `).join('');
    };

    const buildPayload = () => {
        const payload = {
            first_name: value('drv-fn'),
            last_name: value('drv-ln'),
            email: value('drv-email'),
            phone: value('drv-phone'),
            vehicle_type: vehicleType,
            mc_number: value('drv-mc') || null,
            dot_number: value('drv-dot') || null,
            payout_method: payoutMethod,
            account_type: document.getElementById('drv-bk-acct-type')?.value ?? 'checking',
        };

        if (payoutMethod === 'ach') {
            payload.account_holder = value('drv-bk-holder');
            payload.bank_name = value('drv-bk-name');
            payload.routing_number = value('drv-bk-routing');
            payload.account_number = value('drv-bk-account');
        } else if (payoutMethod === 'stripe') {
            payload.stripe_email = value('drv-stripe-email');
        } else if (payoutMethod === 'zelle') {
            payload.zelle_contact = value('drv-zelle');
        } else if (payoutMethod === 'venmo') {
            payload.venmo_handle = value('drv-venmo');
        }

        return payload;
    };

    const showSuccess = () => {
        const summary = document.getElementById('drv-success-summary');

        if (summary) {
            summary.innerHTML = `
                <div class="flex flex-col gap-1">
                    <div><strong class="text-white/80">${value('drv-fn')} ${value('drv-ln')}</strong></div>
                    <div>${value('drv-email')}</div>
                    <div>${VEHICLE_LABELS_PLAIN[vehicleType] ?? vehicleType}</div>
                    <div>Payout via ${PAYOUT_LABELS_PLAIN[payoutMethod] ?? payoutMethod}</div>
                </div>
            `;
        }

        formWrap.classList.add('hidden');
        document.getElementById('driver-success')?.classList.remove('hidden');
    };

    const submitApplication = async () => {
        if (submitting) {
            return;
        }

        if (!value('drv-fn') || !value('drv-ln') || !value('drv-email')) {
            showStepError(3, 'Missing required fields. Please go back and complete all steps.');
            return;
        }

        submitting = true;
        const button = document.getElementById('driver-submit-btn');
        button?.setAttribute('disabled', 'disabled');

        try {
            const response = await axios.post('/api/v1/site/driver-application', buildPayload());

            if (response.data?.status) {
                showSuccess();
                showToast('Application submitted', response.data.message ?? 'CRS will review within 48 hours.', 'success');
            }
        } catch (error) {
            const message = error.response?.data?.message ?? 'Unable to submit application. Please try again.';
            showStepError(3, message);
            showToast('Submission failed', message, 'error');
        } finally {
            submitting = false;
            button?.removeAttribute('disabled');
        }
    };

    document.querySelectorAll('[data-vehicle-type]').forEach((button) => {
        button.addEventListener('click', () => selectVehicle(button.getAttribute('data-vehicle-type')));
    });

    document.querySelectorAll('[data-payout-method]').forEach((button) => {
        button.addEventListener('click', () => selectPayout(button.getAttribute('data-payout-method')));
    });

    document.querySelector('[data-driver-next="1"]')?.addEventListener('click', () => {
        if (validateStepOne()) {
            showPanel(2);
        }
    });

    document.querySelector('[data-driver-next="2"]')?.addEventListener('click', () => {
        if (validateStepTwo()) {
            buildReview();
            showPanel(3);
        }
    });

    document.querySelector('[data-driver-back]')?.addEventListener('click', () => {
        showPanel(Math.max(1, currentStep - 1));
    });

    document.querySelector('[data-driver-back-review]')?.addEventListener('click', () => {
        showPanel(2);
    });

    document.getElementById('driver-submit-btn')?.addEventListener('click', submitApplication);

    document.getElementById('drv-bk-routing')?.addEventListener('input', (event) => {
        event.target.value = event.target.value.replace(/\D/g, '');
    });

    document.getElementById('drv-bk-account')?.addEventListener('input', (event) => {
        event.target.value = event.target.value.replace(/\D/g, '');
    });

    showPanel(1);
}
