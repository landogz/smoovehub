import axios from 'axios';
import { showToast } from '../landing/toast.js';

let DM = {};
let DM_PRESETS = {};
let DM_MAT_MOD = {};
let DM_BASE = {};
let DM_FEATURES = {};
let DM_COLORS = [];
let refImages = [];
let shopUrl = '/shop';

function loadConfig() {
    const el = document.getElementById('design-me-config');

    if (!el) {
        return false;
    }

    const config = JSON.parse(el.textContent || '{}');
    const engine = config.engine ?? {};

    DM_PRESETS = engine.presets ?? {};
    DM_MAT_MOD = engine.matMod ?? {};
    DM_BASE = engine.base ?? {};
    DM_FEATURES = engine.features ?? {};
    DM_COLORS = engine.colors ?? [];
    shopUrl = config.shop_url ?? '/shop';

    DM = {
        type: 'sofa',
        material: 'solid-oak',
        color: DM_COLORS[0]?.hex ?? '#8B6914',
        colorName: DM_COLORS[0]?.name ?? 'Natural Walnut',
        finish: 'Matte',
        width: 72,
        depth: 36,
        height: 30,
        features: {},
        timeline: 'standard',
        delivery: 'standard',
        rotY: 0,
        viewMode: 'perspective',
    };

    return true;
}

function value(id) {
    return document.getElementById(id)?.value.trim() ?? '';
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function dmDarkenHex(hex, amt) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);

    return `#${[r, g, b].map((c) => Math.max(0, c - amt).toString(16).padStart(2, '0')).join('')}`;
}

function dmLightenHex(hex, amt) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);

    return `#${[r, g, b].map((c) => Math.min(255, c + amt).toString(16).padStart(2, '0')).join('')}`;
}

function dmRect3D(ctx, x, y, w, h, depth, topCol, sideCol, frontCol, ang) {
    const iso = Math.cos(ang || 0);

    ctx.fillStyle = topCol;
    ctx.beginPath();
    ctx.moveTo(x, y);
    ctx.lineTo(x + w, y);
    ctx.lineTo(x + w + depth * iso * 0.5, y - depth * 0.4);
    ctx.lineTo(x + depth * iso * 0.5, y - depth * 0.4);
    ctx.closePath();
    ctx.fill();

    ctx.fillStyle = frontCol || topCol;
    ctx.fillRect(x, y, w, h);

    ctx.fillStyle = sideCol;
    ctx.beginPath();
    ctx.moveTo(x + w, y);
    ctx.lineTo(x + w, y + h);
    ctx.lineTo(x + w + depth * iso * 0.5, y + h - depth * 0.4);
    ctx.lineTo(x + w + depth * iso * 0.5, y - depth * 0.4);
    ctx.closePath();
    ctx.fill();
}

function dmDrawSofa(ctx, col, dark, light, sw, sh, ang) {
    const w = 130 * sw;
    const h = 70 * sh;

    ctx.fillStyle = 'rgba(0,0,0,.3)';
    ctx.beginPath();
    ctx.ellipse(0, 80, w * 0.7, 16, 0, 0, Math.PI * 2);
    ctx.fill();
    dmRect3D(ctx, -w / 2, -20, w, h * 0.55, 18, col, dark, light, ang);
    dmRect3D(ctx, -w / 2, -20 - h * 0.4, w, h * 0.45, 10, col, dark, light, ang);
    dmRect3D(ctx, -w / 2 - 15, -20 - h * 0.1, 16, h * 0.55, 18, dark, dmDarkenHex(dark, 20), col, ang);
    dmRect3D(ctx, w / 2, -20 - h * 0.1, 16, h * 0.55, 18, dark, dmDarkenHex(dark, 20), col, ang);
    ctx.strokeStyle = 'rgba(255,255,255,.15)';
    ctx.lineWidth = 1.5;

    for (let i = 1; i < 3; i += 1) {
        ctx.beginPath();
        ctx.moveTo(-w / 2 + (i * w) / 3, -20);
        ctx.lineTo(-w / 2 + (i * w) / 3, -20 + h * 0.55);
        ctx.stroke();
    }

    ctx.fillStyle = dmDarkenHex(col, 60);
    [[-w / 2 + 8, h * 0.35], [-w / 2 + 8 + w * 0.3, h * 0.35], [w / 2 - 8, h * 0.35], [w / 2 - 8 - w * 0.3, h * 0.35]].forEach((p) => {
        ctx.fillRect(p[0] - 4, -20 + p[1], 8, 18);
    });
}

