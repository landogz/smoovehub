@extends('layouts.landing')

@section('title', 'Design Me — Custom Furniture — SmooveHub')

@section('content')
    <div id="design-me-page">
        <script type="application/json" id="design-me-config">@json([
            'engine' => $site['engine'],
            'shop_url' => $site['shop_url'],
        ])</script>

        {{-- Hero --}}
        <section class="sh-dm-hero">
            <div class="sh-dm-hero-glow sh-dm-hero-glow-orange" aria-hidden="true"></div>
            <div class="sh-dm-hero-glow sh-dm-hero-glow-purple" aria-hidden="true"></div>
            <div class="sh-hero-pattern" aria-hidden="true"></div>
            <div class="sh-container relative z-[1]">
                <div class="sh-dm-badge">{{ $site['hero_badge'] }}</div>
                <h1 class="sh-dm-title">
                    Design Me <span class="text-sh-orange">A Furniture</span>
                </h1>
                <p class="sh-dm-subtitle">{{ $site['hero_subtitle'] }}</p>
                <div class="flex flex-wrap gap-3">
                    @foreach ($site['hero_stats'] as $stat)
                        <div class="sh-dm-stat-pill">
                            <div class="sh-dm-stat-value">{{ $stat['value'] }}</div>
                            <div class="sh-dm-stat-label">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Main --}}
        <section class="sh-dm-body">
            <div class="sh-container">
                <div class="sh-dm-grid">
                    {{-- Left: configurator --}}
                    <div class="flex flex-col gap-5">
                        {{-- Step 1 --}}
                        <div class="sh-dm-section">
                            <div class="sh-dm-step-header">
                                <div class="sh-dm-step-num">1</div>
                                <div>
                                    <div class="sh-dm-step-title">What are we building?</div>
                                    <div class="sh-dm-step-sub">Choose your furniture type</div>
                                </div>
                            </div>
                            <div class="sh-dm-type-grid">
                                @foreach ($site['types'] as $index => $type)
                                    <button
                                        type="button"
                                        class="sh-dm-type-btn @if ($index === 0) sh-dm-type-btn-active @endif"
                                        data-dm-type="{{ $type['key'] }}"
                                        id="dmt-{{ $type['key'] }}"
                                    >
                                        <div class="text-[32px] mb-2" aria-hidden="true">{{ $type['icon'] }}</div>
                                        <div class="sh-dm-type-name">{{ $type['label'] }}</div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="sh-dm-section">
                            <div class="sh-dm-step-header">
                                <div class="sh-dm-step-num">2</div>
                                <div>
                                    <div class="sh-dm-step-title">Choose Your Material</div>
                                    <div class="sh-dm-step-sub">Each material affects durability, price, and look</div>
                                </div>
                            </div>
                            <div class="sh-dm-mat-grid">
                                @foreach ($site['materials'] as $index => $material)
                                    <button
                                        type="button"
                                        class="sh-dm-mat-btn @if ($index === 0) sh-dm-mat-btn-active @endif"
                                        data-dm-material="{{ $material['key'] }}"
                                        id="dmm-{{ $material['key'] }}"
                                    >
                                        <div class="sh-dm-mat-swatch" style="background: {{ $material['swatch'] }}"></div>
                                        <div class="text-left">
                                            <div class="font-bold text-[13px] text-sh-s900">{{ $material['label'] }}</div>
                                            <div class="text-[11px] text-sh-s500 mt-0.5">{{ $material['desc'] }}</div>
                                            <div @class([
                                                'text-[11px] font-bold mt-0.5',
                                                'text-sh-orange' => ($material['price_tone'] ?? '') === 'orange',
                                                'text-green-600' => ($material['price_tone'] ?? '') === 'green',
                                            ])>{{ $material['price_label'] }}</div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="sh-dm-section">
                            <div class="sh-dm-step-header">
                                <div class="sh-dm-step-num">3</div>
                                <div>
                                    <div class="sh-dm-step-title">Set Your Dimensions</div>
                                    <div class="sh-dm-step-sub">Custom sizing — we build exactly to your specs</div>
                                </div>
                            </div>
                            <div class="sh-dm-slider-wrap">
                                <label class="sh-dm-slider-label">Width <span id="dm-w-val">72"</span></label>
                                <input type="range" class="sh-dm-range" id="dm-width" min="24" max="144" value="72">
                            </div>
                            <div class="sh-dm-slider-wrap">
                                <label class="sh-dm-slider-label">Depth <span id="dm-d-val">36"</span></label>
                                <input type="range" class="sh-dm-range" id="dm-depth" min="12" max="60" value="36">
                            </div>
                            <div class="sh-dm-slider-wrap">
                                <label class="sh-dm-slider-label">Height <span id="dm-h-val">30"</span></label>
                                <input type="range" class="sh-dm-range" id="dm-height" min="10" max="96" value="30">
                            </div>
                            <div class="mt-1">
                                <div class="text-[11px] font-bold uppercase tracking-wide text-sh-s500 mb-2.5">Quick Presets</div>
                                <div class="flex flex-wrap gap-2" id="dm-presets"></div>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="sh-dm-section">
                            <div class="sh-dm-step-header">
                                <div class="sh-dm-step-num">4</div>
                                <div>
                                    <div class="sh-dm-step-title">Color &amp; Finish</div>
                                    <div class="sh-dm-step-sub">Pick your primary color and surface finish</div>
                                </div>
                            </div>
                            <div class="mb-5">
                                <div class="text-[11px] font-bold uppercase tracking-wide text-sh-s500 mb-3">Primary Color</div>
                                <div class="sh-dm-color-grid" id="dm-colors-wrap"></div>
                                <div class="mt-2.5 flex flex-wrap items-center gap-2.5">
                                    <div id="dm-selected-color-swatch" class="sh-dm-selected-swatch" style="background:#8B6914"></div>
                                    <span id="dm-selected-color-name" class="text-[13px] font-semibold text-sh-s700">Natural Walnut</span>
                                    <input type="color" id="dm-custom-color" class="sh-dm-color-picker" value="#8B6914" title="Custom color">
                                    <span class="text-[11px] text-sh-s400">or pick custom →</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-[11px] font-bold uppercase tracking-wide text-sh-s500 mb-3">Surface Finish</div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($site['finishes'] as $index => $finish)
                                        <button
                                            type="button"
                                            class="sh-dm-finish-pill @if ($index === 0) sh-dm-finish-active @endif"
                                            data-dm-finish="{{ $finish }}"
                                        >{{ $finish }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div class="sh-dm-section">
                            <div class="sh-dm-step-header">
                                <div class="sh-dm-step-num">5</div>
                                <div>
                                    <div class="sh-dm-step-title">Features &amp; Add-ons</div>
                                    <div class="sh-dm-step-sub">Toggle the extras you want built in</div>
                                </div>
                            </div>
                            <div class="sh-dm-feature-grid" id="dm-features-grid"></div>
                        </div>

                        {{-- Step 6 --}}
                        <div class="sh-dm-section">
                            <div class="sh-dm-step-header">
                                <div class="sh-dm-step-num">6</div>
                                <div>
                                    <div class="sh-dm-step-title">Sketch &amp; Notes</div>
                                    <div class="sh-dm-step-sub">Draw a rough sketch or add special instructions</div>
                                </div>
                            </div>
                            <div class="mb-3.5">
                                <div class="mb-2.5 flex flex-wrap items-center justify-between gap-2">
                                    <div class="text-xs font-bold text-sh-s600">✏️ Sketch Area (draw with mouse or touch)</div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <input type="color" id="dm-pen-color" value="#1B3C87" class="sh-dm-color-picker">
                                        <select id="dm-pen-size" class="sh-dm-select">
                                            <option value="2">Fine</option>
                                            <option value="4" selected>Medium</option>
                                            <option value="8">Bold</option>
                                        </select>
                                        <button type="button" id="dm-clear-sketch" class="sh-dm-clear-btn">Clear</button>
                                    </div>
                                </div>
                                <canvas class="sh-dm-sketch-canvas" id="dm-sketch" width="800" height="400"></canvas>
                            </div>
                            <div class="mb-3.5">
                                <div class="text-xs font-bold text-sh-s600 mb-2">📷 Upload Reference Images (optional)</div>
                                <button type="button" id="dm-ref-drop" class="sh-dm-upload-zone">
                                    <div class="text-[28px] mb-1.5" aria-hidden="true">🖼️</div>
                                    <div class="text-[13px] font-semibold text-sh-s700">Drop images here or click to browse</div>
                                    <div class="text-[11px] text-sh-s400 mt-0.5">JPG, PNG, WEBP — max 5MB each</div>
                                </button>
                                <input type="file" id="dm-ref-input" multiple accept="image/*" class="hidden">
                                <div id="dm-ref-previews" class="mt-2.5 flex flex-wrap gap-2"></div>
                            </div>
                            <div>
                                <label class="sh-form-label" for="dm-notes">📝 Special Instructions</label>
                                <textarea id="dm-notes" class="sh-form-input min-h-[100px] resize-y text-[13px] leading-relaxed" rows="4" placeholder="E.g. I need extra-deep seat cushions, hidden cable management holes, a reinforced leg for a 350lb weight limit, specific wood grain direction, or any other custom detail…"></textarea>
                            </div>
                        </div>

                        {{-- Step 7 --}}
                        <div class="sh-dm-section">
                            <div class="sh-dm-step-header">
                                <div class="sh-dm-step-num">7</div>
                                <div>
                                    <div class="sh-dm-step-title">Delivery &amp; Timeline</div>
                                    <div class="sh-dm-step-sub">Choose your build priority and delivery method</div>
                                </div>
                            </div>
                            <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <button type="button" id="dmtl-standard" data-dm-timeline="standard" class="sh-dm-timeline-card sh-dm-timeline-active">
                                    <div class="text-[22px] mb-1.5" aria-hidden="true">📦</div>
                                    <div class="font-bold text-sm text-sh-s900">Standard Build</div>
                                    <div class="text-xs text-sh-s500 mt-0.5">21–28 days</div>
                                    <div class="text-xs font-bold text-sh-orange mt-1.5">Included</div>
                                </button>
                                <button type="button" id="dmtl-rush" data-dm-timeline="rush" class="sh-dm-timeline-card">
                                    <div class="text-[22px] mb-1.5" aria-hidden="true">⚡</div>
                                    <div class="font-bold text-sm text-sh-s900">Rush Build</div>
                                    <div class="text-xs text-sh-s500 mt-0.5">10–14 days</div>
                                    <div class="text-xs font-bold text-sh-orange mt-1.5">+$199</div>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <button type="button" id="dmdel-standard" data-dm-delivery="standard" class="sh-dm-delivery-card sh-dm-delivery-active">
                                    <div class="text-xl mb-1" aria-hidden="true">🚚</div>
                                    <div class="font-bold text-[13px] text-sh-s900">Standard Delivery</div>
                                    <div class="text-[11px] text-sh-s500 mt-0.5">Curbside drop-off · CRS</div>
                                    <div class="text-xs font-bold text-sh-blue mt-1">$89</div>
                                </button>
                                <button type="button" id="dmdel-whiteglove" data-dm-delivery="whiteglove" class="sh-dm-delivery-card">
                                    <div class="text-xl mb-1" aria-hidden="true">🤍</div>
                                    <div class="font-bold text-[13px] text-sh-s900">White Glove</div>
                                    <div class="text-[11px] text-sh-s500 mt-0.5">Room placement + assembly</div>
                                    <div class="text-xs font-bold text-sh-blue mt-1">$229</div>
                                </button>
                            </div>
                        </div>

                        {{-- Step 8 --}}
                        <div class="sh-dm-section">
                            <div class="sh-dm-step-header">
                                <div class="sh-dm-step-num">8</div>
                                <div>
                                    <div class="sh-dm-step-title">Your Details</div>
                                    <div class="sh-dm-step-sub">Where should we send the build confirmation?</div>
                                </div>
                            </div>
                            <div class="mb-3.5 grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                                <div class="sh-form-field mb-0">
                                    <label class="sh-form-label" for="dm-name">Full Name <span class="text-red-500">*</span></label>
                                    <input class="sh-form-input" id="dm-name" type="text" placeholder="Maria Santos">
                                </div>
                                <div class="sh-form-field mb-0">
                                    <label class="sh-form-label" for="dm-phone">Phone <span class="text-red-500">*</span></label>
                                    <input class="sh-form-input" id="dm-phone" type="tel" placeholder="+1 (555) 000-0000">
                                </div>
                            </div>
                            <div class="sh-form-field">
                                <label class="sh-form-label" for="dm-email">Email <span class="text-red-500">*</span></label>
                                <input class="sh-form-input" id="dm-email" type="email" placeholder="maria@email.com">
                            </div>
                            <div class="sh-form-field">
                                <label class="sh-form-label" for="dm-address">Delivery Address <span class="text-red-500">*</span></label>
                                <input class="sh-form-input" id="dm-address" type="text" placeholder="1234 Main St, City, State ZIP" data-address-suggest data-suggest-full="dm-address">
                            </div>
                            <div class="sh-form-field mb-0">
                                <label class="sh-form-label" for="dm-source">How did you hear about us?</label>
                                <select class="sh-form-input" id="dm-source">
                                    @foreach ($site['sources'] as $source)
                                        <option>{{ $source }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Right sidebar --}}
                    <aside class="sh-dm-sidebar">
                        <div class="sh-dm-preview-card">
                            <div class="sh-dm-preview-head">
                                <div class="font-bold text-sm text-sh-s900">Live Preview</div>
                                <div id="dm-preview-label" class="text-[11px] text-sh-s500">Sofa · solid oak · Natural Walnut</div>
                            </div>
                            <div class="sh-dm-preview-box">
                                <canvas id="dm-preview-canvas" width="360" height="360" class="h-full w-full"></canvas>
                            </div>
                            <div class="sh-dm-preview-actions">
                                <button type="button" id="dm-rotate-left" class="sh-dm-preview-btn">← Rotate</button>
                                <button type="button" id="dm-rotate-right" class="sh-dm-preview-btn">Rotate →</button>
                                <button type="button" id="dm-toggle-view" class="sh-dm-preview-btn sh-dm-preview-btn-dark">Toggle View</button>
                            </div>
                        </div>

                        <div class="sh-dm-quote-card">
                            <div class="sh-dm-quote-head">
                                <div class="font-bold text-sm text-white">Live Quote Estimate</div>
                                <div class="text-[11px] text-white/55 mt-0.5">Updates as you configure</div>
                            </div>
                            <div class="p-4">
                                <div id="dm-quote-lines" class="mb-3"></div>
                                <div class="sh-dm-total-box">
                                    <div class="text-[11px] font-bold uppercase tracking-wide text-amber-700 mb-1">Total Estimate</div>
                                    <div id="dm-total-price" class="sh-dm-total-price">$1,233</div>
                                    <div class="text-[11px] text-amber-700 mt-1">+ CRS delivery · Deposit 30% to confirm</div>
                                </div>
                                <p class="text-[11px] text-sh-s400 text-center my-3.5">💡 Exact quote confirmed within 24hrs of submission</p>
                                <div class="sh-dm-timeline-box">
                                    <div class="text-[11px] font-bold uppercase tracking-wide text-sh-s600 mb-3">Build Timeline</div>
                                    <div class="flex flex-col gap-2.5">
                                        <div class="flex items-start gap-2.5">
                                            <div class="sh-dm-timeline-dot bg-sh-orange"></div>
                                            <div>
                                                <div class="text-xs font-bold text-sh-s900">Order Confirmed</div>
                                                <div class="text-[11px] text-sh-s500">Day 0 · 30% deposit</div>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2.5">
                                            <div class="sh-dm-timeline-dot bg-sh-blue"></div>
                                            <div>
                                                <div class="text-xs font-bold text-sh-s900">Material Sourced</div>
                                                <div class="text-[11px] text-sh-s500">Day 2–5</div>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2.5">
                                            <div class="sh-dm-timeline-dot bg-violet-600"></div>
                                            <div>
                                                <div class="text-xs font-bold text-sh-s900">Build Begins</div>
                                                <div class="text-[11px] text-sh-s500">Day 5–7</div>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2.5">
                                            <div class="sh-dm-timeline-dot bg-green-600"></div>
                                            <div>
                                                <div class="text-xs font-bold text-sh-s900" id="dm-delivery-label">Delivered</div>
                                                <div class="text-[11px] text-sh-s500" id="dm-delivery-day">Day 21–28</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sh-dm-craftsmen-card">
                            <div class="font-bold text-sm mb-3.5">🔨 Matched Craftsmen</div>
                            <div class="flex flex-col gap-2.5">
                                @foreach ($site['craftsmen'] as $craftsman)
                                    <div class="sh-dm-craftsman-item">
                                        <div @class([
                                            'sh-dm-craftsman-avatar',
                                            'sh-dm-craftsman-avatar-orange' => ($craftsman['avatar'] ?? '') === 'orange',
                                            'sh-dm-craftsman-avatar-purple' => ($craftsman['avatar'] ?? '') === 'purple',
                                        ])>{{ $craftsman['initial'] }}</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-bold text-[13px]">{{ $craftsman['name'] }}</div>
                                            <div class="text-[11px] opacity-60 mt-0.5">{{ $craftsman['meta'] }}</div>
                                        </div>
                                        <div @class([
                                            'text-[11px] font-bold shrink-0',
                                            'text-green-300' => ($craftsman['badge_tone'] ?? '') === 'green',
                                            'text-fuchsia-300' => ($craftsman['badge_tone'] ?? '') === 'purple',
                                        ])>{{ $craftsman['badge'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="button" id="dm-submit-btn" class="sh-dm-submit-btn">🔨 Submit Custom Order</button>
                        <p class="text-center text-[11px] text-sh-s400 leading-relaxed">By submitting you agree to our custom build terms. A 30% deposit is required to begin production. Final quote confirmed within 24 hours.</p>
                    </aside>
                </div>
            </div>
        </section>

        {{-- Success modal --}}
        <div id="dm-success-modal" class="sh-dm-modal hidden" role="dialog" aria-modal="true" aria-labelledby="dm-success-title">
            <div class="sh-dm-modal-panel">
                <div class="text-[56px] mb-4" aria-hidden="true">🎉</div>
                <h2 id="dm-success-title" class="sh-dm-modal-title">Order Submitted!</h2>
                <p class="text-sm text-sh-s500 leading-relaxed mb-5">Your custom furniture order has been received. Our craftsmen team will review your specs and send a confirmed quote within <strong>24 hours</strong>.</p>
                <div id="dm-order-summary-box" class="sh-dm-summary-box"></div>
                <div class="flex flex-col gap-2.5 sm:flex-row">
                    <button type="button" id="dm-success-close" class="sh-dm-modal-btn sh-dm-modal-btn-muted flex-1">Back to Configure</button>
                    <button type="button" id="dm-success-shop" class="sh-dm-modal-btn sh-dm-modal-btn-dark flex-1">Browse Shop</button>
                </div>
            </div>
        </div>
    </div>
@endsection
