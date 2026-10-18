import axios from 'axios';

const DEBOUNCE_MS = 300;

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function fillField(id, value) {
    if (!id || value == null || value === '') {
        return;
    }

    const field = document.getElementById(id);

    if (!field) {
        return;
    }

    field.value = value;
    field.dispatchEvent(new Event('input', { bubbles: true }));
    field.dispatchEvent(new Event('change', { bubbles: true }));
}

function applySuggestion(input, hit) {
    const line1Id = input.dataset.suggestLine1;
    const cityId = input.dataset.suggestCity;
    const stateId = input.dataset.suggestState;
    const zipId = input.dataset.suggestZip;
    const fullId = input.dataset.suggestFull;

    if (fullId) {
        fillField(fullId, hit.label);
    }

    if (line1Id && hit.line1) {
        fillField(line1Id, hit.line1);
    }

    if (cityId && hit.city) {
        fillField(cityId, hit.city);
    }

    if (stateId && hit.state) {
        fillField(stateId, hit.state);
    }

    if (zipId && hit.zip) {
        fillField(zipId, hit.zip);
    }

    if (!fullId && !line1Id && zipId && hit.zip && input.id === zipId) {
        fillField(zipId, hit.zip);
    }
}

function positionList(input, list) {
    const rect = input.getBoundingClientRect();

    list.style.left = `${Math.round(rect.left)}px`;
    list.style.top = `${Math.round(rect.bottom + 4)}px`;
    list.style.width = `${Math.round(rect.width)}px`;
}

function bindAddressInput(input) {
    if (input.dataset.suggestBound === '1') {
        return;
    }

    input.dataset.suggestBound = '1';
    input.setAttribute('autocomplete', 'off');
    input.setAttribute('aria-autocomplete', 'list');

    const list = document.createElement('ul');
    list.className = 'sh-address-suggest-list hidden';
    list.setAttribute('role', 'listbox');
    document.body.appendChild(list);

    let items = [];
    let activeIndex = -1;
    let debounceId = 0;
    let requestSeq = 0;

    const hide = () => {
        list.classList.add('hidden');
        list.innerHTML = '';
        items = [];
        activeIndex = -1;
    };

    const highlight = () => {
        list.querySelectorAll('[data-suggest-index]').forEach((row, index) => {
            row.classList.toggle('is-active', index === activeIndex);
        });
    };

    const render = () => {
        if (items.length === 0) {
            hide();
            return;
        }

        list.innerHTML = items.map((hit, index) => (
            `<li role="option" data-suggest-index="${index}">${escapeHtml(hit.label)}</li>`
        )).join('');
        list.classList.remove('hidden');
        positionList(input, list);
        highlight();
    };

    const search = async (query) => {
        const seq = ++requestSeq;

        if (query.length < 2) {
            hide();
            return;
        }

        try {
            const response = await axios.get('/api/v1/site/address-suggest', {
                params: { q: query },
            });

            if (seq !== requestSeq) {
                return;
            }

            items = Array.isArray(response.data?.data) ? response.data.data : [];
            activeIndex = items.length ? 0 : -1;
            render();
        } catch {
            if (seq === requestSeq) {
                hide();
            }
        }
    };

    const choose = (index) => {
        const hit = items[index];

        if (!hit) {
            return;
        }

        applySuggestion(input, hit);
        hide();
    };

    input.addEventListener('input', () => {
        window.clearTimeout(debounceId);
        debounceId = window.setTimeout(() => {
            search(input.value.trim());
        }, DEBOUNCE_MS);
    });

    input.addEventListener('focus', () => {
        if (items.length) {
            render();
        }
    });

    input.addEventListener('keydown', (event) => {
        if (list.classList.contains('hidden') || items.length === 0) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            activeIndex = (activeIndex + 1) % items.length;
            highlight();
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            activeIndex = (activeIndex - 1 + items.length) % items.length;
            highlight();
        } else if (event.key === 'Enter' && activeIndex >= 0) {
            event.preventDefault();
            choose(activeIndex);
        } else if (event.key === 'Escape') {
            hide();
        }
    });

    list.addEventListener('mousedown', (event) => {
        const row = event.target.closest('[data-suggest-index]');

        if (!row) {
            return;
        }

        event.preventDefault();
        choose(Number(row.dataset.suggestIndex));
    });

    const reposition = () => {
        if (!list.classList.contains('hidden')) {
            positionList(input, list);
        }
    };

    window.addEventListener('resize', reposition);
    window.addEventListener('scroll', reposition, true);

    document.addEventListener('click', (event) => {
        if (event.target !== input && !list.contains(event.target)) {
            hide();
        }
    });
}

export function initAddressSuggest() {
    document.querySelectorAll('[data-address-suggest]').forEach((input) => {
        bindAddressInput(input);
    });
}
