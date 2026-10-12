@extends('layouts.portal')

@section('title', 'Seller Dashboard — SmooveHub')

@section('content')
    <div id="seller-dashboard" class="flex min-h-screen flex-col lg:flex-row">
        <aside class="w-full shrink-0 border-b border-sh-s200 bg-gradient-to-b from-sh-navy to-sh-blue text-white lg:w-64 lg:border-b-0 lg:border-r">
            <div class="border-b border-white/10 px-5 py-4">
                <a href="{{ route('site.home') }}" class="block">
                    <img src="{{ asset('images/landing/logo-nav.png') }}" alt="SmooveHub" class="h-10 brightness-0 invert">
                </a>
                <div id="dash-seller-name" class="mt-3 text-sm font-bold">My Store</div>
                <div class="text-xs text-white/60">Seller Account</div>
                <span class="mt-2 inline-flex sh-badge sh-badge-green">✓ Verified</span>
                <div class="mt-3 rounded-lg border border-sh-orange/30 bg-sh-orange/10 p-3">
                    <div class="text-[9px] font-bold uppercase tracking-widest text-white/40">Your Store URL</div>
                    <div id="dash-store-url" class="mt-1 cursor-pointer break-all font-mono text-[11px] font-semibold text-sh-orange">smoovehub.com/store/my-store</div>
                    <button type="button" class="mt-2 w-full rounded-md bg-sh-orange py-1.5 text-[11px] font-bold text-white" data-open-my-store>🏪 View My Store →</button>
                </div>
            </div>
            <nav class="flex gap-1 overflow-x-auto p-3 lg:flex-col lg:overflow-visible" aria-label="Dashboard navigation">
                @foreach ($portal['nav'] as $item)
                    <a
                        href="#"
                        data-dash-tab="{{ $item['key'] }}"
                        @class([
                            'flex shrink-0 items-center gap-2 rounded-lg px-4 py-2.5 text-[13px] font-medium transition-colors lg:w-full',
                            'bg-white/15 text-white' => ($item['key'] ?? '') === 'overview',
                            'text-white/70 hover:bg-white/10 hover:text-white' => ($item['key'] ?? '') !== 'overview',
                        ])
                    >
                        <span>{{ $item['label'] }}</span>
                        @if (isset($item['badge']))
                            <span @class([
                                'ml-auto rounded-full px-2 py-0.5 text-[10px] font-bold',
                                'bg-sh-orange text-white' => ($item['accent'] ?? false) === true || $item['badge'] === 'NEW',
                                'bg-white/20 text-white' => ! (($item['accent'] ?? false) === true || $item['badge'] === 'NEW'),
                            ])>{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>
            <div class="hidden border-t border-white/10 p-4 lg:block">
                <a href="{{ route('site.home') }}" class="text-[13px] font-medium text-white/60 hover:text-white">← Back to site</a>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-sh-s200 bg-white px-5 py-4 md:px-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-lg font-bold text-sh-s900 md:text-xl">Seller Dashboard</h1>
                        <p class="text-[13px] text-sh-s500">Welcome back, <strong id="dash-greet">Seller</strong></p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" class="sh-btn sh-btn-sm sh-btn-outline" data-modal-open="login-modal">Account</button>
                        <button type="button" class="sh-btn sh-btn-sm sh-btn-blue" data-modal-open="add-product-modal">+ Add Product</button>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-5 md:p-8">
                <div id="dash-overview" class="sh-dash-panel sh-dash-panel-active">
                    <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
                        @foreach ($portal['stats'] as $stat)
                            <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-5">
                                <div class="text-xs font-semibold uppercase tracking-widest text-sh-s400">{{ $stat['label'] }}</div>
                                <div class="sh-display mt-2 text-2xl font-black text-sh-s900 md:text-3xl">{{ $stat['value'] }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6">
                            <h2 class="mb-4 text-sm font-bold text-sh-s900">Recent Orders</h2>
                            <div id="dash-orders-list" class="space-y-3"></div>
                        </div>
                        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6">
                            <h2 class="mb-4 text-sm font-bold text-sh-s900">Quick Actions</h2>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <button type="button" class="rounded-xl border border-sh-s200 p-4 text-center transition-colors hover:border-sh-blue hover:bg-sh-blue-lt" data-open-my-store>
                                    <div class="mb-2 text-2xl" aria-hidden="true">🛒</div>
                                    <div class="text-sm font-semibold text-sh-s900">View Storefront</div>
                                </button>
                                <a href="{{ route('site.quote') }}" class="rounded-xl border border-sh-s200 p-4 text-center transition-colors hover:border-sh-blue hover:bg-sh-blue-lt">
                                    <div class="mb-2 text-2xl" aria-hidden="true">🚛</div>
                                    <div class="text-sm font-semibold text-sh-s900">Delivery Quotes</div>
                                </a>
                                <a href="{{ route('site.contact') }}" class="rounded-xl border border-sh-s200 p-4 text-center transition-colors hover:border-sh-blue hover:bg-sh-blue-lt">
                                    <div class="mb-2 text-2xl" aria-hidden="true">💬</div>
                                    <div class="text-sm font-semibold text-sh-s900">Support</div>
                                </a>
                                <button type="button" class="rounded-xl border border-sh-s200 p-4 text-center transition-colors hover:border-sh-orange hover:bg-sh-orange-lt" data-modal-open="add-product-modal">
                                    <div class="mb-2 text-2xl" aria-hidden="true">📸</div>
                                    <div class="text-sm font-semibold text-sh-s900">List Product</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="dash-products" class="sh-dash-panel hidden">
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-sh-s900">My Products</h2>
                            <p class="text-[13px] text-sh-s500">Manage your active listings</p>
                        </div>
                        <button type="button" class="sh-btn sh-btn-blue" data-modal-open="add-product-modal">+ Add Product</button>
                    </div>
                    <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-4 md:p-6">
                        <div id="dash-products-list"></div>
                    </div>
                </div>

                <div id="dash-orders" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">Orders</h2>
                    <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-4 md:p-6">
                        <div id="dash-orders-tab-list"></div>
                    </div>
                </div>

                <div id="dash-payouts" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">Payouts</h2>
                    <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6">
                        <p class="mb-4 text-[13px] text-sh-s500">Stripe Connect payouts — 100% of your listed price, deposited on delivery confirmation.</p>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="rounded-xl bg-sh-green-bg p-4"><div class="text-xs text-sh-s500">Available</div><div class="text-2xl font-black text-sh-green">$3,240</div></div>
                            <div class="rounded-xl bg-sh-s50 p-4"><div class="text-xs text-sh-s500">Pending</div><div class="text-2xl font-black text-sh-s900">$890</div></div>
                            <div class="rounded-xl bg-sh-s50 p-4"><div class="text-xs text-sh-s500">Total paid</div><div class="text-2xl font-black text-sh-s900">$12,480</div></div>
                        </div>
                        <button type="button" class="sh-btn sh-btn-orange mt-6">Request Payout →</button>
                    </div>
                </div>

                <div id="dash-settings" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">Store Settings</h2>
                    <div class="max-w-xl rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6 space-y-4">
                        <div class="sh-form-field mb-0">
                            <label class="sh-form-label" for="set-store-name">Store Name</label>
                            <input id="set-store-name" class="sh-form-input" type="text" placeholder="My Store">
                        </div>
                        <div class="sh-form-field mb-0">
                            <label class="sh-form-label" for="set-email">Email</label>
                            <input id="set-email" class="sh-form-input" type="email" placeholder="seller@email.com" disabled>
                        </div>
                        <button type="button" class="sh-btn sh-btn-blue" data-save-settings>Save Settings</button>
                    </div>
                </div>

                <div id="dash-distcatalog" class="sh-dash-panel hidden">
                    <h2 class="mb-2 text-lg font-bold text-sh-s900">🏭 Distribution Catalog</h2>
                    <p class="mb-6 text-[13px] text-sh-s500">Import wholesale inventory from verified distributors at reseller markup.</p>
                    <div class="overflow-x-auto rounded-2xl border-[1.5px] border-sh-s200 bg-white">
                        <table class="min-w-full text-left text-[13px]">
                            <thead class="border-b border-sh-s100 bg-sh-s50 text-xs uppercase tracking-widest text-sh-s500">
                                <tr>
                                    <th class="px-4 py-3">SKU</th>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3">Wholesale</th>
                                    <th class="px-4 py-3">Qty</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($portal['dist_catalog'] as $item)
                                    <tr class="border-b border-sh-s100 last:border-0">
                                        <td class="px-4 py-3 font-mono text-xs">{{ $item['sku'] }}</td>
                                        <td class="px-4 py-3 font-medium">{{ $item['name'] }}</td>
                                        <td class="px-4 py-3">${{ number_format($item['wholesale']) }}</td>
                                        <td class="px-4 py-3">{{ $item['qty'] }}</td>
                                        <td class="px-4 py-3"><button type="button" class="sh-btn sh-btn-sm sh-btn-outline">Import</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="dash-myimports" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">My Import Orders</h2>
                    <p class="rounded-2xl border border-sh-s200 bg-white p-8 text-center text-[13px] text-sh-s500">No import orders yet. Browse the Distribution Catalog to import wholesale inventory.</p>
                </div>

                <div id="dash-mypurchases" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">My Dist. Purchases</h2>
                    <p class="rounded-2xl border border-sh-s200 bg-white p-8 text-center text-[13px] text-sh-s500">Track your distribution catalog purchases here.</p>
                </div>

                <div id="dash-onsiteappts" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">Onsite Visit Appointments</h2>
                    <p class="rounded-2xl border border-sh-s200 bg-white p-8 text-center text-[13px] text-sh-s500">Customer onsite visit requests will appear here when buyers book showroom appointments.</p>
                </div>

                <div id="dash-openapi" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">Open API</h2>
                    <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6">
                        <p class="mb-4 text-[13px] text-sh-s500">Connect your ERP or inventory system via SmooveHub Open API.</p>
                        <div class="rounded-xl bg-sh-s50 p-4 font-mono text-xs text-sh-s700">API Key: sh_live_••••••••••••••••</div>
                        <button type="button" class="sh-btn sh-btn-outline mt-4">Regenerate Key</button>
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection
