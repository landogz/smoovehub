@extends('layouts.landing')

@section('title', 'Become a Driver — SmooveHub / CRS')

@section('content')
    <section class="sh-drive-hero">
        <div class="sh-container sh-drive-grid">
            <div>
                <div class="sh-drive-badge">{{ $site['hero_badge'] }}</div>
                <h1 class="sh-drive-title">
                    Drive With<br>
                    <span class="text-sh-orange">SmooveHub</span>
                </h1>
                <p class="sh-drive-subtitle">{{ $site['hero_subtitle'] }}</p>

                <div class="mt-7 flex flex-col gap-4">
                    @foreach ($site['benefit_items'] as $benefit)
                        <div class="flex items-start gap-3.5">
                            <div @class([
                                'sh-drive-benefit-icon',
                                'sh-drive-benefit-icon-blue' => ($benefit['tone'] ?? '') === 'blue',
                                'sh-drive-benefit-icon-orange' => ($benefit['tone'] ?? '') === 'orange',
                                'sh-drive-benefit-icon-green' => ($benefit['tone'] ?? '') === 'green',
                            ]) aria-hidden="true">{{ $benefit['icon'] }}</div>
                            <div>
                                <div class="mb-0.5 text-sm font-semibold text-white">{{ $benefit['title'] }}</div>
                                <div class="text-xs leading-snug text-white/45">{{ $benefit['text'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="min-w-0">
                <div id="driver-form-wrap" class="sh-drive-form-panel">
                    <div class="mb-5 flex items-center gap-0">
                        <div id="drv-step-1-dot" class="sh-drive-prog-dot sh-drive-prog-dot-active" data-step-dot="1">1</div>
                        <div id="drv-prog-1" class="sh-drive-prog-bar sh-drive-prog-bar-active" data-prog-bar="1"></div>
                        <div id="drv-step-2-dot" class="sh-drive-prog-dot sh-drive-prog-dot-future" data-step-dot="2">2</div>
                        <div id="drv-prog-2" class="sh-drive-prog-bar sh-drive-prog-bar-future" data-prog-bar="2"></div>
                        <div id="drv-step-3-dot" class="sh-drive-prog-dot sh-drive-prog-dot-future" data-step-dot="3">✓</div>
                    </div>

                    <div class="mb-5 flex justify-between border-b border-white/[0.08] pb-4 text-[10px] font-semibold uppercase tracking-[0.5px] text-white/35">
                        <span id="drv-step-lbl-1" class="sh-drive-step-lbl-active" data-step-lbl="1">Driver Info</span>
                        <span id="drv-step-lbl-2" data-step-lbl="2">Bank Payout</span>
                        <span id="drv-step-lbl-3" data-step-lbl="3">Review</span>
                    </div>

                    {{-- Step 1 --}}
                    <div id="drv-s1" data-driver-step-panel="1">
                        <h2 class="sh-drive-step-title">Step 1 — Driver Information</h2>

                        <div class="mb-2.5 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                            <div>
                                <label class="sh-drive-label" for="drv-fn">First Name *</label>
                                <input id="drv-fn" class="sh-drive-input" type="text" name="first_name" placeholder="John" autocomplete="given-name">
                            </div>
                            <div>
                                <label class="sh-drive-label" for="drv-ln">Last Name *</label>
                                <input id="drv-ln" class="sh-drive-input" type="text" name="last_name" placeholder="Smith" autocomplete="family-name">
                            </div>
                        </div>

                        <div class="mb-2.5">
                            <label class="sh-drive-label" for="drv-email">Email *</label>
                            <input id="drv-email" class="sh-drive-input" type="email" name="email" placeholder="john@trucking.com" autocomplete="email">
                        </div>

                        <div class="mb-2.5">
                            <label class="sh-drive-label" for="drv-phone">Phone *</label>
                            <input id="drv-phone" class="sh-drive-input" type="tel" name="phone" placeholder="(555) 000-0000" autocomplete="tel">
                        </div>

                        <div class="mb-3.5">
                            <label class="sh-drive-label mb-2">Vehicle Type *</label>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2" id="drv-vehicle-grid">
                                @foreach ($site['vehicles'] as $vehicle)
                                    <button
                                        type="button"
                                        id="drv-vt-{{ $vehicle['value'] }}"
                                        class="sh-drive-vt-option @if ($vehicle['wide']) sm:col-span-2 @endif"
                                        data-vehicle-type="{{ $vehicle['value'] }}"
                                    >
                                        <span class="text-xl" aria-hidden="true">{{ $vehicle['icon'] }}</span>
                                        <div class="text-left">
                                            <div class="text-[13px] font-semibold text-white">{{ $vehicle['label'] }}</div>
                                            <div class="text-[10px] text-white/35">{{ $vehicle['subtext'] }}</div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" id="drv-vehicle-type" name="vehicle_type" value="">
                        </div>

                        <div class="mb-2.5">
                            <label class="sh-drive-label" for="drv-mc">
                                MC Number
                                <span class="text-[9px] normal-case tracking-normal text-white/25">(Motor Carrier — if applicable)</span>
                            </label>
                            <input id="drv-mc" class="sh-drive-input" type="text" name="mc_number" placeholder="MC-123456">
                        </div>

                        <div id="drv-dot-wrap" class="mb-2.5 hidden">
                            <label class="sh-drive-label text-sh-orange" for="drv-dot">
                                DOT Number *
                                <span class="ml-1 rounded bg-sh-orange/20 px-1.5 py-px text-[9px] text-sh-orange">Required for trucks</span>
                            </label>
                            <input id="drv-dot" class="sh-drive-input sh-drive-input-dot" type="text" name="dot_number" placeholder="DOT-987654">
                            <p class="mt-1.5 text-[11px] text-sh-orange/60">A valid USDOT number is required for commercial trucks over 10,001 lbs. Find yours at safer.fmcsa.dot.gov</p>
                        </div>

                        <div id="drv-err-1" class="sh-drive-error hidden" role="alert"></div>

                        <button type="button" class="sh-drive-btn-primary mt-1 w-full" data-driver-next="1">
                            Continue to Bank Payout →
                        </button>
                        <p class="mt-2.5 text-center text-[11px] text-white/25">Encrypted · Never sold · FMCSA verified</p>
                    </div>

                    {{-- Step 2 --}}
                    <div id="drv-s2" class="hidden" data-driver-step-panel="2">
                        <h2 class="sh-drive-step-title">Step 2 — Payout Setup</h2>
                        <p class="mb-4 text-xs text-white/35">Enter where you want your earnings deposited after each job</p>

                        <div class="mb-4">
                            <label class="sh-drive-label mb-2">Preferred Payout Method *</label>
                            <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-4">
                                @foreach ($site['payout_methods'] as $method)
                                    <button
                                        type="button"
                                        id="drv-pm-{{ $method['value'] }}"
                                        class="sh-drive-pm-btn"
                                        data-payout-method="{{ $method['value'] }}"
                                    >{{ $method['label'] }}</button>
                                @endforeach
                            </div>
                            <input type="hidden" id="drv-payout-method" name="payout_method" value="">
                        </div>

                        <div id="drv-pf-ach" class="hidden">
                            <div class="sh-drive-payout-panel mb-2.5">
                                <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.5px] text-white/40">Bank Account (ACH Direct Deposit)</div>
                                <div class="flex flex-col gap-2.5">
                                    <div>
                                        <label class="sh-drive-label-sm" for="drv-bk-holder">Account Holder Name *</label>
                                        <input id="drv-bk-holder" class="sh-drive-input sh-drive-input-sm" type="text" name="account_holder" placeholder="John A. Smith">
                                    </div>
                                    <div>
                                        <label class="sh-drive-label-sm" for="drv-bk-name">Bank Name *</label>
                                        <input id="drv-bk-name" class="sh-drive-input sh-drive-input-sm" type="text" name="bank_name" placeholder="Chase, Wells Fargo, etc.">
                                    </div>
                                    <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                                        <div>
                                            <label class="sh-drive-label-sm" for="drv-bk-routing">Routing Number *</label>
                                            <input id="drv-bk-routing" class="sh-drive-input sh-drive-input-sm" type="text" name="routing_number" placeholder="021000021" maxlength="9" inputmode="numeric">
                                        </div>
                                        <div>
                                            <label class="sh-drive-label-sm" for="drv-bk-account">Account Number *</label>
                                            <input id="drv-bk-account" class="sh-drive-input sh-drive-input-sm" type="text" name="account_number" placeholder="000123456789" inputmode="numeric">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="sh-drive-label-sm" for="drv-bk-acct-type">Account Type</label>
                                        <select id="drv-bk-acct-type" class="sh-drive-input sh-drive-input-sm" name="account_type">
                                            <option value="checking">Checking</option>
                                            <option value="savings">Savings</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="drv-pf-stripe" class="hidden">
                            <div class="sh-drive-payout-panel sh-drive-payout-panel-stripe mb-2.5">
                                <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.5px] text-indigo-300/70">Stripe Connect</div>
                                <div>
                                    <label class="sh-drive-label-sm" for="drv-stripe-email">Stripe Email *</label>
                                    <input id="drv-stripe-email" class="sh-drive-input sh-drive-input-sm sh-drive-input-stripe" type="email" name="stripe_email" placeholder="your@stripe.com">
                                </div>
                                <p class="mt-2.5 text-xs leading-relaxed text-white/35">After approval, you'll receive an email invite to complete your Stripe Connect onboarding. Payouts arrive within 2 business days of job completion.</p>
                            </div>
                        </div>

                        <div id="drv-pf-zelle" class="hidden">
                            <div class="sh-drive-payout-panel sh-drive-payout-panel-zelle mb-2.5">
                                <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.5px] text-violet-300/80">Zelle</div>
                                <div>
                                    <label class="sh-drive-label-sm" for="drv-zelle">Zelle Phone or Email *</label>
                                    <input id="drv-zelle" class="sh-drive-input sh-drive-input-sm sh-drive-input-zelle" type="text" name="zelle_contact" placeholder="(555) 000-0000 or zelle@email.com">
                                </div>
                                <p class="mt-2.5 text-xs text-white/35">Must match a Zelle-enrolled bank account. Instant delivery after job confirmation.</p>
                            </div>
                        </div>

                        <div id="drv-pf-venmo" class="hidden">
                            <div class="sh-drive-payout-panel sh-drive-payout-panel-venmo mb-2.5">
                                <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.5px] text-sky-300/80">Venmo</div>
                                <div>
                                    <label class="sh-drive-label-sm" for="drv-venmo">Venmo Handle *</label>
                                    <input id="drv-venmo" class="sh-drive-input sh-drive-input-sm sh-drive-input-venmo" type="text" name="venmo_handle" placeholder="@your-venmo">
                                </div>
                                <p class="mt-2.5 text-xs text-white/35">Make sure your Venmo account is verified and set to receive business payments.</p>
                            </div>
                        </div>

                        <div class="sh-drive-pay-note mb-3.5">
                            <strong class="text-green-400/90">Pay schedule:</strong>
                            Driver earnings are deposited <strong>every Friday</strong> for all jobs confirmed that week. Stripe Connect pays within 2 business days of each job.
                        </div>

                        <div id="drv-err-2" class="sh-drive-error hidden" role="alert"></div>

                        <div class="flex gap-2.5">
                            <button type="button" class="sh-drive-btn-back" data-driver-back>← Back</button>
                            <button type="button" class="sh-drive-btn-primary flex-1" data-driver-next="2">Review &amp; Submit →</button>
                        </div>
                        <p class="mt-2.5 text-center text-[11px] text-white/25">Bank details are encrypted and never shared</p>
                    </div>

                    {{-- Step 3 --}}
                    <div id="drv-s3" class="hidden" data-driver-step-panel="3">
                        <h2 class="sh-drive-step-title">Step 3 — Review &amp; Submit</h2>
                        <p class="mb-4 text-xs text-white/35">Confirm your details before sending your application</p>
                        <div id="drv-review-card" class="sh-drive-review-card mb-4"></div>
                        <div id="drv-err-3" class="sh-drive-error hidden" role="alert"></div>
                        <div class="flex gap-2.5">
                            <button type="button" class="sh-drive-btn-back" data-driver-back-review>← Back</button>
                            <button type="button" id="driver-submit-btn" class="sh-drive-btn-success flex-1">Submit Application</button>
                        </div>
                    </div>
                </div>

                <div id="driver-success" class="sh-drive-success hidden">
                    <div class="mb-3.5 text-[52px]" aria-hidden="true">✅</div>
                    <h2 class="sh-display mb-2.5 text-[32px] font-extrabold uppercase text-white">Application Sent!</h2>
                    <p class="text-sm leading-relaxed text-white/50">
                        CRS will review your application within 48 hours.<br>
                        Login credentials and payout confirmation will be emailed to you.
                    </p>
                    <div id="drv-success-summary" class="sh-drive-success-summary"></div>
                    <a href="{{ $site['crs_login_url'] }}" target="_blank" rel="noopener" class="mt-1 inline-block text-[13px] text-blue-400 underline">
                        competitiverelocationcrm.com/auth/login →
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