function dmDrawTable(ctx, col, dark, light, sw, sh, ang) {
    const w = 140 * sw;
    const d = 60;

    ctx.fillStyle = 'rgba(0,0,0,.3)';
    ctx.beginPath();
    ctx.ellipse(0, 70, w * 0.5, 14, 0, 0, Math.PI * 2);
    ctx.fill();
    dmRect3D(ctx, -w / 2, -20, w, 18, d * 0.4, col, dark, light, ang);
    ctx.fillStyle = dark;
    [[-w / 2 + 12, -20], [-w / 2 + 12 + w * 0.4, -20], [w / 2 - 12, -20], [w / 2 - 12 - w * 0.4, -20]].forEach((p) => {
        ctx.fillRect(p[0] - 5, p[1], 10, 80);
    });
    ctx.fillStyle = dmDarkenHex(dark, 10);
    ctx.fillRect(-w / 2 + 14, 30, w - 28, 8);
}

function dmDrawBed(ctx, col, dark, light, sw, sh, ang) {
    const w = 150 * sw;
    const h = 90;

    ctx.fillStyle = 'rgba(0,0,0,.3)';
    ctx.beginPath();
    ctx.ellipse(0, 80, w * 0.55, 14, 0, 0, Math.PI * 2);
    ctx.fill();
    dmRect3D(ctx, -w / 2, 0, w, h * 0.3, 20, dark, dmDarkenHex(dark, 20), col, ang);
    dmRect3D(ctx, -w / 2 + 6, -h * 0.3 + 4, w - 12, h * 0.55, 16, '#F5F0E8', '#E8DDD0', '#fff', ang);
    dmRect3D(ctx, -w / 2, -50, w, 54, 12, col, dark, light, ang);
    dmRect3D(ctx, -w / 2 + 20, -h * 0.3 - 8, w / 2 - 30, 22, 8, '#fff', '#E2E8F0', '#fff', ang);
    dmRect3D(ctx, 10, -h * 0.3 - 8, w / 2 - 30, 22, 8, '#fff', '#E2E8F0', '#fff', ang);
}

