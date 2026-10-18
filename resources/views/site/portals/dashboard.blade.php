@extends('layouts.portal')

@section('title', 'Seller Dashboard — SmooveHub')

@section('content')
    <div id="seller-dashboard" class="flex min-h-screen flex-col lg:flex-row">
        <aside class="w-full shrink-0 border-b border-sh-s200 bg-gradient-to-b from-sh-navy to-sh-blue text-white lg:w-64 lg:border-b-0 lg:border-r">
            <div class="border-b border-white/10 px-5 py-4">
                <a href="{{ route('site.home') }}" class="block">
                    <img src="{{ asset('images/landing/logo-nav.png') }}" alt="SmooveHub" class="h-10 brightness-0 invert">
                </a>
                <div class="mt-3 flex items-center gap-3">
                    <div id="dash-seller-avatar" class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-white/15 text-xs font-bold">S</div>
                    <div class="min-w-0">
                        <div id="dash-seller-name" class="truncate text-sm font-bold">My Store</div>
                        <div id="dash-seller-username" class="truncate text-xs text-white/60">Seller Account</div>
                    </div>
                </div>
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
                        href="#{{ $item['key'] }}"
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
                        <p class="text-[13px] text-sh-s500">Welcome back, <strong id="dash-greet">Seller</strong> <span id="dash-header-username" class="text-sh-s400"></span></p>
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
                        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-5">
                            <div class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Revenue (30d)</div>
                            <div id="dash-stat-revenue" class="sh-display mt-2 text-2xl font-black text-sh-s900 md:text-3xl">$0</div>
                        </div>
                        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-5">
                            <div class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Orders</div>
                            <div id="dash-stat-orders" class="sh-display mt-2 text-2xl font-black text-sh-s900 md:text-3xl">0</div>
                        </div>
                        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-5">
                            <div class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Products</div>
                            <div id="dash-stat-products" class="sh-display mt-2 text-2xl font-black text-sh-s900 md:text-3xl">0</div>
                        </div>
                        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-5">
                            <div class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Rating</div>
                            <div id="dash-stat-rating" class="sh-display mt-2 text-2xl font-black text-sh-s900 md:text-3xl">—</div>
                        </div>
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
                    <div class="space-y-6">
                        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6">
                            <p id="dash-payout-note" class="mb-4 text-[13px] text-sh-s500">Payouts release Fridays after CRS delivery confirmation. Stripe Connect ships in Phase 3.</p>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div class="rounded-xl bg-sh-green-bg p-4"><div class="text-xs text-sh-s500">Available</div><div id="dash-payout-available" class="text-2xl font-black text-sh-green">$0</div></div>
                                <div class="rounded-xl bg-sh-s50 p-4"><div class="text-xs text-sh-s500">Pending</div><div id="dash-payout-pending" class="text-2xl font-black text-sh-s900">$0</div></div>
                                <div class="rounded-xl bg-sh-s50 p-4"><div class="text-xs text-sh-s500">Total paid</div><div id="dash-payout-paid" class="text-2xl font-black text-sh-s900">$0</div></div>
                            </div>
                            <button type="button" class="sh-btn sh-btn-orange mt-6" disabled title="Stripe Connect ships in Phase 3">Request Payout →</button>
                        </div>
                        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-sm font-bold text-sh-s900">Payout bank accounts</h3>
                                    <p class="text-[12px] text-sh-s500">Last 4 digits only. Full account and routing numbers are never stored.</p>
                                </div>
                                <button type="button" class="sh-btn sh-btn-sm sh-btn-blue" data-open-bank-modal>Add bank</button>
                            </div>
                            <div id="dash-banks-list" class="space-y-3"></div>
                        </div>
                    </div>
                </div>

                <div id="dash-vouchers" class="sh-dash-panel hidden">
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-sh-s900">Store vouchers</h2>
                            <p class="text-[13px] text-sh-s500">Coupons members can redeem on your listings.</p>
                        </div>
                        <button type="button" class="sh-btn sh-btn-blue" data-open-voucher-modal>Create voucher</button>
                    </div>
                    <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-4 md:p-6">
                        <div id="dash-vouchers-list"></div>
                    </div>
                </div>

                <div id="dash-settings" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">Store Settings</h2>
                    <div class="max-w-2xl rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sh-form-field mb-0 sm:col-span-2">
                                <label class="sh-form-label" for="set-store-name">Store Name</label>
                                <input id="set-store-name" class="sh-form-input" type="text" placeholder="My Store">
                            </div>
                            <div class="sh-form-field mb-0 sm:col-span-2">
                                <label class="sh-form-label" for="set-description">Description</label>
                                <textarea id="set-description" class="sh-form-input resize-none" rows="3" placeholder="Tell shoppers about your store"></textarea>
                            </div>
                            <div class="sh-form-field mb-0">
                                <label class="sh-form-label" for="set-city">City</label>
                                <input id="set-city" class="sh-form-input" type="text" placeholder="Dallas">
                            </div>
                            <div class="sh-form-field mb-0">
                                <label class="sh-form-label" for="set-state">State</label>
                                <input id="set-state" class="sh-form-input" type="text" maxlength="40" placeholder="TX">
                            </div>
                            <div class="sh-form-field mb-0">
                                <label class="sh-form-label" for="set-business-type">Business type</label>
                                <input id="set-business-type" class="sh-form-input" type="text" placeholder="Reseller">
                            </div>
                            <div class="sh-form-field mb-0">
                                <label class="sh-form-label" for="set-email">Email</label>
                                <input id="set-email" class="sh-form-input" type="email" disabled>
                            </div>
                            <label class="flex items-center gap-2 text-[13px] text-sh-s700 sm:col-span-2">
                                <input id="set-onsite" type="checkbox" class="h-4 w-4 rounded border-sh-s300 text-sh-blue">
                                Accept onsite showroom visits
                            </label>
                            <div class="sh-form-field mb-0 sm:col-span-2">
                                <label class="sh-form-label" for="set-returns">Returns policy</label>
                                <textarea id="set-returns" class="sh-form-input resize-none" rows="3" placeholder="Returns accepted within 7 days…"></textarea>
                            </div>
                            <div class="sh-form-field mb-0 sm:col-span-2">
                                <label class="sh-form-label" for="set-shipping">Shipping policy</label>
                                <textarea id="set-shipping" class="sh-form-input resize-none" rows="3" placeholder="CRS delivery after purchase…"></textarea>
                            </div>
                        </div>
                        <button type="button" class="sh-btn sh-btn-blue mt-6" data-save-settings>Save Settings</button>
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
                            <tbody id="dash-dist-catalog-body">
                                <tr><td colspan="5" class="px-4 py-8 text-center text-[13px] text-sh-s400">Loading wholesale inventory…</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="dash-myimports" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">My Import Orders</h2>
                    <p id="dash-imports-list" class="rounded-2xl border border-sh-s200 bg-white p-8 text-center text-[13px] text-sh-s500">No import orders yet. Browse the Distribution Catalog to import wholesale inventory.</p>
                </div>

                <div id="dash-mypurchases" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">My Dist. Purchases</h2>
                    <p id="dash-purchases-list" class="rounded-2xl border border-sh-s200 bg-white p-8 text-center text-[13px] text-sh-s500">Track your distribution catalog purchases here.</p>
                </div>

                <div id="dash-onsiteappts" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">Onsite Visit Appointments</h2>
                    <p id="dash-onsite-list" class="rounded-2xl border border-sh-s200 bg-white p-8 text-center text-[13px] text-sh-s500">Customer onsite visit requests will appear here when buyers book showroom appointments.</p>
                </div>

                <div id="dash-openapi" class="sh-dash-panel hidden">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">Open API</h2>
                    <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6">
                        <p class="mb-4 text-[13px] text-sh-s500">Issue a store token for inventory sync. The plain key is shown once; we keep only a masked preview.</p>
                        <div id="dash-openapi-status" class="rounded-xl bg-sh-s50 p-4 font-mono text-xs text-sh-s700">No key issued yet.</div>
                        <div id="dash-openapi-plain" class="mt-3 hidden rounded-xl border border-sh-orange/40 bg-sh-orange-lt p-4 font-mono text-xs text-sh-s800"></div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button type="button" class="sh-btn sh-btn-blue" data-generate-openapi>Generate key</button>
                            <button type="button" class="sh-btn sh-btn-outline" data-revoke-openapi>Revoke</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <x-shared.modal id="seller-bank-modal" title="Add payout bank" maxWidth="max-w-[420px]">
        <p class="mb-4 text-[13px] text-sh-s500">Enter the last 4 digits only. Do not submit full account or routing numbers.</p>
        <div class="space-y-3">
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="bank-name">Bank name</label>
                <input id="bank-name" class="sh-form-input" type="text" placeholder="Chase">
            </div>
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="bank-holder">Account holder</label>
                <input id="bank-holder" class="sh-form-input" type="text">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="sh-form-field mb-0">
                    <label class="sh-form-label" for="bank-type">Type</label>
                    <select id="bank-type" class="sh-form-input">
                        <option value="checking">Checking</option>
                        <option value="savings">Savings</option>
                    </select>
                </div>
                <div class="sh-form-field mb-0">
                    <label class="sh-form-label" for="bank-last4">Last 4</label>
                    <input id="bank-last4" class="sh-form-input" type="text" inputmode="numeric" maxlength="4" placeholder="1234">
                </div>
            </div>
            <label class="flex items-center gap-2 text-[13px] text-sh-s700">
                <input id="bank-default" type="checkbox" class="h-4 w-4 rounded border-sh-s300 text-sh-blue" checked>
                Set as default payout account
            </label>
        </div>
        <div class="mt-5 flex gap-3">
            <button type="button" class="sh-btn sh-btn-outline" data-modal-close="seller-bank-modal">Cancel</button>
            <button type="button" class="sh-btn sh-btn-blue flex-1" data-save-bank>Save bank</button>
        </div>
    </x-shared.modal>

    <x-shared.modal id="seller-voucher-modal" title="Store voucher" maxWidth="max-w-[440px]">
        <input type="hidden" id="voucher-id" value="">
        <div class="space-y-3">
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="voucher-code">Code</label>
                <input id="voucher-code" class="sh-form-input uppercase" type="text" placeholder="STORE10">
            </div>
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="voucher-title">Title</label>
                <input id="voucher-title" class="sh-form-input" type="text" placeholder="10% off store items">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="sh-form-field mb-0">
                    <label class="sh-form-label" for="voucher-type">Type</label>
                    <select id="voucher-type" class="sh-form-input">
                        <option value="amount">Fixed amount</option>
                        <option value="percent">Percent</option>
                    </select>
                </div>
                <div class="sh-form-field mb-0">
                    <label class="sh-form-label" for="voucher-value">Value</label>
                    <input id="voucher-value" class="sh-form-input" type="number" min="0.01" step="0.01">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="sh-form-field mb-0">
                    <label class="sh-form-label" for="voucher-min">Min spend</label>
                    <input id="voucher-min" class="sh-form-input" type="number" min="0" step="0.01" value="0">
                </div>
                <div class="sh-form-field mb-0">
                    <label class="sh-form-label" for="voucher-expires">Expires</label>
                    <input id="voucher-expires" class="sh-form-input" type="date">
                </div>
            </div>
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="voucher-status">Status</label>
                <select id="voucher-status" class="sh-form-input">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="mt-5 flex gap-3">
            <button type="button" class="sh-btn sh-btn-outline" data-modal-close="seller-voucher-modal">Cancel</button>
            <button type="button" class="sh-btn sh-btn-blue flex-1" data-save-voucher>Save voucher</button>
        </div>
    </x-shared.modal>

    <x-shared.modal id="seller-confirm-modal" title="Please confirm" maxWidth="max-w-[400px]">
        <p id="seller-confirm-text" class="text-[14px] text-sh-s700">Are you sure?</p>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" class="sh-btn sh-btn-outline" data-modal-close="seller-confirm-modal">Cancel</button>
            <button type="button" class="sh-btn sh-btn-blue" data-seller-confirm>Confirm</button>
        </div>
    </x-shared.modal>
@endsection
