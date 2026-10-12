@extends('layouts.landing')

@section('title', 'Delivery Quote — SmooveHub / CRS')

@section('content')
    <div id="quote-page">
        {{-- Hero --}}
        <section class="sh-quote-hero">
            <div class="sh-hero-pattern" aria-hidden="true"></div>
            <div class="sh-quote-hero-glow" aria-hidden="true"></div>
            <div class="sh-container relative z-[1]">
                <div class="sh-quote-badge">{{ $site['hero_badge'] }}</div>
                <h1 class="sh-quote-title">
                    Get Your<br>
                    <span class="text-sh-orange">Delivery Quote</span>
                </h1>
                <p class="sh-quote-subtitle">{{ $site['hero_subtitle'] }}</p>

                <div class="flex flex-wrap gap-2">
                    @foreach ($site['steps'] as $step)
                        <div class="sh-quote-step-pill">
                            <span class="sh-quote-step-num">{{ $step['number'] }}</span>
                            <span class="text-[13px] font-medium text-white/80">
                                @if ($step['highlight'] ?? false)
                                    <strong class="text-sh-orange">{{ $step['text'] }}</strong>
                                @else
                                    {{ $step['text'] }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Main body --}}
        <section class="sh-quote-body">
            <div class="sh-container">
                <div class="sh-quote-grid">
                    {{-- Left column --}}
                    <div class="flex flex-col gap-3.5">
                        <div class="sh-quote-form-card">
                            <div class="mb-[18px] flex items-center gap-3">
                                <div class="sh-quote-form-icon" aria-hidden="true">📋</div>
                                <div>
                                    <div class="sh-quote-form-heading">Access Details</div>
                                    <div class="text-xs text-sh-s500">Required for accurate CRS pricing</div>
                                </div>
                            </div>

                            <div class="mb-3.5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="sh-quote-label" for="q-fz">Pickup ZIP <span class="text-red-600">*</span></label>
                                    <input id="q-fz" class="sh-quote-input sh-quote-input-zip" type="text" maxlength="5" placeholder="75201" inputmode="numeric">
                                </div>
                                <div>
                                    <label class="sh-quote-label" for="q-tz">Delivery ZIP <span class="text-red-600">*</span></label>
                                    <input id="q-tz" class="sh-quote-input sh-quote-input-zip" type="text" maxlength="5" placeholder="75205" inputmode="numeric">
                                </div>
                            </div>

                            <div class="mb-[18px] grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="sh-quote-label" for="q-wt">Weight (lbs)</label>
                                    <input id="q-wt" class="sh-quote-input" type="number" placeholder="200" min="1">
                                </div>
                                <div>
                                    <label class="sh-quote-label" for="q-pc">Pieces</label>
                                    <input id="q-pc" class="sh-quote-input" type="number" placeholder="1" min="1">
                                </div>
                            </div>

                            <div class="mb-[18px] border-t border-sh-s100"></div>

                            <div class="sh-quote-panel mb-3">
                                <div class="mb-3 flex items-center gap-2">
                                    <span class="text-lg" aria-hidden="true">🚪</span>
                                    <div class="text-xs font-bold uppercase tracking-[0.8px] text-sh-s700">Door Dimensions</div>
                                </div>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="sh-quote-label" for="q-dw">Width (inches) <span class="text-red-600">*</span></label>
                                        <input id="q-dw" class="sh-quote-input" type="number" value="36" min="18" max="120">
                                        <p class="mt-1 text-[11px] text-sh-s400">Standard = 36"</p>
                                    </div>
                                    <div>
                                        <label class="sh-quote-label" for="q-dh">Height (inches) <span class="text-red-600">*</span></label>
                                        <input id="q-dh" class="sh-quote-input" type="number" value="80" min="60" max="120">
                                        <p class="mt-1 text-[11px] text-sh-s400">Standard = 80"</p>
                                    </div>
                                </div>
                                <div id="narrow-warn" class="sh-quote-narrow-warn hidden" role="alert"></div>
                            </div>

                            <div class="sh-quote-access-card mb-2.5">
                                <button type="button" class="sh-quote-access-toggle" data-quote-toggle="stairs">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-lg" aria-hidden="true">🪜</span>
                                        <div class="text-left">
                                            <div class="text-[13px] font-semibold text-sh-s900">Stairs at delivery</div>
                                            <div class="text-[11px] font-semibold text-green-600">Flights 1–3 FREE · $75/flight above 3rd</div>
                                        </div>
                                    </div>
                                    <div class="sh-switch" id="stairs-sw"><span class="sh-switch-knob"></span></div>
                                </button>
                                <div id="stairs-body" class="sh-quote-access-body">
                                    <div class="grid grid-cols-1 gap-3 bg-sh-s50 p-4 sm:grid-cols-2">
                                        <div>
                                            <label class="sh-quote-label" for="q-fl">Flights of stairs</label>
                                            <input id="q-fl" class="sh-quote-input" type="number" value="1" min="1" max="20">
                                            <p class="mt-1 text-[11px] font-semibold text-green-600">First 3 flights free</p>
                                        </div>
                                        <div>
                                            <label class="sh-quote-label" for="q-st">Stair type</label>
                                            <select id="q-st" class="sh-quote-input">
                                                <option value="standard">Standard</option>
                                                <option value="spiral">Spiral (+1 flight equiv.)</option>
                                                <option value="outdoor">Outdoor / Open</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="sh-quote-access-card mb-[18px]">
                                <button type="button" class="sh-quote-access-toggle" data-quote-toggle="elev">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-lg" aria-hidden="true">🛗</span>
                                        <div class="text-left">
                                            <div class="text-[13px] font-semibold text-sh-s900">Elevator available</div>
                                            <div class="text-[11px] text-sh-s500">No elevator + stairs = +$30</div>
                                        </div>
                                    </div>
                                    <div class="sh-switch" id="elev-sw"><span class="sh-switch-knob"></span></div>
                                </button>
                                <div id="elev-body" class="sh-quote-access-body">
                                    <div class="grid grid-cols-1 gap-3 bg-sh-s50 p-4 sm:grid-cols-2">
                                        <div>
                                            <label class="sh-quote-label" for="q-ef">Number of floors</label>
                                            <input id="q-ef" class="sh-quote-input" type="number" placeholder="1" min="1" max="50">
                                            <p class="mt-1 text-[11px] font-semibold text-green-600">Floors 1–3 FREE · $75/floor above 3rd</p>
                                        </div>
                                        <div>
                                            <label class="sh-quote-label" for="q-ev">Elevator interior width (in)</label>
                                            <input id="q-ev" class="sh-quote-input" type="number" placeholder="36" min="24" max="120">
                                            <p class="mt-1 text-[11px] text-sh-s400">Must be wider than door to clear</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="sh-quote-access-card mb-2.5">
                                <div class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-xl" aria-hidden="true">📏</span>
                                        <div>
                                            <div class="text-[13px] font-bold text-sh-s900">Long Carry Distance</div>
                                            <div class="text-[11px] text-sh-s500">First 75ft free · $100 flat if over 75ft</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 pb-3.5">
                                    <label class="sh-quote-label" for="q-carry">Estimated carry distance (feet)</label>
                                    <input id="q-carry" class="sh-quote-input" type="number" placeholder="0" min="0" max="500">
                                    <p class="mt-1 text-[11px] text-sh-s400">Distance from truck to delivery location. Under 75ft = no charge.</p>
                                </div>
                            </div>

                            <div class="sh-quote-waiting-note mb-[18px]">
                                <span class="text-lg" aria-hidden="true">⏱</span>
                                <div>
                                    <div class="mb-1 text-[13px] font-bold text-amber-900">Waiting Time Policy</div>
                                    <div class="text-xs leading-relaxed text-amber-950">
                                        The first <strong>30 minutes</strong> on-site are always <strong>free</strong>.
                                        If the owner or recipient is not present when movers arrive, a waiting fee of
                                        <strong>$150.00/hour</strong> will be charged for every hour (or part thereof) beyond the first 30 minutes.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-[18px]">
                                <label class="sh-quote-label mb-2">Delivery type</label>
                                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                                    <button type="button" id="do-c" class="sh-quote-delivery-option sh-quote-delivery-active">
                                        <div class="mb-1 text-xl" aria-hidden="true">🚚</div>
                                        <div class="sh-quote-delivery-title text-[13px] font-bold">Curbside</div>
                                        <div class="mt-0.5 text-[11px] text-sh-s600">Included in base</div>
                                    </button>
                                    <button type="button" id="do-h" class="sh-quote-delivery-option">
                                        <div class="mb-1 text-xl" aria-hidden="true">🏠</div>
                                        <div class="sh-quote-delivery-title text-[13px] font-bold text-sh-s700">In-Home</div>
                                        <div class="mt-0.5 text-[11px] font-semibold text-green-600">No extra charge</div>
                                    </button>
                                </div>
                            </div>

                            <div class="sh-quote-panel mb-[18px]">
                                <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.8px] text-sh-s500">Your info (optional — sent to CRS)</div>
                                <div class="mb-2.5 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                                    <div>
                                        <label class="sh-quote-label" for="q-nm">Name</label>
                                        <input id="q-nm" class="sh-quote-input" type="text" placeholder="Jane Smith">
                                    </div>
                                    <div>
                                        <label class="sh-quote-label" for="q-em">Email</label>
                                        <input id="q-em" class="sh-quote-input" type="email" placeholder="jane@email.com">
                                    </div>
                                </div>
                                <div>
                                    <label class="sh-quote-label" for="q-ph">Phone</label>
                                    <input id="q-ph" class="sh-quote-input max-w-[200px]" type="tel" placeholder="(555) 000-0000">
                                </div>
                            </div>

                            <div id="q-err" class="sh-quote-error hidden" role="alert"></div>

                            <button type="button" id="crs-go-btn" class="sh-quote-submit-btn">
                                🚛 Get CRS Delivery Quote →
                            </button>
                            <p class="mt-2 text-center text-[11px] text-sh-s400">Opens Competitive Relocation Service in a new tab with your details pre-filled</p>
                        </div>

                        <div id="estimate-card" class="sh-quote-estimate hidden">
                            <div class="sh-quote-estimate-head">
                                <div>
                                    <div id="q-rid" class="font-mono text-[11px] font-bold text-sh-blue"></div>
                                    <div id="q-rsub" class="mt-0.5 text-[13px] font-semibold text-sh-blue"></div>
                                </div>
                                <div id="q-rexp" class="text-right text-[11px] text-sh-s400"></div>
                            </div>
                            <div id="q-rpills" class="flex flex-wrap gap-1.5 px-5 pt-2.5"></div>
                            <div id="q-rlines"></div>
                            <div class="flex items-center justify-between border-t border-sh-s100 bg-sh-s50 px-5 py-3.5">
                                <div class="text-[13px] font-semibold text-sh-s600">SmooveHub estimate</div>
                                <div id="q-rtotal" class="sh-display text-[28px] font-black text-sh-blue"></div>
                            </div>
                            <div id="q-rcall" class="hidden border-t border-amber-200 bg-amber-50 px-5 py-3 text-xs text-amber-900">
                                200+ mile route — call
                                <a href="{{ $site['crs_phone_href'] }}" class="font-bold text-sh-blue">{{ $site['crs_phone'] }}</a>
                                for a custom quote.
                            </div>
                        </div>

                        <div id="access-summary-card" class="sh-quote-summary hidden">
                            <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.8px] text-sh-s500">Details sent to CRS</div>
                            <div id="access-summary-grid" class="grid grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-x-5"></div>
                            <p class="mt-3 border-t border-sh-s100 pt-2.5 text-[11px] text-sh-s400">Share these with your CRS agent for accurate final pricing.</p>
                        </div>
                    </div>

                    {{-- Right column --}}
                    <div class="sh-quote-sidebar">
                        <div class="sh-quote-crs-panel">
                            <div class="sh-quote-crs-head">
                                <div class="flex items-center gap-2.5">
                                    <div class="sh-quote-crs-icon" aria-hidden="true">🚛</div>
                                    <div>
                                        <div class="sh-display text-base font-extrabold text-white">CRS Delivery Quote</div>
                                        <div class="text-[11px] text-white/50">competitiverelocation.com</div>
                                    </div>
                                </div>
                                <a id="crs-open-tab-link" href="{{ $site['crs_base_url'] }}" target="_blank" rel="noopener" class="sh-quote-crs-open-link">
                                    Open in tab ↗
                                </a>
                            </div>

                            <div id="crs-placeholder" class="sh-quote-crs-placeholder">
                                <div class="sh-quote-crs-placeholder-icon" aria-hidden="true">🚛</div>
                                <div class="sh-display mb-2 text-[22px] font-extrabold text-sh-s900">CRS Live Quote</div>
                                <p class="mb-6 max-w-[300px] text-[13px] leading-relaxed text-sh-s500">
                                    Fill in your access details on the left, then click
                                    <strong>"Get CRS Delivery Quote"</strong> to load the CRS pricing page here.
                                </p>
                                <div class="flex w-full max-w-[280px] flex-col gap-2.5">
                                    <div class="sh-quote-contact-card">
                                        <span class="text-lg" aria-hidden="true">📞</span>
                                        <div>
                                            <div class="text-[11px] uppercase tracking-[0.5px] text-sh-s400">Phone</div>
                                            <a href="{{ $site['crs_phone_href'] }}" class="sh-display text-[17px] font-black text-sh-blue no-underline">{{ $site['crs_phone'] }}</a>
                                        </div>
                                    </div>
                                    <div class="sh-quote-contact-card">
                                        <span class="text-lg" aria-hidden="true">✉️</span>
                                        <div>
                                            <div class="text-[11px] uppercase tracking-[0.5px] text-sh-s400">Email</div>
                                            <a href="mailto:{{ $site['crs_email'] }}" class="text-xs font-semibold text-sh-orange no-underline">{{ $site['crs_email'] }}</a>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-5 text-[11px] text-sh-s300">Or click below to visit CRS directly</p>
                                <a href="{{ $site['crs_base_url'] }}" target="_blank" rel="noopener" class="sh-quote-crs-direct-link">
                                    Visit competitiverelocation.com →
                                </a>
                            </div>

                            <div id="crs-iframe-wrap" class="hidden">
                                <div class="border-b border-amber-200 bg-amber-50 px-4 py-2.5 text-xs text-amber-900">
                                    If the page doesn't load below,
                                    <a id="crs-fallback-link" href="{{ $site['crs_base_url'] }}" target="_blank" rel="noopener" class="font-bold text-sh-blue underline">click here to open CRS in a new tab →</a>
                                </div>
                                <iframe id="crs-iframe" src="" title="CRS Delivery Quote — Competitive Relocation Service" allow="forms" class="h-[600px] w-full border-0" loading="lazy"></iframe>
                            </div>
                        </div>

                        <div class="sh-quote-side-card">
                            <div class="sh-quote-side-title">SmooveHub Surcharges</div>
                            <div class="flex flex-col gap-2 text-[13px]">
                                @foreach ($site['surcharges'] as $surcharge)
                                    <div class="flex justify-between gap-3">
                                        <span class="text-sh-s600">{{ $surcharge['item'] }}</span>
                                        <span @class([
                                            'font-semibold',
                                            'text-green-600' => ($surcharge['tone'] ?? '') === 'green',
                                            'text-sh-s900' => ($surcharge['tone'] ?? '') !== 'green',
                                        ])>{{ $surcharge['amount'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="sh-quote-side-card">
                            <div class="sh-quote-side-title">CRS Distance Rules</div>
                            <div class="flex flex-col gap-2.5 text-[13px]">
                                @foreach ($site['distance_rules'] as $rule)
                                    <div class="flex items-center gap-2">
                                        <span aria-hidden="true">{{ $rule['icon'] }}</span>
                                        <div>
                                            <span class="font-semibold">{{ $rule['range'] }}</span>
                                            <span class="text-sh-s500">→ {{ $rule['status'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