function dmDrawDesk(ctx, col, dark, light, sw, sh, ang) {
    const w = 140 * sw;

    ctx.fillStyle = 'rgba(0,0,0,.3)';
    ctx.beginPath();
    ctx.ellipse(0, 80, w * 0.45, 12, 0, 0, Math.PI * 2);
    ctx.fill();
    dmRect3D(ctx, -w / 2, -20, w, 14, 30, col, dark, light, ang);
    dmRect3D(ctx, -w / 2, -20, 16, 100, 20, dark, dmDarkenHex(dark, 20), col, ang);
    ctx.fillStyle = dark;
    ctx.fillRect(w / 2 - 10, -20, 10, 96);
    dmRect3D(ctx, w / 2 - 60, -20, 50, 50, 20, dmDarkenHex(col, 10), dark, light, ang);
    ctx.strokeStyle = 'rgba(255,255,255,.15)';
    ctx.lineWidth = 1;
    ctx.strokeRect(w / 2 - 56, -16, 42, 42);
    ctx.beginPath();
    ctx.arc(w / 2 - 35, -4 + 26, 5, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(255,255,255,.4)';
    ctx.fill();
}

function dmDrawChair(ctx, col, dark, light, sw, sh, ang) {
    const s = 0.8 * Math.max(sw, 0.7);

    ctx.fillStyle = 'rgba(0,0,0,.3)';
    ctx.beginPath();
    ctx.ellipse(0, 70, 50 * s, 12, 0, 0, Math.PI * 2);
    ctx.fill();
    dmRect3D(ctx, -40 * s, -10, 80 * s, 22, 24 * s, col, dark, light, ang);
    dmRect3D(ctx, -36 * s, -55, 72 * s, 50, 10, col, dark, light, ang);
    dmRect3D(ctx, -46 * s, -28, 12, 40, 8, dark, dmDarkenHex(dark, 20), col, ang);
    dmRect3D(ctx, 34 * s, -28, 12, 40, 8, dark, dmDarkenHex(dark, 20), col, ang);
    ctx.fillStyle = dmDarkenHex(col, 50);
    [[-28 * s, 12], [28 * s, 12], [-20 * s, 12], [20 * s, 12]].forEach((p, i) => {
        if (i < 2) {
            ctx.fillRect(p[0] - 4, p[1], 8, 56);
        }
    });
}

function dmDrawShelf(ctx, col, dark, light, sw, sh, ang) {
    const w = 110 * sw;
    const h = 160 * sh;

    ctx.fillStyle = 'rgba(0,0,0,.3)';
    ctx.beginPath();
    ctx.ellipse(0, 90, w * 0.4, 10, 0, 0, Math.PI * 2);
    ctx.fill();
    dmRect3D(ctx, -w / 2, -h / 2, 14, h, 16, dark, dmDarkenHex(dark, 20), col, ang);
    dmRect3D(ctx, w / 2 - 14, -h / 2, 14, h, 16, dark, dmDarkenHex(dark, 20), col, ang);

    for (let i = 0; i <= 3; i += 1) {
        dmRect3D(ctx, -w / 2 + 14, -h / 2 + i * (h / 3.5), w - 28, 12, 16, col, dark, light, ang);
    }

    ctx.fillStyle = 'rgba(0,0,0,.3)';
    ctx.fillRect(-w / 2 + 14, -h / 2 + 8, w - 28, h - 12);
    const bookColors = ['#EF4444', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6'];

    for (let b = 0; b < 3; b += 1) {
        const shelfY = -h / 2 + b * (h / 3.5) + 8;

        for (let k = 0; k < 5; k += 1) {
            ctx.fillStyle = bookColors[(b * 5 + k) % 5];
            ctx.fillRect(-w / 2 + 20 + k * 14, shelfY - 22, 10, 22);
        }
    }
}

function dmDrawCabinet(ctx, col, dark, light, sw, sh, ang) {
    const w = 120 * sw;
    const h = 160 * sh;

    ctx.fillStyle = 'rgba(0,0,0,.3)';
    ctx.beginPath();
    ctx.ellipse(0, 86, w * 0.4, 10, 0, 0, Math.PI * 2);
    ctx.fill();
    dmRect3D(ctx, -w / 2, -h / 2, w, h, 22, col, dark, light, ang);
    ctx.strokeStyle = 'rgba(255,255,255,.15)';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(0, -h / 2);
    ctx.lineTo(0, -h / 2 + h);
    ctx.stroke();
    ctx.fillStyle = 'rgba(255,255,255,.35)';
    ctx.beginPath();
    ctx.roundRect(-14, -8, 8, 24, 4);
    ctx.fill();
    ctx.beginPath();
    ctx.roundRect(6, -8, 8, 24, 4);
    ctx.fill();
    dmRect3D(ctx, -w / 2 - 4, -h / 2 - 10, w + 8, 14, 24, dark, dmDarkenHex(dark, 30), col, ang);
    ctx.fillStyle = dark;
    [[-w / 2 + 8, h / 2], [w / 2 - 16, h / 2]].forEach((p) => {
        ctx.fillRect(p[0], p[1], 12, 12);
    });
}

function dmDrawOutdoor(ctx, col, dark, light, sw, sh, ang) {
    dmDrawSofa(ctx, col, dark, light, sw * 0.85, sh, ang);
    ctx.fillStyle = '#16A34A';
    ctx.font = 'bold 28px serif';
    ctx.fillText('🌿', 90, -30);
}

function dmDrawPreview() {
    const canvas = document.getElementById('dm-preview-canvas');

    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext('2d');
    const W = canvas.width;
    const H = canvas.height;

    ctx.clearRect(0, 0, W, H);

    const bg = ctx.createLinearGradient(0, 0, W, H);
    bg.addColorStop(0, '#1E293B');
    bg.addColorStop(1, '#0F172A');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, W, H);

    ctx.strokeStyle = 'rgba(255,255,255,.05)';
    ctx.lineWidth = 1;

    for (let i = 0; i <= 10; i += 1) {
        ctx.beginPath();
        ctx.moveTo((i * W) / 10, 0);
        ctx.lineTo((i * W) / 10, H);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(0, (i * H) / 10);
        ctx.lineTo(W, (i * H) / 10);
        ctx.stroke();
    }

    const cx = W / 2;
    const cy = H / 2;
    const color = DM.color;
    const darkColor = dmDarkenHex(color, 40);
    const lightColor = dmLightenHex(color, 40);
    const angle = DM.rotY * (Math.PI / 180);
    const scaleW = Math.min(1.5, Math.max(0.5, DM.width / 72));
    const scaleH = Math.min(1.5, Math.max(0.5, DM.height / 34));

    ctx.save();
    ctx.translate(cx, cy);

    const drawers = {
        sofa: dmDrawSofa,
        table: dmDrawTable,
        bed: dmDrawBed,
        desk: dmDrawDesk,
        chair: dmDrawChair,
        shelf: dmDrawShelf,
        cabinet: dmDrawCabinet,
        outdoor: dmDrawOutdoor,
    };

    (drawers[DM.type] ?? dmDrawSofa)(ctx, color, darkColor, lightColor, scaleW, scaleH, angle);
    ctx.restore();

    ctx.fillStyle = 'rgba(255,255,255,.5)';
    ctx.font = '12px sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(`${DM.width}" W × ${DM.depth}" D × ${DM.height}" H`, W / 2, H - 14);
}

function dmUpdatePreviewLabel() {
    const el = document.getElementById('dm-preview-label');

    if (el) {
        el.textContent = `${DM.type.charAt(0).toUpperCase()}${DM.type.slice(1)} · ${DM.material.replace('-', ' ')} · ${DM.colorName}`;
    }
}

function dmUpdateSlider(input, labelId, suffix = '"') {
    const val = parseInt(input.value, 10);
    const label = document.getElementById(labelId);

    if (label) {
        label.textContent = `${val}${suffix}`;
    }

    const pct = (((val - input.min) / (input.max - input.min)) * 100).toFixed(1);
    input.style.setProperty('--pct', `${pct}%`);
    DM.width = parseInt(document.getElementById('dm-width')?.value ?? DM.width, 10);
    DM.depth = parseInt(document.getElementById('dm-depth')?.value ?? DM.depth, 10);
    DM.height = parseInt(document.getElementById('dm-height')?.value ?? DM.height, 10);
    dmDrawPreview();
}

function dmRenderPresets() {
    const wrap = document.getElementById('dm-presets');

    if (!wrap) {
        return;
    }

    const presets = DM_PRESETS[DM.type] ?? [];
    wrap.innerHTML = presets.map((p) => (
        `<button type="button" class="sh-dm-preset-btn" data-preset-w="${p.w}" data-preset-d="${p.d}" data-preset-h="${p.h}">${p.n} (${p.w}×${p.d}×${p.h}")</button>`
    )).join('');
}

function dmApplyPreset(w, d, h) {
    const width = document.getElementById('dm-width');
    const depth = document.getElementById('dm-depth');
    const height = document.getElementById('dm-height');

    if (width) {
        width.value = w;
    }
    if (depth) {
        depth.value = d;
    }
    if (height) {
        height.value = h;
    }

    document.getElementById('dm-w-val').textContent = `${w}"`;
    document.getElementById('dm-d-val').textContent = `${d}"`;
    document.getElementById('dm-h-val').textContent = `${h}"`;

    [['dm-width', w], ['dm-depth', d], ['dm-height', h]].forEach(([id, value]) => {
        const el = document.getElementById(id);

        if (el) {
            const pct = (((value - el.min) / (el.max - el.min)) * 100).toFixed(1);
            el.style.setProperty('--pct', `${pct}%`);
        }
    });

    DM.width = w;
    DM.depth = d;
    DM.height = h;
    dmRecalc();
    dmDrawPreview();
}

function dmRenderColors() {
    const wrap = document.getElementById('dm-colors-wrap');

    if (!wrap) {
        return;
    }

    wrap.innerHTML = DM_COLORS.map((c, i) => (
        `<button type="button" class="sh-dm-color-btn${i === 0 ? ' sh-dm-color-btn-active' : ''}" style="background:${c.hex}" title="${c.name}" data-color-hex="${c.hex}" data-color-name="${c.name}" aria-label="${c.name}"></button>`
    )).join('');
}

function dmSelectColor(hex, name, el) {
    DM.color = hex;
    DM.colorName = name;
    document.querySelectorAll('.sh-dm-color-btn').forEach((btn) => btn.classList.remove('sh-dm-color-btn-active'));

    if (el) {
        el.classList.add('sh-dm-color-btn-active');
    }

    const swatch = document.getElementById('dm-selected-color-swatch');
    const nameEl = document.getElementById('dm-selected-color-name');

    if (swatch) {
        swatch.style.background = hex;
    }
    if (nameEl) {
        nameEl.textContent = name;
    }

    dmDrawPreview();
    dmUpdatePreviewLabel();
}

function dmSelectType(type, el) {
    DM.type = type;
    document.querySelectorAll('[data-dm-type]').forEach((btn) => btn.classList.remove('sh-dm-type-btn-active'));

    if (el) {
        el.classList.add('sh-dm-type-btn-active');
    }

    dmRenderFeatures();
    dmRenderPresets();
    dmRecalc();
    dmUpdatePreviewLabel();
    dmDrawPreview();
}

function dmSelectMaterial(mat, el) {
    DM.material = mat;
    document.querySelectorAll('[data-dm-material]').forEach((btn) => btn.classList.remove('sh-dm-mat-btn-active'));

    if (el) {
        el.classList.add('sh-dm-mat-btn-active');
    }

    dmRecalc();
    dmUpdatePreviewLabel();
    dmDrawPreview();
}

function dmSelectFinish(finish, el) {
    DM.finish = finish;
    document.querySelectorAll('[data-dm-finish]').forEach((pill) => pill.classList.remove('sh-dm-finish-active'));

    if (el) {
        el.classList.add('sh-dm-finish-active');
    }
}

function dmRenderFeatures() {
    const grid = document.getElementById('dm-features-grid');

    if (!grid) {
        return;
    }

    const feats = DM_FEATURES[DM.type] ?? [];
    DM.features = {};
    grid.innerHTML = feats.map((f) => (
        `<button type="button" class="sh-dm-feature-toggle" id="dmf-${f.id}" data-feature-id="${f.id}" data-feature-price="${f.price}">
            <div>
                <div class="sh-dm-feature-label">${f.icon} ${f.label}</div>
                <div class="text-[11px] text-sh-s400 mt-0.5">${f.price > 0 ? `+$${f.price}` : 'Included'}</div>
            </div>
            <div class="sh-dm-feature-check" id="dmfc-${f.id}"></div>
        </button>`
    )).join('');
}

function dmToggleFeature(id, price, el) {
    if (DM.features[id]) {
        delete DM.features[id];
        el.classList.remove('sh-dm-feature-active');
        const check = document.getElementById(`dmfc-${id}`);

        if (check) {
            check.textContent = '';
            check.classList.remove('sh-dm-feature-check-on');
        }
    } else {
        DM.features[id] = price;
        el.classList.add('sh-dm-feature-active');
        const check = document.getElementById(`dmfc-${id}`);

        if (check) {
            check.textContent = '✓';
            check.classList.add('sh-dm-feature-check-on');
        }
    }

    dmRecalc();
}

function dmSelectTimeline(tl, el) {
    DM.timeline = tl;
    document.querySelectorAll('[data-dm-timeline]').forEach((btn) => btn.classList.remove('sh-dm-timeline-active'));
    el.classList.add('sh-dm-timeline-active');

    const dday = document.getElementById('dm-delivery-day');

    if (dday) {
        dday.textContent = tl === 'rush' ? 'Day 10–14' : 'Day 21–28';
    }

    dmRecalc();
}

function dmSelectDelivery(del, el) {
    DM.delivery = del;
    document.querySelectorAll('[data-dm-delivery]').forEach((btn) => btn.classList.remove('sh-dm-delivery-active'));
    el.classList.add('sh-dm-delivery-active');
    dmRecalc();
}

function dmRecalc() {
    const base = DM_BASE[DM.type] ?? 800;
    const matMod = DM_MAT_MOD[DM.material] ?? 0;
    const stdVol = 72 * 36 * 34;
    const vol = DM.width * DM.depth * DM.height;
    const sizeMod = Math.round((vol / stdVol - 1) * base * 0.4);
    const featureTotal = Object.values(DM.features).reduce((sum, val) => sum + val, 0);
    const rushMod = DM.timeline === 'rush' ? 199 : 0;
    const deliveryMod = DM.delivery === 'whiteglove' ? 229 : 89;
    const total = base + matMod + sizeMod + featureTotal + rushMod + deliveryMod;

    const lines = [
        { label: `Base price (${DM.type})`, val: `$${base.toLocaleString()}` },
        { label: `Material (${DM.material.replace('-', ' ')})`, val: `${matMod >= 0 ? '+' : ''}$${matMod.toLocaleString()}` },
        { label: 'Custom dimensions', val: `${sizeMod >= 0 ? '+' : ''}$${sizeMod.toLocaleString()}` },
    ];

    Object.keys(DM.features).forEach((id) => {
        const feat = (DM_FEATURES[DM.type] ?? []).find((f) => f.id === id);

        if (feat) {
            lines.push({ label: `${feat.icon} ${feat.label}`, val: `+$${feat.price}` });
        }
    });

    lines.push({
        label: DM.timeline === 'rush' ? '⚡ Rush build' : '📦 Standard build',
        val: rushMod > 0 ? `+$${rushMod}` : 'Included',
    });
    lines.push({
        label: DM.delivery === 'whiteglove' ? '🤍 White-glove delivery' : '🚚 Standard delivery',
        val: `$${deliveryMod}`,
    });

    const linesEl = document.getElementById('dm-quote-lines');

    if (linesEl) {
        linesEl.innerHTML = lines.map((line) => (
            `<div class="sh-dm-quote-row"><span class="sh-dm-quote-label">${line.label}</span><span class="sh-dm-quote-val">${line.val}</span></div>`
        )).join('');
    }

    const totalEl = document.getElementById('dm-total-price');

    if (totalEl) {
        totalEl.textContent = `$${total.toLocaleString()}`;
    }

    return total;
}

function dmInitSketch() {
    const canvas = document.getElementById('dm-sketch');

    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#FAFAFA';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.strokeStyle = '#E2E8F0';
    ctx.lineWidth = 1;

    for (let x = 0; x < canvas.width; x += 40) {
        ctx.beginPath();
        ctx.moveTo(x, 0);
        ctx.lineTo(x, canvas.height);
        ctx.stroke();
    }

    for (let y = 0; y < canvas.height; y += 40) {
        ctx.beginPath();
        ctx.moveTo(0, y);
        ctx.lineTo(canvas.width, y);
        ctx.stroke();
    }

    ctx.fillStyle = '#CBD5E1';
    ctx.font = '16px sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('Sketch your furniture here — front, side, or top view', canvas.width / 2, canvas.height / 2);

    let drawing = false;
    let lx = 0;
    let ly = 0;

    function getPos(e) {
        const r = canvas.getBoundingClientRect();
        const scaleX = canvas.width / r.width;
        const scaleY = canvas.height / r.height;

        if (e.touches) {
            return {
                x: (e.touches[0].clientX - r.left) * scaleX,
                y: (e.touches[0].clientY - r.top) * scaleY,
            };
        }

        return {
            x: (e.clientX - r.left) * scaleX,
            y: (e.clientY - r.top) * scaleY,
        };
    }

    canvas.addEventListener('mousedown', (e) => {
        drawing = true;
        const p = getPos(e);
        lx = p.x;
        ly = p.y;
    });

    canvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        drawing = true;
        const p = getPos(e);
        lx = p.x;
        ly = p.y;
    });

    canvas.addEventListener('mousemove', (e) => {
        if (!drawing) {
            return;
        }

        const p = getPos(e);
        ctx.strokeStyle = document.getElementById('dm-pen-color')?.value ?? '#1B3C87';
        ctx.lineWidth = parseInt(document.getElementById('dm-pen-size')?.value ?? '4', 10);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.beginPath();
        ctx.moveTo(lx, ly);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        lx = p.x;
        ly = p.y;
    });

    canvas.addEventListener('touchmove', (e) => {
        e.preventDefault();

        if (!drawing) {
            return;
        }

        const p = getPos(e);
        ctx.strokeStyle = document.getElementById('dm-pen-color')?.value ?? '#1B3C87';
        ctx.lineWidth = parseInt(document.getElementById('dm-pen-size')?.value ?? '4', 10);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.beginPath();
        ctx.moveTo(lx, ly);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        lx = p.x;
        ly = p.y;
    });

    ['mouseup', 'mouseleave', 'touchend'].forEach((ev) => {
        canvas.addEventListener(ev, () => {
            drawing = false;
        });
    });
}

