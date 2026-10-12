@extends('layouts.portal')

@section('title', 'Admin Portal — SmooveHub')

@section('content')
    @php
        $adminConfig = [
            'nav_sections' => $portal['nav_sections'],
            'credentials' => $portal['credentials'],
            'sellers' => $portal['sellers'],
            'drivers' => $portal['drivers'],
            'customers' => $portal['customers'],
            'distributors' => $portal['distributors'],
            'company_banks' => $portal['company_banks'],
            'order_ledger' => $portal['order_ledger'],
            'shop_products' => $portal['shop_products'],
            'file_records' => $portal['file_records'],
            'orders' => $portal['orders'],
            'payouts' => $portal['payouts'],
            'inventory' => $portal['inventory'],
            'delivery_integrations' => $portal['delivery_integrations'],
            'api_sections' => $portal['api_sections'],
            'crm_url' => $portal['crm_url'],
            'home_url' => $portal['home_url'],
            'distribute_url' => $portal['distribute_url'],
        ];
    @endphp
    <script type="application/json" id="admin-portal-config">@json($adminConfig)</script>

    {{-- Login overlay --}}
    <div id="admin-login-overlay" class="sh-adm-login-overlay" style="display:flex" aria-hidden="false">
        <div class="sh-adm-auth-card">
            <div class="mb-7 text-center">
                <div class="sh-adm-auth-icon">🛡</div>
                <h2 class="sh-adm-auth-title">Admin Portal</h2>
                <p class="mt-1 text-[13px] text-sh-s500">SmooveHub · Platform Administration</p>
            </div>
            <div class="mb-3.5">
                <label class="sh-adm-label">Admin Email</label>
                <input id="adm-email" class="sh-adm-input" type="email" placeholder="admin@smoovehub.com" autocomplete="email">
            </div>
            <div class="mb-4">
                <label class="sh-adm-label">Password</label>
                <input id="adm-pass" class="sh-adm-input" type="password" placeholder="••••••••" autocomplete="current-password">
            </div>
            <div id="adm-err" class="mb-3 hidden rounded-lg border border-red-200 bg-red-50 px-3.5 py-2.5 text-[13px] text-red-800"></div>
            <button type="button" class="sh-adm-btn sh-adm-btn-red sh-adm-btn-full mb-3" data-adm-action="login">Sign In to Admin →</button>
            <div id="adm-forgot-wrap" class="mb-3 hidden rounded-lg border border-green-200 bg-green-50 px-3.5 py-2.5 text-[12px] text-green-800"></div>
            <p class="mb-2 text-center">
                <button type="button" class="text-[13px] font-semibold text-red-600 underline" data-adm-action="forgot-password">Forgot password? Reset via email →</button>
            </p>
            <a href="{{ route('site.home') }}" class="sh-adm-btn sh-adm-btn-outline sh-adm-btn-full mb-3">← Back to Marketplace</a>
            <div class="rounded-lg bg-sh-s50 px-3.5 py-3 text-center text-[12px] text-sh-s500">
                Demo:
                <button type="button" class="font-semibold text-red-600" data-adm-action="fill-demo">admin@smoovehub.com / Admin2025!</button>
            </div>
        </div>
    </div>

    {{-- Main portal --}}
    <div id="admin-main" class="sh-adm-main hidden" style="display:none" aria-hidden="true">
        <aside class="sh-adm-sidebar">
            <div class="sh-adm-sidebar-head">
                <div class="flex items-center gap-2.5">
                    <div class="sh-adm-sidebar-badge">🛡</div>
                    <div>
                        <div class="text-[17px] font-black text-white leading-none">Admin Portal</div>
                        <div class="mt-0.5 text-[10px] tracking-wide text-white/35">SmooveHub Platform</div>
                    </div>
                </div>
            </div>

            <nav class="sh-adm-nav" aria-label="Admin navigation">
                @foreach ($portal['nav_sections'] as $section)
                    <div class="sh-adm-nav-section-label">{{ $section['label'] }}</div>
                    @foreach ($section['items'] as $item)
                        <button
                            type="button"
                            class="sh-adm-nav-item @if (($item['key'] ?? '') === 'adm-overview') sh-adm-nav-item-active @endif"
                            data-adm-tab="{{ $item['key'] }}"
                        >
                            <span>{{ $item['label'] }}</span>
                            @if (! empty($item['badge']))
                                <span @class([
                                    'sh-adm-nav-badge',
                                    'sh-adm-nav-badge-orange' => ($item['badge_color'] ?? '') === 'orange',
                                    'sh-adm-nav-badge-purple' => ($item['badge_color'] ?? '') === 'purple',
                                    'sh-adm-nav-badge-green' => ($item['badge_color'] ?? '') === 'green' || ! isset($item['badge_color']),
                                ])>{{ $item['badge'] }}</span>
                            @endif
                        </button>
                    @endforeach
                @endforeach
            </nav>

            <div class="sh-adm-sidebar-foot">
                <div class="mb-2.5 flex items-center gap-2">
                    <div class="sh-adm-avatar">A</div>
                    <div class="min-w-0">
                        <div id="adm-user-name" class="truncate text-[12px] font-semibold text-white">Admin</div>
                        <div class="text-[10px] text-white/35">Platform Administrator</div>
                    </div>
                </div>
                <div class="flex gap-1.5">
                    <a href="{{ route('site.home') }}" class="sh-adm-foot-btn flex-1">🏠 Site</a>
                    <button type="button" class="sh-adm-foot-btn sh-adm-foot-btn-danger flex-1" data-adm-action="logout">Sign Out</button>
                </div>
            </div>
        </aside>

        <div class="sh-adm-content-wrap">
            <header class="sh-adm-topbar">
                <div id="adm-page-title" class="sh-adm-topbar-title">Platform Overview</div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="sh-adm-status-badge">
                        <span class="sh-adm-status-dot"></span>
                        All systems operational
                    </div>
                    <div id="adm-last-refresh" class="text-[12px] text-sh-s500">Live data</div>
                </div>
            </header>

            <main class="sh-adm-main-panels">
                <div id="adm-overview" class="sh-adm-panel"></div>
                <div id="adm-sellers" class="sh-adm-panel hidden"></div>
                <div id="adm-shopprods" class="sh-adm-panel hidden"></div>
                <div id="adm-drivers" class="sh-adm-panel hidden"></div>
                <div id="adm-customers" class="sh-adm-panel hidden"></div>
                <div id="adm-dist" class="sh-adm-panel hidden"></div>
                <div id="adm-orders" class="sh-adm-panel hidden"></div>
                <div id="adm-distorders" class="sh-adm-panel hidden"></div>
                <div id="adm-payouts" class="sh-adm-panel hidden"></div>
                <div id="adm-delivery" class="sh-adm-panel hidden"></div>
                <div id="adm-fulfillment" class="sh-adm-panel hidden"></div>
                <div id="adm-analytics" class="sh-adm-panel hidden"></div>
                <div id="adm-apihub" class="sh-adm-panel hidden"></div>
                <div id="adm-filemanager" class="sh-adm-panel hidden"></div>
                <div id="adm-settings" class="sh-adm-panel hidden"></div>
            </main>
        </div>
    </div>
@endsection
