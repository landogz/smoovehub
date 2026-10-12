@extends('layouts.landing')

@section('title', 'Design Room — SmooveHub')

@section('content')
    <div id="design-room-page">
        @php
            $designRoomConfig = [
                'inventory' => $site['inventory'],
                'ai_suggestions' => $site['ai_suggestions'],
                'shop_url' => $site['shop_url'],
            ];
        @endphp
        <script type="application/json" id="design-room-config">@json($designRoomConfig)</script>

        {{-- Hero --}}
        <section class="sh-dr-hero">
            <div class="sh-hero-pattern" aria-hidden="true"></div>
            <div class="sh-dr-hero-glow" aria-hidden="true"></div>
            <div class="sh-container relative z-[1]">
                <div class="sh-dr-badge">{{ $site['hero_badge'] }}</div>
                <h1 class="sh-dr-title">
                    Design Your <span class="text-fuchsia-300">Dream Room</span>
                </h1>
                <p class="sh-dr-subtitle">{{ $site['hero_subtitle'] }}</p>
                <div class="flex flex-wrap gap-2.5 text-[13px] text-white/55">
                    @foreach ($site['hero_features'] as $feature)
                        <span>{{ $feature }}</span>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Main --}}
        <section class="sh-dr-body">
            <div class="sh-container">
                <div class="sh-dr-grid">
                    {{-- Left --}}
                    <div class="flex flex-col gap-5">
                        {{-- Step 1 --}}
                        <div class="sh-dr-section">
                            <div class="sh-dr-step-header">
                                <div class="sh-dr-step-num">1</div>
                                <div>
                                    <div class="sh-dr-step-title">Choose Room Type</div>
                                    <div class="sh-dr-step-sub">Select the room you're designing</div>
                                </div>
                            </div>
                            <div class="sh-dr-room-grid">
                                @foreach ($site['room_types'] as $index => $room)
                                    <button
                                        type="button"
                                        class="sh-dr-room-btn @if ($index === 0) sh-dr-room-btn-active @endif"
                                        data-dr-room="{{ $room['key'] }}"
                                    >
                                        <div class="text-[28px] mb-1.5" aria-hidden="true">{{ $room['icon'] }}</div>
                                        <div class="text-[11px] font-bold text-sh-s700">{{ $room['label'] }}</div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Step 2: Canvas --}}
                        <div class="sh-dr-canvas-card">
                            <div class="sh-dr-canvas-head">
                                <div class="sh-dr-step-header mb-0 border-0 pb-0">
                                    <div class="sh-dr-step-num">2</div>
                                    <div>
                                        <div class="sh-dr-step-title">Your Room Canvas</div>
                                        <div class="sh-dr-step-sub">Upload a photo or video, or use the virtual room</div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" id="dr-camera-btn" class="sh-dr-btn-purple">📷 Camera</button>
                                    <button type="button" id="dr-upload-btn" class="sh-dr-btn-navy">📁 Upload</button>
                                    <button type="button" id="dr-reset-btn" class="sh-dr-btn-muted">Reset</button>
                                </div>
                            </div>

                            <div id="dr-canvas-wrap" class="sh-dr-canvas-wrap">
                                <div id="dr-room-bg" class="absolute inset-0">
                                    <div id="dr-room-illustration" class="relative h-full min-h-[480px] w-full">
                                        <div class="sh-dr-virtual-wall" aria-hidden="true"></div>
                                        <div class="sh-dr-virtual-shadow" aria-hidden="true"></div>
                                        <div class="absolute bottom-0 left-0 right-0 h-[40%] overflow-hidden" aria-hidden="true">
                                            <div class="sh-dr-virtual-floor"></div>
                                        </div>
                                        <div class="sh-dr-virtual-window" aria-hidden="true"></div>
                                        <div class="sh-dr-virtual-baseboard" aria-hidden="true"></div>
                                        <div id="dr-canvas-placeholder" class="sh-dr-canvas-placeholder">
                                            <div class="sh-dr-placeholder-card">
                                                <div class="text-[32px] mb-2" aria-hidden="true">🏠</div>
                                                <div class="font-bold text-sm text-violet-900 mb-1">Your Virtual Room</div>
                                                <div class="text-xs text-violet-600">Upload a photo to use your real room, or drag furniture below to place items here</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <img id="dr-photo-layer" class="absolute inset-0 hidden h-full w-full object-cover" alt="Your room photo">
                                <video id="dr-video-layer" class="absolute inset-0 hidden h-full w-full object-cover" autoplay playsinline muted></video>
                                <div id="dr-placed-items" class="absolute inset-0"></div>

                                <div id="dr-dims-overlay" class="sh-dr-dims-overlay">
                                    📐 <span id="dr-dims-text">Living · 15×12 ft</span>
                                </div>

                                <div id="dr-camera-overlay" class="sh-dr-camera-overlay hidden">
                                    <div class="text-lg font-bold text-white text-center">Point your camera at the room</div>
                                    <div class="max-w-[280px] text-center text-[13px] text-white/70">Try to capture 3 walls for best furniture placement accuracy</div>
                                    <div class="flex flex-wrap justify-center gap-3">
                                        <button type="button" id="dr-capture-btn" class="sh-dr-btn-purple sh-dr-btn-lg">📸 Capture Photo</button>
                                        <button type="button" id="dr-camera-cancel" class="sh-dr-btn-outline-light">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <div class="sh-dr-canvas-toolbar">
                                <span class="text-xs font-semibold text-sh-s500">Placed items:</span>
                                <div id="dr-placed-summary" class="flex-1 text-xs text-sh-s400">None yet — add furniture from the catalog below</div>
                                <button type="button" id="dr-save-btn" class="sh-dr-btn-green">💾 Save Design</button>
                                <button type="button" id="dr-share-btn" class="sh-dr-btn-blue">📤 Share</button>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="sh-dr-section">
                            <div class="sh-dr-step-header">
                                <div class="sh-dr-step-num">3</div>
                                <div>
                                    <div class="sh-dr-step-title">Room Dimensions</div>
                                    <div class="sh-dr-step-sub">Helps us suggest furniture that fits perfectly</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3">
                                <div>
                                    <label class="sh-dr-field-label" for="dr-room-length">Room Length (ft)</label>
                                    <input class="sh-form-input" id="dr-room-length" type="number" value="15" min="6" max="60">
                                </div>
                                <div>
                                    <label class="sh-dr-field-label" for="dr-room-width">Room Width (ft)</label>
                                    <input class="sh-form-input" id="dr-room-width" type="number" value="12" min="6" max="60">
                                </div>
                                <div>
                                    <label class="sh-dr-field-label" for="dr-room-height">Ceiling Height (ft)</label>
                                    <input class="sh-form-input" id="dr-room-height" type="number" value="9" min="7" max="20">
                                </div>
                            </div>
                            <div id="dr-fit-analysis" class="sh-dr-fit-analysis mt-3.5">
                                📐 <strong>15 × 12 ft room (180 sq ft)</strong> — Spacious room — great for sectional sofas, dining sets, and statement pieces.
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="sh-dr-section">
                            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                <div class="sh-dr-step-header mb-0 border-0 pb-0">
                                    <div class="sh-dr-step-num">4</div>
                                    <div>
                                        <div class="sh-dr-step-title">My Room Furniture</div>
                                        <div class="sh-dr-step-sub">Items you've added to your design</div>
                                    </div>
                                </div>
                                <div id="dr-total-badge" class="sh-dr-total-badge">$0.00 total</div>
                            </div>
                            <div id="dr-furniture-list" class="min-h-[80px]">
                                <div class="py-7 text-center text-[13px] text-sh-s400">
                                    <div class="text-[32px] mb-2" aria-hidden="true">🛋️</div>
                                    No furniture added yet. Browse the catalog below and click <strong>"Add to Room"</strong>
                                </div>
                            </div>
                            <div id="dr-checkout-row" class="hidden mt-4 border-t border-sh-s50 pt-4">
                                <button type="button" id="dr-checkout-btn" class="sh-dr-checkout-btn">🛒 Add All to Cart &amp; Checkout</button>
                            </div>
                        </div>
                    </div>

                    {{-- Right sidebar --}}
                    <aside class="sh-dr-sidebar">
                        <div id="dr-item-detail" class="sh-dr-detail-card hidden">
                            <div class="sh-dr-detail-head">
                                <div class="text-[11px] font-semibold uppercase tracking-wide opacity-70 mb-1">Selected Item</div>
                                <div id="dr-det-name" class="sh-dr-detail-title"></div>
                                <div id="dr-det-sku" class="text-[11px] opacity-60 mt-1"></div>
                            </div>
                            <div class="p-[18px]">
                                <div class="mb-4 flex items-center gap-3.5">
                                    <div id="dr-det-icon" class="sh-dr-detail-icon"></div>
                                    <div>
                                        <div id="dr-det-price" class="text-2xl font-black text-violet-600"></div>
                                        <div id="dr-det-seller" class="text-xs text-sh-s500 mt-0.5"></div>
                                        <div id="dr-det-condition" class="mt-1"></div>
                                    </div>
                                </div>
                                <div class="mb-3.5">
                                    <div class="text-[11px] font-bold uppercase tracking-wide text-sh-s500 mb-2">Color / Finish</div>
                                    <div id="dr-det-colors" class="flex flex-wrap gap-2"></div>
                                </div>
                                <div class="sh-dr-dim-panel mb-3.5">
                                    <div class="text-[11px] font-bold uppercase tracking-wide text-sh-s500 mb-2.5">📐 Measurements</div>
                                    <div id="dr-det-dims-grid" class="grid grid-cols-2 gap-2"></div>
                                </div>
                                <div id="dr-fit-check" class="sh-dr-fit-check mb-3.5"></div>
                                <div id="dr-det-info" class="mb-4 text-xs leading-relaxed text-sh-s600"></div>
                                <div class="flex flex-col gap-2">
                                    <button type="button" id="dr-add-room-btn" class="sh-dr-action-primary">🛋️ Add to My Room</button>
                                    <button type="button" id="dr-buy-btn" class="sh-dr-action-navy">🛒 Buy Now on SmooveHub</button>
                                    <button type="button" id="dr-wishlist-btn" class="sh-dr-action-outline">❤️ Save to Wishlist</button>
                                </div>
                            </div>
                        </div>

                        <div id="dr-no-item" class="sh-dr-empty-card">
                            <div class="text-[40px] mb-3" aria-hidden="true">🪑</div>
                            <div class="font-bold text-[15px] text-violet-900 mb-1.5">Select a Furniture Item</div>
                            <div class="text-[13px] text-violet-600 leading-relaxed">Click any item in the catalog below to see colors, measurements, and how it fits in your room</div>
                        </div>

                        <div class="sh-dr-ai-panel">
                            <div class="mb-3.5 flex items-center gap-2">
                                <div class="text-xl" aria-hidden="true">🤖</div>
                                <div>
                                    <div class="font-bold text-sm">AI Room Suggestions</div>
                                    <div class="text-[11px] opacity-60">Based on your room type &amp; selected pieces</div>
                                </div>
                            </div>
                            <div id="dr-ai-suggestions" class="flex flex-col gap-2.5"></div>
                        </div>

                        <div class="sh-dr-style-card">
                            <div class="font-bold text-sm text-sh-s900 mb-3">🎨 Style Guide</div>
                            <div class="text-xs text-sh-s500 mb-3">
                                Your current selections suggest a <strong class="text-violet-600">{{ $site['style_guide']['aesthetic'] }}</strong> aesthetic. Recommended accents:
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($site['style_guide']['accents'] as $accent)
                                    <div class="sh-dr-style-pill">{{ $accent }}</div>
                                @endforeach
                            </div>
                        </div>
                    </aside>
                </div>

                {{-- Catalog --}}
                <div class="mt-2.5">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="sh-dr-catalog-title">SmooveHub Furniture Catalog</h2>
                            <p class="mt-0.5 text-[13px] text-sh-s500">Click any item to view full details, colors, and measurements — then add it to your room</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <select id="dr-cat-filter" class="sh-dr-select">
                                <option value="">All Categories</option>
                                @foreach ($site['catalog_categories'] as $category)
                                    <option value="{{ $category }}">{{ $category === 'Living' ? 'Living Room' : $category }}</option>
                                @endforeach
                            </select>
                            <input id="dr-search" class="sh-dr-search" type="search" placeholder="🔍 Search furniture…">
                        </div>
                    </div>
                    <div id="dr-catalog-grid" class="sh-dr-catalog-grid"></div>
                </div>
            </div>
        </section>

        <input type="file" id="dr-file-input" accept="image/*,video/*" class="hidden">
        <canvas id="dr-capture-canvas" class="hidden"></canvas>
    </div>
@endsection