function dmClearSketch() {
    const canvas = document.getElementById('dm-sketch');

    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#FAFAFA';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.strokeStyle = '#E2E8F0';
    ctx.lineWidth = 1;

    for (let x = 0; x < canvas.width; x += 40) {
        ctx.beginPath();
        ctx.moveTo(x, 0);
        ctx.lineTo(x, canvas.height);
        ctx.stroke();
    }

    for (let y = 0; y < canvas.height; y += 40) {
        ctx.beginPath();
        ctx.moveTo(0, y);
        ctx.lineTo(canvas.width, y);
        ctx.stroke();
    }
}

function dmHandleRefImages(event) {
    const files = Array.from(event.target.files ?? []);
    const wrap = document.getElementById('dm-ref-previews');

    files.forEach((file) => {
        if (file.size > 5 * 1024 * 1024) {
            showToast('File too large', `${file.name} exceeds 5MB.`, 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = (ev) => {
            const src = ev.target?.result;

            if (typeof src !== 'string') {
                return;
            }

            refImages.push(src);
            const img = document.createElement('img');
            img.src = src;
            img.className = 'sh-dm-ref-thumb';
            img.title = 'Click to remove';
            img.addEventListener('click', () => {
                refImages = refImages.filter((item) => item !== src);
                img.remove();
            });
            wrap?.appendChild(img);
        };
        reader.readAsDataURL(file);
    });

    event.target.value = '';
}

function buildOrderSummary(total, deposit, email, reference) {
    return [
        '<div class="grid grid-cols-1 gap-2 sm:grid-cols-2">',
        `<div><div class="text-[10px] font-bold uppercase text-sh-s400 mb-0.5">Reference</div><div class="font-bold">${reference}</div></div>`,
        `<div><div class="text-[10px] font-bold uppercase text-sh-s400 mb-0.5">Type</div><div class="font-bold">${DM.type.charAt(0).toUpperCase()}${DM.type.slice(1)}</div></div>`,
        `<div><div class="text-[10px] font-bold uppercase text-sh-s400 mb-0.5">Material</div><div class="font-bold">${DM.material.replace('-', ' ')}</div></div>`,
        `<div><div class="text-[10px] font-bold uppercase text-sh-s400 mb-0.5">Dimensions</div><div class="font-bold">${DM.width}"W × ${DM.depth}"D × ${DM.height}"H</div></div>`,
        `<div><div class="text-[10px] font-bold uppercase text-sh-s400 mb-0.5">Color</div><div class="flex items-center gap-1.5"><span class="inline-block h-3.5 w-3.5 rounded-full border border-sh-s200" style="background:${DM.color}"></span><span class="font-bold">${DM.colorName}</span></div></div>`,
        `<div><div class="text-[10px] font-bold uppercase text-sh-s400 mb-0.5">Finish</div><div class="font-bold">${DM.finish}</div></div>`,
        `<div><div class="text-[10px] font-bold uppercase text-sh-s400 mb-0.5">Timeline</div><div class="font-bold">${DM.timeline === 'rush' ? '⚡ Rush (10–14 days)' : '📦 Standard (21–28 days)'}</div></div>`,
        '</div>',
        '<div class="mt-3 flex justify-between border-t border-sh-s100 pt-3">',
        `<div><div class="text-[10px] font-bold uppercase text-sh-s400 mb-0.5">Order Total</div><div class="text-xl font-black text-sh-orange">$${total.toLocaleString()}</div></div>`,
        `<div><div class="text-[10px] font-bold uppercase text-sh-s400 mb-0.5">Deposit Due</div><div class="text-xl font-black text-sh-blue">$${deposit.toLocaleString()}</div></div>`,
        '</div>',
        `<div class="mt-2.5 text-xs text-sh-s500">📧 Confirmation sent to <strong>${email}</strong></div>`,
    ].join('');
}

async function dmSubmitOrder() {
    const name = value('dm-name');
    const email = value('dm-email');
    const phone = value('dm-phone');
    const address = value('dm-address');

    if (!name || !email || !phone || !address) {
        showToast('Missing details', 'Please fill in your name, email, phone, and delivery address.', 'error');
        return;
    }

    if (!isValidEmail(email)) {
        showToast('Invalid email', 'Please enter a valid email address.', 'error');
        return;
    }

    const total = dmRecalc();
    const deposit = Math.round(total * 0.3);
    const sketchCanvas = document.getElementById('dm-sketch');
    const sketchData = sketchCanvas ? sketchCanvas.toDataURL('image/png') : null;
    const button = document.getElementById('dm-submit-btn');

    button?.setAttribute('disabled', 'disabled');

    try {
        const response = await axios.post('/api/v1/site/design-me-order', {
            furniture_type: DM.type,
            material: DM.material,
            color: DM.color,
            color_name: DM.colorName,
            finish: DM.finish,
            width: DM.width,
            depth: DM.depth,
            height: DM.height,
            features: DM.features,
            timeline: DM.timeline,
            delivery_method: DM.delivery,
            contact_name: name,
            contact_email: email,
            contact_phone: phone,
            contact_address: address,
            source: value('dm-source') || null,
            notes: value('dm-notes') || null,
            estimated_price: total,
            deposit_amount: deposit,
            sketch_data: sketchData,
            reference_images: refImages.slice(0, 5),
        });

        if (response.data?.status) {
            const reference = response.data.data?.reference ?? 'DM-ORDER';
            const box = document.getElementById('dm-order-summary-box');
            const modal = document.getElementById('dm-success-modal');

            if (box) {
                box.innerHTML = buildOrderSummary(total, deposit, email, reference);
            }

            modal?.classList.remove('hidden');
            modal?.classList.add('flex');
            showToast('Order submitted', response.data.message ?? 'Your custom order has been received.', 'success');
        }
    } catch (error) {
        const errorMessage = error.response?.data?.message ?? 'Unable to submit order. Please try again.';
        showToast('Submission failed', errorMessage, 'error');
    } finally {
        button?.removeAttribute('disabled');
    }
}

function closeSuccessModal() {
    const modal = document.getElementById('dm-success-modal');
    modal?.classList.add('hidden');
    modal?.classList.remove('flex');
}

function initSliders() {
    ['dm-width', 'dm-depth', 'dm-height'].forEach((id, index) => {
        const input = document.getElementById(id);
        const labels = ['dm-w-val', 'dm-d-val', 'dm-h-val'];

        if (input) {
            dmUpdateSlider(input, labels[index]);
            input.addEventListener('input', () => {
                dmUpdateSlider(input, labels[index]);
                dmRecalc();
            });
        }
    });
}

export function initDesignMe() {
    const page = document.getElementById('design-me-page');

    if (!page || !loadConfig()) {
        return;
    }

    dmRenderColors();
    dmRenderFeatures();
    dmRenderPresets();
    initSliders();
    dmRecalc();
    dmInitSketch();
    dmDrawPreview();
    dmUpdatePreviewLabel();

    page.addEventListener('click', (event) => {
        const target = event.target.closest('[data-dm-type]');

        if (target) {
            dmSelectType(target.getAttribute('data-dm-type'), target);
            return;
        }

        const material = event.target.closest('[data-dm-material]');

        if (material) {
            dmSelectMaterial(material.getAttribute('data-dm-material'), material);
            return;
        }

        const finish = event.target.closest('[data-dm-finish]');

        if (finish) {
            dmSelectFinish(finish.getAttribute('data-dm-finish'), finish);
            return;
        }

        const feature = event.target.closest('[data-feature-id]');

        if (feature) {
            dmToggleFeature(
                feature.getAttribute('data-feature-id'),
                parseInt(feature.getAttribute('data-feature-price') ?? '0', 10),
                feature,
            );
            return;
        }

        const timeline = event.target.closest('[data-dm-timeline]');

        if (timeline) {
            dmSelectTimeline(timeline.getAttribute('data-dm-timeline'), timeline);
            return;
        }

        const delivery = event.target.closest('[data-dm-delivery]');

        if (delivery) {
            dmSelectDelivery(delivery.getAttribute('data-dm-delivery'), delivery);
            return;
        }

        const preset = event.target.closest('[data-preset-w]');

        if (preset) {
            dmApplyPreset(
                parseInt(preset.getAttribute('data-preset-w') ?? '0', 10),
                parseInt(preset.getAttribute('data-preset-d') ?? '0', 10),
                parseInt(preset.getAttribute('data-preset-h') ?? '0', 10),
            );
        }
    });

    page.addEventListener('click', (event) => {
        const colorBtn = event.target.closest('[data-color-hex]');

        if (colorBtn) {
            dmSelectColor(
                colorBtn.getAttribute('data-color-hex'),
                colorBtn.getAttribute('data-color-name'),
                colorBtn,
            );
        }
    });

    document.getElementById('dm-custom-color')?.addEventListener('input', (event) => {
        dmSelectColor(event.target.value, `Custom ${event.target.value}`, null);
    });

    document.getElementById('dm-clear-sketch')?.addEventListener('click', dmClearSketch);
    document.getElementById('dm-ref-input')?.addEventListener('change', dmHandleRefImages);
    document.getElementById('dm-ref-drop')?.addEventListener('click', () => document.getElementById('dm-ref-input')?.click());
    document.getElementById('dm-submit-btn')?.addEventListener('click', dmSubmitOrder);
    document.getElementById('dm-rotate-left')?.addEventListener('click', () => {
        DM.rotY -= 30;
        dmDrawPreview();
    });
    document.getElementById('dm-rotate-right')?.addEventListener('click', () => {
        DM.rotY += 30;
        dmDrawPreview();
    });
    document.getElementById('dm-toggle-view')?.addEventListener('click', () => {
        const modes = ['perspective', 'front', 'side', 'top'];
        const idx = modes.indexOf(DM.viewMode);
        DM.viewMode = modes[(idx + 1) % modes.length];
        dmDrawPreview();
    });
    document.getElementById('dm-success-close')?.addEventListener('click', closeSuccessModal);
    document.getElementById('dm-success-shop')?.addEventListener('click', () => {
        closeSuccessModal();
        window.location.href = shopUrl;
    });
}
