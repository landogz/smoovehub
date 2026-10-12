@extends('layouts.portal')

@section('title', 'Distribution Company Portal — SmooveHub')

@section('content')
    @php
        $distConfig = [
            'nav_sections' => $portal['nav_sections'],
            'company' => $portal['company'],
            'inventory' => $portal['inventory'],
            'orders' => $portal['orders'],
            'payouts' => $portal['payouts'],
            'delivery_integrations' => $portal['delivery_integrations'],
            'demo_accounts' => $portal['demo_accounts'],
            'crs_pricing' => $portal['crs_pricing'],
            'categories' => $portal['categories'],
            'api_base_url' => $portal['api_base_url'],
            'crm_url' => $portal['crm_url'],
            'home_url' => $portal['home_url'],
        ];
    @endphp
    <script type="application/json" id="distribute-portal-config">@json($distConfig)</script>

    {{-- Payout modal --}}
    <div id="dist-payout-modal" class="sh-dist-modal hidden" style="display:none" aria-hidden="true">
        <div class="sh-dist-modal-card">
            <button type="button" class="sh-dist-modal-close" data-dist-action="close-payout-modal" aria-label="Close">✕</button>
            <h2 class="sh-dist-modal-title">🏦 Payout Method Setup</h2>
            <p class="sh-dist-modal-sub">PCI-DSS compliant · AES-256 encrypted · Stripe tokenization</p>
            <div class="sh-dist-payout-tabs">
                <button type="button" id="dpt-bank" class="sh-dist-payout-tab sh-dist-payout-tab-active" data-dist-action="payout-tab" data-tab="bank">🏦 Bank Account</button>
                <button type="button" id="dpt-card" class="sh-dist-payout-tab" data-dist-action="payout-tab" data-tab="card">💳 Credit Card</button>
            </div>
            <div id="dptp-bank">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="sh-dist-label">Account Holder Name *</label>
                        <input id="dp-bank-holder" class="sh-dist-input" placeholder="Jane Smith LLC">
                    </div>
                    <div>
                        <label class="sh-dist-label">Bank Name *</label>
                        <input id="dp-bank-name" class="sh-dist-input" placeholder="Chase Bank">
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="sh-dist-label">Routing Number *</label>
                        <input id="dp-bank-route" class="sh-dist-input" type="password" placeholder="9-digit routing" maxlength="9">
                        <p class="mt-1 text-[11px] text-sh-s400">Must be 9 digits</p>
                    </div>
                    <div>
                        <label class="sh-dist-label">Account Number *</label>
                        <input id="dp-bank-acct" class="sh-dist-input" type="password" placeholder="Account number">
                    </div>
                </div>
                <button type="button" class="sh-dist-btn sh-dist-btn-blue sh-dist-btn-full mt-4" data-dist-action="save-bank-payout">🔒 Save Bank Account</button>
            </div>
            <div id="dptp-card" class="hidden">
                <div class="mb-3 rounded-lg border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-[12px] text-amber-900">
                    Supported: Visa · Mastercard · American Express · Discover · Stripe · PayPal
                </div>
                <div class="mb-3">
                    <label class="sh-dist-label">Cardholder Name *</label>
                    <input id="dp-card-name" class="sh-dist-input" placeholder="Jane Smith">
                </div>
                <div class="mb-3">
                    <label class="sh-dist-label">Card Number *</label>
                    <input id="dp-card-num" class="sh-dist-input" type="password" placeholder="•••• •••• •••• ••••" maxlength="19">
                </div>
                <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="sh-dist-label">Expiration (MM/YY) *</label>
                        <input id="dp-card-exp" class="sh-dist-input" placeholder="12/27" maxlength="5">
                    </div>
                    <div>
                        <label class="sh-dist-label">CVV *</label>
                        <input id="dp-card-cvv" class="sh-dist-input" type="password" placeholder="•••" maxlength="4">
                    </div>
                </div>
                <button type="button" class="sh-dist-btn sh-dist-btn-orange sh-dist-btn-full" data-dist-action="save-card-payout">🔒 Save Card (Tokenized)</button>
            </div>
            <p class="mt-3 text-center text-[11px] text-sh-s400">🛡 AES-256 encrypted · PCI-DSS Level 1 · Never stored in plaintext</p>
        </div>
    </div>

    {{-- Login overlay --}}
    <div id="dist-login-overlay" class="sh-dist-login-overlay" style="display:flex" aria-hidden="false">
        <div id="dist-login-panel" class="sh-dist-auth-card">
            <div class="mb-6 text-center">
                <div class="sh-dist-auth-icon">🏭</div>
                <h2 class="sh-dist-auth-title">Distribution Portal</h2>
                <p class="mt-1 text-[13px] text-sh-s500">SmooveHub Enterprise · Secure Login</p>
            </div>
            <div class="mb-3.5">
                <label class="sh-dist-label">Email *</label>
                <input id="dist-email" class="sh-dist-input" type="email" placeholder="dist@company.com" autocomplete="email">
            </div>
            <div class="mb-4">
                <label class="sh-dist-label">Password *</label>
                <input id="dist-pass" class="sh-dist-input" type="password" placeholder="••••••••" autocomplete="current-password">
            </div>
            <div id="dist-login-err" class="mb-3 hidden rounded-lg border border-red-200 bg-red-50 px-3.5 py-2.5 text-[13px] text-red-800"></div>
            <button type="button" class="sh-dist-btn sh-dist-btn-blue sh-dist-btn-full mb-3" data-dist-action="login">Sign In →</button>
            <p class="text-center text-[13px] text-sh-s500">
                Don't have an account?
                <button type="button" class="font-bold text-sh-orange" data-dist-action="show-register">Register →</button>
            </p>
            <div class="mt-4 rounded-lg bg-sh-s50 px-3.5 py-3 text-[12px] text-sh-s500">
                <strong>Demo:</strong><br>
                <button type="button" class="font-semibold text-sh-blue" data-dist-action="fill-demo">dist@acme.com / Dist2025!</button>
            </div>
        </div>

        <div id="dist-register-panel" class="sh-dist-auth-card hidden">
            <div class="mb-5 text-center">
                <div class="sh-dist-auth-icon">🏭</div>
                <h2 class="sh-dist-auth-title">Create Account</h2>
                <p class="mt-1 text-[13px] text-sh-s500">Distribution partner registration</p>
            </div>
            <div class="mb-3">
                <label class="sh-dist-label">Company Name *</label>
                <input id="dist-reg-biz" class="sh-dist-input" placeholder="ACME Furniture Co.">
            </div>
            <div class="mb-3">
                <label class="sh-dist-label">Business Email *</label>
                <input id="dist-reg-em" class="sh-dist-input" type="email" placeholder="admin@company.com">
            </div>
            <div class="mb-4">
                <label class="sh-dist-label">Password *</label>
                <input id="dist-reg-pw" class="sh-dist-input" type="password" placeholder="Min 8 characters">
            </div>
            <div id="dist-reg-err" class="mb-3 hidden rounded-lg border border-red-200 bg-red-50 px-3.5 py-2.5 text-[13px] text-red-800"></div>
            <button type="button" class="sh-dist-btn sh-dist-btn-orange sh-dist-btn-full mb-3" data-dist-action="register">Create Account →</button>
            <p class="text-center text-[13px] text-sh-s500">
                Already have an account?
                <button type="button" class="font-bold text-sh-blue" data-dist-action="show-login">Sign In →</button>
            </p>
        </div>
    </div>

    {{-- Main portal --}}
    <div id="dist-main-content" class="sh-dist-main hidden" style="display:none" aria-hidden="true">
        <aside class="sh-dist-sidebar">
            <div class="sh-dist-sidebar-head">
                <a href="{{ route('site.home') }}" class="block">
                    <img src="{{ asset('images/landing/logo-nav.png') }}" alt="SmooveHub" class="h-[52px] w-auto object-contain brightness-0 invert">
                </a>
                <div class="mt-2 flex items-center gap-2">
                    <div class="sh-dist-sidebar-badge">🏭</div>
                    <div>
                        <div id="dist-sidebar-name" class="text-[13px] font-black text-white">Distribution</div>
                        <div class="text-[9px] tracking-wide text-white/35">Enterprise Portal</div>
                    </div>
                </div>
            </div>

            <nav class="sh-dist-nav" aria-label="Distribution navigation">
                @foreach ($portal['nav_sections'] as $section)
                    <div class="sh-dist-nav-section-label">{{ $section['label'] }}</div>
                    @foreach ($section['items'] as $item)
                        <button
                            type="button"
                            class="sh-dist-nav-item @if (($item['key'] ?? '') === 'overview') sh-dist-nav-item-active @endif"
                            data-dist-tab="{{ $item['key'] }}"
                        >
                            <span>{{ $item['label'] }}</span>
                            @if (! empty($item['badge']))
                                <span class="sh-dist-nav-badge">{{ $item['badge'] }}</span>
                            @endif
                        </button>
                    @endforeach
                @endforeach
            </nav>

            <div class="sh-dist-sidebar-foot">
                <div class="mb-2.5 flex items-center gap-2.5">
                    <div id="dist-avatar-letter" class="sh-dist-avatar">?</div>
                    <div class="min-w-0">
                        <div id="dist-user-display" class="truncate text-[12px] font-semibold text-white">—</div>
                        <div id="dist-user-role" class="text-[10px] text-white/35">—</div>
                    </div>
                </div>
                <button type="button" class="sh-dist-signout" data-dist-action="logout">Sign Out</button>
            </div>
        </aside>

        <div class="sh-dist-content-wrap">
            <header class="sh-dist-topbar">
                <div class="sh-dist-topbar-title">Distribution Company Portal</div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="sh-dist-crm-badge">
                        <span class="sh-dist-crm-dot"></span>
                        CRM Live
                    </div>
                    <a href="{{ route('site.home') }}" class="sh-dist-back-btn">← Back to Marketplace</a>
                </div>
            </header>

            <main class="sh-dist-main-panels">
                <div id="dp-overview" class="sh-dist-panel"></div>
                <div id="dp-inventory" class="sh-dist-panel hidden"></div>
                <div id="dp-upload" class="sh-dist-panel hidden"></div>
                <div id="dp-orders" class="sh-dist-panel hidden"></div>
                <div id="dp-payout" class="sh-dist-panel hidden"></div>
                <div id="dp-delivery" class="sh-dist-panel hidden"></div>
                <div id="dp-dquote" class="sh-dist-panel hidden"></div>
                <div id="dp-api" class="sh-dist-panel hidden"></div>
                <div id="dp-analytics" class="sh-dist-panel hidden"></div>
                <div id="dp-settings" class="sh-dist-panel hidden"></div>
            </main>
        </div>
    </div>
@endsection
