import { openPurchase } from '../prototype/shop.js';
import { showToast } from '../landing/toast.js';

let DR_INVENTORY = [];
let DR_AI_SUGGESTIONS = {};
let shopUrl = '/shop';

const DR_STATE = {
    roomType: 'living',
    selectedItem: null,
    addedFurniture: [],
    roomLength: 15,
    roomWidth: 12,
    roomHeight: 9,
    stream: null,
};

const PLACE_POSITIONS = [
    { top: '55%', left: '20%' },
    { top: '65%', left: '55%' },
    { top: '45%', left: '65%' },
    { top: '70%', left: '35%' },
];

function loadConfig() {
    const el = document.getElementById('design-room-config');

    if (!el) {
        return false;
    }

    const config = JSON.parse(el.textContent || '{}');
    DR_INVENTORY = config.inventory ?? [];
    DR_AI_SUGGESTIONS = config.ai_suggestions ?? {};
    shopUrl = config.shop_url ?? '/shop';

    return true;
}

function findItem(id) {
    return DR_INVENTORY.find((item) => item.id === id);
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function selectRoom(type, el) {
    DR_STATE.roomType = type;
    document.querySelectorAll('[data-dr-room]').forEach((btn) => btn.classList.remove('sh-dr-room-btn-active'));
    el?.classList.add('sh-dr-room-btn-active');
    drUpdateDims();
    drUpdateAISuggestions();
    drRenderCatalog();
}

function drUpdateDims() {
    const length = parseFloat(document.getElementById('dr-room-length')?.value ?? '15') || 15;
    const width = parseFloat(document.getElementById('dr-room-width')?.value ?? '12') || 12;
    const height = parseFloat(document.getElementById('dr-room-height')?.value ?? '9') || 9;

    DR_STATE.roomLength = length;
    DR_STATE.roomWidth = width;
    DR_STATE.roomHeight = height;

    const area = length * width;
    let msg = '';

    if (area < 100) {
        msg = 'Compact space — focus on multi-functional pieces and vertical storage.';
    } else if (area < 180) {
        msg = 'Standard size — fits a sofa, coffee table, and accent chairs comfortably.';
    } else if (area < 280) {
        msg = 'Spacious room — great for sectional sofas, dining sets, and statement pieces.';
    } else {
        msg = 'Large open space — consider zoning with rugs and furniture groupings.';
    }

    const analysis = document.getElementById('dr-fit-analysis');

    if (analysis) {
        analysis.innerHTML = `📐 <strong>${length} × ${width} ft room (${area.toFixed(0)} sq ft)</strong> — ${msg}`;
    }

    const dimsText = document.getElementById('dr-dims-text');

    if (dimsText) {
        dimsText.textContent = `${DR_STATE.roomType.charAt(0).toUpperCase()}${DR_STATE.roomType.slice(1)} · ${length}×${width} ft`;
    }
}

function drTakePhoto() {
    const overlay = document.getElementById('dr-camera-overlay');
    overlay?.classList.remove('hidden');
    overlay?.classList.add('flex');

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then((stream) => {
            DR_STATE.stream = stream;
            const video = document.getElementById('dr-video-layer');

            if (video) {
                video.srcObject = stream;
                video.classList.remove('hidden');
            }

            document.getElementById('dr-canvas-placeholder')?.classList.add('hidden');
        })
        .catch(() => {
            overlay?.classList.add('hidden');
            overlay?.classList.remove('flex');
            showToast('Camera unavailable', 'Please upload a photo instead.', 'error');
        });
}

function drCapturePhoto() {
    const video = document.getElementById('dr-video-layer');
    const canvas = document.getElementById('dr-capture-canvas');

    if (!video || !canvas) {
        return;
    }

    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    canvas.getContext('2d')?.drawImage(video, 0, 0);

    const photo = document.getElementById('dr-photo-layer');

    if (photo) {
        photo.src = canvas.toDataURL('image/jpeg');
        photo.classList.remove('hidden');
    }

    video.classList.add('hidden');
    drStopCamera();
    showToast('Photo captured', 'Now add furniture to your room design.', 'success');
}

function drStopCamera() {
    if (DR_STATE.stream) {
        DR_STATE.stream.getTracks().forEach((track) => track.stop());
        DR_STATE.stream = null;
    }

    const overlay = document.getElementById('dr-camera-overlay');
    overlay?.classList.add('hidden');
    overlay?.classList.remove('flex');
    document.getElementById('dr-video-layer')?.classList.add('hidden');
}

function drUploadFile() {
    document.getElementById('dr-file-input')?.click();
}

function drHandleFile(event) {
    const file = event.target.files?.[0];

    if (!file) {
        return;
    }

    const reader = new FileReader();

    if (file.type.startsWith('image/')) {
        reader.onload = (ev) => {
            const photo = document.getElementById('dr-photo-layer');

            if (photo && typeof ev.target?.result === 'string') {
                photo.src = ev.target.result;
                photo.classList.remove('hidden');
            }

            document.getElementById('dr-canvas-placeholder')?.classList.add('hidden');
            showToast('Room photo loaded', 'Select furniture to see it in your space.', 'success');
        };
        reader.readAsDataURL(file);
    } else if (file.type.startsWith('video/')) {
        reader.onload = (ev) => {
            const video = document.getElementById('dr-video-layer');

            if (video && typeof ev.target?.result === 'string') {
                video.src = ev.target.result;
                video.classList.remove('hidden');
                video.play().catch(() => {});
            }

            document.getElementById('dr-canvas-placeholder')?.classList.add('hidden');
            showToast('Room video loaded', 'Your video is ready on the canvas.', 'success');
        };
        reader.readAsDataURL(file);
    } else {
        showToast('Unsupported file', 'Please upload an image or video file.', 'error');
    }

    event.target.value = '';
}

function drClearCanvas() {
    document.getElementById('dr-photo-layer')?.classList.add('hidden');
    document.getElementById('dr-video-layer')?.classList.add('hidden');
    document.getElementById('dr-canvas-placeholder')?.classList.remove('hidden');

    const placed = document.getElementById('dr-placed-items');

    if (placed) {
        placed.innerHTML = '';
    }

    const summary = document.getElementById('dr-placed-summary');

    if (summary) {
        summary.textContent = 'None yet — add furniture from the catalog below';
    }

    drStopCamera();
}

function drFocusItem(id) {
    const item = findItem(id);

    if (item) {
        drSelectItem(item);
        document.getElementById('dr-item-detail')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function drSelectItem(item) {
    DR_STATE.selectedItem = item;
    document.getElementById('dr-no-item')?.classList.add('hidden');
    document.getElementById('dr-item-detail')?.classList.remove('hidden');

    document.getElementById('dr-det-name').textContent = item.name;
    document.getElementById('dr-det-sku').textContent = `${item.sku} · ${item.brand}`;
    document.getElementById('dr-det-icon').textContent = item.icon;
    document.getElementById('dr-det-price').textContent = `$${item.price.toLocaleString()}`;
    document.getElementById('dr-det-seller').textContent = `by ${item.seller}`;
    document.getElementById('dr-det-condition').innerHTML = `<span class="sh-dr-condition-pill">${escapeHtml(item.condition)}</span>`;

    const colorsEl = document.getElementById('dr-det-colors');
    colorsEl.innerHTML = `${(item.colors ?? []).map((color, index) => (
        `<button type="button" class="sh-dr-detail-color" style="background:${item.colorHex?.[index] ?? '#888'}" title="${escapeHtml(color)}" data-color-name="${escapeHtml(color)}" aria-label="${escapeHtml(color)}"></button>`
    )).join('')}<span class="text-xs text-sh-s500 ml-1 self-center" id="dr-selected-color">${escapeHtml(item.color)}</span>`;

    const dimParts = String(item.dims).replace(/"/g, '').split('×');
    const labels = ['Width', 'Depth', 'Height'];
    document.getElementById('dr-det-dims-grid').innerHTML = `${dimParts.map((dim, index) => (
        `<div class="sh-dr-dim-cell"><div class="text-[11px] text-sh-s500 mb-0.5">${labels[index] ?? ''}</div><div class="font-bold text-sm text-sh-blue">${escapeHtml(dim.trim())}"</div></div>`
    )).join('')}<div class="sh-dr-dim-cell sh-dr-dim-cell-muted"><div class="text-[11px] text-sh-s500 mb-0.5">Weight</div><div class="font-bold text-[13px] text-sh-s700">${escapeHtml(item.weight)}</div></div>`;

    const itemW = parseFloat(dimParts[0]) / 12;
    const itemD = parseFloat(dimParts[1]) / 12;
    const fitEl = document.getElementById('dr-fit-check');
    fitEl.classList.remove('sh-dr-fit-ok', 'sh-dr-fit-warn', 'sh-dr-fit-bad');

    if (itemW < DR_STATE.roomWidth * 0.75 && itemD < DR_STATE.roomLength * 0.5) {
        fitEl.classList.add('sh-dr-fit-ok');
        fitEl.innerHTML = `✅ Great fit! This piece works well in your ${DR_STATE.roomLength}×${DR_STATE.roomWidth} ft room.`;
    } else if (itemW < DR_STATE.roomWidth && itemD < DR_STATE.roomLength * 0.7) {
        fitEl.classList.add('sh-dr-fit-warn');
        fitEl.innerHTML = '⚠️ Tight but workable. Measure doorways before delivery.';
    } else {
        fitEl.classList.add('sh-dr-fit-bad');
        fitEl.innerHTML = '❌ Large piece — verify room clearances carefully.';
    }

    document.getElementById('dr-det-info').innerHTML = `
        <div class="mb-1.5">📦 <strong>Material:</strong> ${escapeHtml(item.material)} · <strong>Finish:</strong> ${escapeHtml(item.finish)}</div>
        <div class="mb-1.5">🔧 <strong>Assembly:</strong> ${escapeHtml(item.assembly)} · <strong>Warranty:</strong> ${escapeHtml(item.warranty)}</div>
        <div>${escapeHtml(item.description)}</div>
    `;

    drRenderCatalog();
}

function drSelectColor(button, colorName) {
    const label = document.getElementById('dr-selected-color');

    if (label) {
        label.textContent = colorName;
    }

    document.querySelectorAll('.sh-dr-detail-color').forEach((swatch) => swatch.classList.remove('sh-dr-detail-color-active'));
    button.classList.add('sh-dr-detail-color-active');
}

function drAddToRoomFromDetail() {
    if (DR_STATE.selectedItem) {
        drAddToRoom(DR_STATE.selectedItem);
    }
}

function drAddToRoom(item) {
    if (DR_STATE.addedFurniture.find((entry) => entry.id === item.id)) {
        showToast('Already in room', `${item.name} is already in your room.`, 'error');
        return;
    }

    DR_STATE.addedFurniture.push(item);
    drRenderFurnitureList();
    drPlaceOnCanvas(item);
    showToast('Added to room', `${item.icon} ${item.name} added to your room.`, 'success');
    drUpdateAISuggestions();
    drRenderCatalog();
}

function drMakeDraggable(el, container) {
    let isDragging = false;
    let offsetX = 0;
    let offsetY = 0;

    el.addEventListener('mousedown', (event) => {
        isDragging = true;
        offsetX = event.clientX - el.getBoundingClientRect().left;
        offsetY = event.clientY - el.getBoundingClientRect().top;
        el.style.zIndex = '10';
        event.preventDefault();
    });

    const onMove = (event) => {
        if (!isDragging) {
            return;
        }

        const rect = container.getBoundingClientRect();
        const x = ((event.clientX - rect.left - offsetX) / rect.width) * 100;
        const y = ((event.clientY - rect.top - offsetY) / rect.height) * 100;
        el.style.left = `${Math.max(0, Math.min(80, x))}%`;
        el.style.top = `${Math.max(0, Math.min(80, y))}%`;
    };

    const onUp = () => {
        isDragging = false;
        el.style.zIndex = '5';
    };

    document.addEventListener('mousemove', onMove);
    document.addEventListener('mouseup', onUp);

    el.addEventListener('touchstart', (event) => {
        const touch = event.touches[0];
        isDragging = true;
        offsetX = touch.clientX - el.getBoundingClientRect().left;
        offsetY = touch.clientY - el.getBoundingClientRect().top;
        el.style.zIndex = '10';
    }, { passive: true });

    document.addEventListener('touchmove', (event) => {
        if (!isDragging || !event.touches[0]) {
            return;
        }

        const touch = event.touches[0];
        const rect = container.getBoundingClientRect();
        const x = ((touch.clientX - rect.left - offsetX) / rect.width) * 100;
        const y = ((touch.clientY - rect.top - offsetY) / rect.height) * 100;
        el.style.left = `${Math.max(0, Math.min(80, x))}%`;
        el.style.top = `${Math.max(0, Math.min(80, y))}%`;
    }, { passive: true });

    document.addEventListener('touchend', onUp);
}

function drPlaceOnCanvas(item) {
    const container = document.getElementById('dr-placed-items');

    if (!container) {
        return;
    }

    const pos = PLACE_POSITIONS[DR_STATE.addedFurniture.length % PLACE_POSITIONS.length];
    const el = document.createElement('div');
    el.className = 'sh-dr-placed-item';
    el.style.top = pos.top;
    el.style.left = pos.left;
    el.setAttribute('data-item-id', item.id);
    el.innerHTML = `
        <span class="text-lg" aria-hidden="true">${item.icon}</span>
        <div class="min-w-0">
            <div class="max-w-[100px] truncate text-[11px] font-bold">${escapeHtml(item.name.split(' ').slice(0, 3).join(' '))}</div>
            <div class="text-[10px] text-violet-600">${escapeHtml(item.dims)}</div>
        </div>
    `;

    drMakeDraggable(el, container);
    container.appendChild(el);

    const summary = document.getElementById('dr-placed-summary');

    if (summary) {
        summary.textContent = DR_STATE.addedFurniture.map((entry) => `${entry.icon} ${entry.name.split(' ')[0]}`).join(', ');
    }
}

function drRenderFurnitureList() {
    const list = document.getElementById('dr-furniture-list');
    const checkoutRow = document.getElementById('dr-checkout-row');
    const totalBadge = document.getElementById('dr-total-badge');

    if (!list) {
        return;
    }

    if (!DR_STATE.addedFurniture.length) {
        list.innerHTML = `
            <div class="py-7 text-center text-[13px] text-sh-s400">
                <div class="text-[32px] mb-2" aria-hidden="true">🛋️</div>
                No furniture added yet. Browse the catalog below and click <strong>"Add to Room"</strong>
            </div>
        `;
        checkoutRow?.classList.add('hidden');

        if (totalBadge) {
            totalBadge.textContent = '$0.00 total';
        }

        return;
    }

    let total = 0;
    list.innerHTML = DR_STATE.addedFurniture.map((item) => {
        total += item.price;

        return `
            <div class="flex items-center gap-3 border-b border-sh-s50 py-3 last:border-b-0">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[10px] border-[1.5px] border-sh-s200 bg-sh-s50 text-[22px]">${item.icon}</div>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-[13px] font-bold text-sh-s900">${escapeHtml(item.name)}</div>
                    <div class="mt-0.5 text-[11px] text-sh-s500">${escapeHtml(item.dims)} · ${escapeHtml(item.color)}</div>
                </div>
                <div class="shrink-0 text-right">
                    <div class="text-sm font-bold text-violet-600">$${item.price.toLocaleString()}</div>
                    <button type="button" class="mt-0.5 text-[10px] font-semibold text-red-600" data-dr-remove="${item.id}">Remove</button>
                </div>
            </div>
        `;
    }).join('');

    if (totalBadge) {
        totalBadge.textContent = `$${total.toLocaleString()} total`;
    }

    checkoutRow?.classList.remove('hidden');
}

function drRemoveItem(id) {
    DR_STATE.addedFurniture = DR_STATE.addedFurniture.filter((item) => item.id !== id);
    document.querySelector(`[data-item-id="${id}"]`)?.remove();
    drRenderFurnitureList();
    drUpdateAISuggestions();
    drRenderCatalog();

    const summary = document.getElementById('dr-placed-summary');

    if (summary) {
        summary.textContent = DR_STATE.addedFurniture.length
            ? DR_STATE.addedFurniture.map((entry) => `${entry.icon} ${entry.name.split(' ')[0]}`).join(', ')
            : 'None yet — add furniture from the catalog below';
    }
}

function drRenderCatalog() {
    const grid = document.getElementById('dr-catalog-grid');

    if (!grid) {
        return;
    }

    const filter = document.getElementById('dr-cat-filter')?.value ?? '';
    const search = (document.getElementById('dr-search')?.value ?? '').toLowerCase();
    const items = DR_INVENTORY.filter((item) => {
        const catMatch = !filter || item.category === filter;
        const searchMatch = !search
            || item.name.toLowerCase().includes(search)
            || (item.tags ?? []).join(' ').toLowerCase().includes(search);

        return catMatch && searchMatch;
    });

    if (!items.length) {
        grid.innerHTML = '<div class="col-span-full py-12 text-center text-sm text-sh-s400">No items found. Try a different filter.</div>';
        return;
    }

    grid.innerHTML = items.map((item) => {
        const inRoom = DR_STATE.addedFurniture.find((entry) => entry.id === item.id);
        const isSelected = DR_STATE.selectedItem?.id === item.id;

        return `
            <article class="sh-dr-catalog-card ${isSelected ? 'sh-dr-catalog-card-selected' : ''}" data-dr-select="${item.id}">
                <div class="sh-dr-catalog-media">
                    <span class="text-[52px]" aria-hidden="true">${item.icon}</span>
                    ${inRoom ? '<span class="sh-dr-in-room-badge">In Room ✓</span>' : ''}
                    <div class="absolute bottom-2 left-2 flex gap-1">
                        ${(item.colors ?? []).slice(0, 4).map((color, index) => (
                            `<span class="h-3 w-3 rounded-full border-[1.5px] border-white/80" style="background:${item.colorHex?.[index] ?? '#888'}" title="${escapeHtml(color)}"></span>`
                        )).join('')}
                    </div>
                </div>
                <div class="p-3.5">
                    <div class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide text-sh-s400">${escapeHtml(item.category)} · ${escapeHtml(item.brand)}</div>
                    <div class="mb-1.5 line-clamp-2 text-[13px] font-bold leading-snug text-sh-s900">${escapeHtml(item.name)}</div>
                    <div class="mb-2 text-[10px] text-sh-s500">📐 ${escapeHtml(item.dims)}</div>
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-[17px] font-black text-violet-600">$${item.price.toLocaleString()}</div>
                        ${inRoom
                            ? `<button type="button" class="sh-dr-remove-btn" data-dr-remove="${item.id}">Remove</button>`
                            : `<button type="button" class="sh-dr-add-btn" data-dr-add="${item.id}">+ Add to Room</button>`}
                    </div>
                </div>
            </article>
        `;
    }).join('');
}

function drUpdateAISuggestions() {
    const el = document.getElementById('dr-ai-suggestions');

    if (!el) {
        return;
    }

    const toneClass = {
        pink: 'text-fuchsia-300',
        green: 'text-green-300',
        amber: 'text-amber-200',
    };

    let list = (DR_AI_SUGGESTIONS[DR_STATE.roomType] ?? DR_AI_SUGGESTIONS.living ?? [])
        .filter((suggestion) => !DR_STATE.addedFurniture.find((item) => item.id === suggestion.id));

    if (!list.length) {
        el.innerHTML = '<div class="sh-dr-ai-empty">🎉 Great room! You\'ve added the key pieces. Check cart to order.</div>';
        return;
    }

    el.innerHTML = list.map((suggestion) => {
        const item = findItem(suggestion.id);

        if (!item) {
            return '';
        }

        return `
            <div class="sh-dr-ai-card">
                <div class="text-[11px] font-bold uppercase tracking-wide mb-1.5 ${toneClass[suggestion.tone] ?? 'text-fuchsia-300'}">${escapeHtml(suggestion.label)}</div>
                <div class="text-[13px] font-semibold mb-0.5">${escapeHtml(item.name)}</div>
                <div class="text-[11px] opacity-55 mb-2">${escapeHtml(item.seller)} · $${item.price.toLocaleString()} · ${escapeHtml(suggestion.note)}</div>
                <button type="button" class="sh-dr-ai-btn" data-dr-focus="${item.id}">View &amp; Add →</button>
            </div>
        `;
    }).join('');
}

function drCheckoutAll() {
    if (!DR_STATE.addedFurniture.length) {
        return;
    }

    showToast('Opening checkout', `Preparing checkout for ${DR_STATE.addedFurniture.length} items…`, 'success');
    window.setTimeout(() => {
        window.location.href = shopUrl;
    }, 900);
}

function drSaveDesign() {
    const design = {
        roomType: DR_STATE.roomType,
        dims: `${DR_STATE.roomLength}×${DR_STATE.roomWidth} ft`,
        furniture: DR_STATE.addedFurniture.map((item) => item.id),
        total: DR_STATE.addedFurniture.reduce((sum, item) => sum + item.price, 0),
        savedAt: new Date().toISOString(),
    };

    localStorage.setItem('smoovehub_design_room', JSON.stringify(design));
    showToast('Design saved', `Total: $${design.total.toLocaleString()}`, 'success');
}

async function drShareDesign() {
    const shareUrl = `${window.location.origin}/design-room?share=${Date.now().toString(36)}`;

    try {
        await navigator.clipboard.writeText(shareUrl);
        showToast('Link copied', shareUrl, 'success');
    } catch {
        showToast('Share link', shareUrl, 'success');
    }
}

function drViewOnShop() {
    const item = DR_STATE.selectedItem;

    if (item?.shop_product_id) {
        openPurchase(item.shop_product_id);
        return;
    }

    window.location.href = shopUrl;
}

function drAddToWishlist() {
    if (!DR_STATE.selectedItem) {
        return;
    }

    const key = 'smoovehub_wishlist';
    const wishlist = JSON.parse(localStorage.getItem(key) ?? '[]');
    const ids = Array.isArray(wishlist) ? wishlist : [];

    if (!ids.includes(DR_STATE.selectedItem.id)) {
        ids.push(DR_STATE.selectedItem.id);
        localStorage.setItem(key, JSON.stringify(ids));
    }

    showToast('Saved to wishlist', DR_STATE.selectedItem.name, 'success');
}

export function initDesignRoom() {
    const page = document.getElementById('design-room-page');

    if (!page || !loadConfig()) {
        return;
    }

    drRenderCatalog();
    drUpdateDims();
    drUpdateAISuggestions();

    page.addEventListener('click', (event) => {
        const roomBtn = event.target.closest('[data-dr-room]');

        if (roomBtn) {
            selectRoom(roomBtn.getAttribute('data-dr-room'), roomBtn);
            return;
        }

        const selectBtn = event.target.closest('[data-dr-select]');

        if (selectBtn) {
            const item = findItem(selectBtn.getAttribute('data-dr-select'));

            if (item) {
                drSelectItem(item);
            }
            return;
        }

        const addBtn = event.target.closest('[data-dr-add]');

        if (addBtn) {
            event.stopPropagation();
            const item = findItem(addBtn.getAttribute('data-dr-add'));

            if (item) {
                drAddToRoom(item);
            }
            return;
        }

        const removeBtn = event.target.closest('[data-dr-remove]');

        if (removeBtn) {
            event.stopPropagation();
            drRemoveItem(removeBtn.getAttribute('data-dr-remove'));
            return;
        }

        const focusBtn = event.target.closest('[data-dr-focus]');

        if (focusBtn) {
            drFocusItem(focusBtn.getAttribute('data-dr-focus'));
            return;
        }

        const colorBtn = event.target.closest('.sh-dr-detail-color');

        if (colorBtn) {
            drSelectColor(colorBtn, colorBtn.getAttribute('data-color-name'));
        }
    });

    document.getElementById('dr-room-length')?.addEventListener('input', drUpdateDims);
    document.getElementById('dr-room-width')?.addEventListener('input', drUpdateDims);
    document.getElementById('dr-room-height')?.addEventListener('input', drUpdateDims);
    document.getElementById('dr-cat-filter')?.addEventListener('change', drRenderCatalog);
    document.getElementById('dr-search')?.addEventListener('input', drRenderCatalog);
    document.getElementById('dr-camera-btn')?.addEventListener('click', drTakePhoto);
    document.getElementById('dr-upload-btn')?.addEventListener('click', drUploadFile);
    document.getElementById('dr-reset-btn')?.addEventListener('click', drClearCanvas);
    document.getElementById('dr-capture-btn')?.addEventListener('click', drCapturePhoto);
    document.getElementById('dr-camera-cancel')?.addEventListener('click', drStopCamera);
    document.getElementById('dr-file-input')?.addEventListener('change', drHandleFile);
    document.getElementById('dr-save-btn')?.addEventListener('click', drSaveDesign);
    document.getElementById('dr-share-btn')?.addEventListener('click', drShareDesign);
    document.getElementById('dr-add-room-btn')?.addEventListener('click', drAddToRoomFromDetail);
    document.getElementById('dr-buy-btn')?.addEventListener('click', drViewOnShop);
    document.getElementById('dr-wishlist-btn')?.addEventListener('click', drAddToWishlist);
    document.getElementById('dr-checkout-btn')?.addEventListener('click', drCheckoutAll);
}
