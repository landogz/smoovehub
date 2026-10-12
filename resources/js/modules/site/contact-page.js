import axios from 'axios';
import { showToast } from '../landing/toast.js';

function value(id) {
    return document.getElementById(id)?.value.trim() ?? '';
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showContactError(message) {
    const el = document.getElementById('cf-err');

    if (!el) {
        return;
    }

    el.textContent = message;
    el.classList.remove('hidden');
}

function hideContactError() {
    document.getElementById('cf-err')?.classList.add('hidden');
}

function resetContactForm() {
    ['cf-fn', 'cf-ln', 'cf-email', 'cf-phone', 'cf-role', 'cf-subject', 'cf-order', 'cf-msg'].forEach((id) => {
        const field = document.getElementById(id);

        if (field) {
            field.value = '';
        }
    });

    hideContactError();
    document.getElementById('contact-form')?.classList.remove('hidden');
    document.getElementById('contact-success')?.classList.add('hidden');
}

function toggleFaq(index) {
    const body = document.getElementById(`faq-body-${index}`);
    const icon = document.getElementById(`faq-icon-${index}`);

    if (!body || !icon) {
        return;
    }

    const isOpen = body.style.maxHeight && body.style.maxHeight !== '0px';

    document.querySelectorAll('[data-faq-body]').forEach((panel) => {
        panel.style.maxHeight = '0';
    });

    document.querySelectorAll('[data-faq-icon]').forEach((faqIcon) => {
        faqIcon.textContent = '+';
        faqIcon.classList.remove('text-sh-blue');
        faqIcon.classList.add('text-sh-s400');
    });

    if (!isOpen) {
        body.style.maxHeight = `${body.scrollHeight + 20}px`;
        icon.textContent = '−';
        icon.classList.remove('text-sh-s400');
        icon.classList.add('text-sh-blue');
    }
}

export function initContactPage() {
    const page = document.getElementById('contact-page');

    if (!page) {
        return;
    }

    document.querySelectorAll('[data-faq-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            toggleFaq(Number(button.getAttribute('data-faq-toggle')));
        });
    });

    document.getElementById('contact-reset-btn')?.addEventListener('click', resetContactForm);

    document.getElementById('contact-submit-btn')?.addEventListener('click', async () => {
        const firstName = value('cf-fn');
        const lastName = value('cf-ln');
        const email = value('cf-email');
        const role = value('cf-role');
        const subject = value('cf-subject');
        const message = value('cf-msg');

        if (!firstName || !lastName || !email || !role || !subject || !message) {
            showContactError('Please fill in all required fields before sending.');
            return;
        }

        if (!isValidEmail(email)) {
            showContactError('Please enter a valid email address.');
            return;
        }

        hideContactError();

        const button = document.getElementById('contact-submit-btn');
        button?.setAttribute('disabled', 'disabled');

        try {
            const response = await axios.post('/api/v1/site/contact', {
                first_name: firstName,
                last_name: lastName,
                email,
                phone: value('cf-phone') || null,
                role,
                subject,
                order_number: value('cf-order') || null,
                message,
            });

            if (response.data?.status) {
                document.getElementById('contact-form')?.classList.add('hidden');
                document.getElementById('contact-success')?.classList.remove('hidden');

                const confirmEmail = document.getElementById('contact-confirm-email');

                if (confirmEmail) {
                    confirmEmail.textContent = email;
                }

                showToast('Message sent', response.data.message ?? 'We will respond within 2 business hours.', 'success');
            }
        } catch (error) {
            const errorMessage = error.response?.data?.message ?? 'Unable to send message. Please try again.';
            showContactError(errorMessage);
            showToast('Send failed', errorMessage, 'error');
        } finally {
            button?.removeAttribute('disabled');
        }
    });
}
