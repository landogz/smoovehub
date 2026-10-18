@extends('layouts.portal')

@section('title', 'Seller Dashboard — SmooveHub')

@section('content')
    <div id="seller-dashboard" class="flex min-h-screen flex-col lg:flex-row">
        <aside class="flex w-full shrink-0 flex-col bg-sh-navy text-white lg:w-[268px] lg:min-h-screen">
            <div class="border-b border-white/10 px-5 py-5">
                <a href="{{ route('site.home') }}" class="block">
                    <img src="{{ asset('images/landing/logo-nav.png') }}" alt="SmooveHub" class="h-9 brightness-0 invert">
                </a>
                <div class="mt-5 flex items-center gap-3">
                    <div id="dash-seller-avatar" class="sh-store-logo h-11 w-11 rounded-xl bg-white/10 text-xs font-bold text-white ring-2 ring-white/10">S</div>
                    <div class="min-w-0">
                        <div id="dash-seller-name" class="truncate text-sm font-semibold">My Store</div>
                        <div id="dash-seller-username" class="truncate text-[12px] text-white/55">Seller</div>
                    </div>
                </div>
                <div class="mt-4 rounded-xl bg-white/5 p-3">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.14em] text-white/40">Storefront</div>
                    <div id="dash-store-url" class="mt-1 cursor-pointer break-all font-mono text-[11px] text-white/80">smoovehub.com/store/my-store</div>
                    <button type="button" class="mt-3 w-full rounded-lg bg-white py-2 text-[12px] font-semibold text-sh-navy" data-open-my-store>View store</button>
                </div>
            </div>
            <nav class="flex flex-1 gap-1 overflow-x-auto p-3 lg:flex-col lg:overflow-visible" aria-label="Dashboard navigation">
                @foreach ($portal['nav_groups'] ?? [['items' => $portal['nav']]] as $group)
                    @if (! empty($group['label']))
                        <p class="hidden px-3 pb-1 pt-3 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/35 lg:block">{{ $group['label'] }}</p>
                    @endif
                    @foreach ($group['items'] as $item)
                        <a
                            href="#{{ $item['key'] }}"
                            data-dash-tab="{{ $item['key'] }}"
                            @class(['sh-dash-nav-link', 'is-active' => ($item['key'] ?? '') === 'overview'])
                        >
                            <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                @switch($item['key'])
                                    @case('overview')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h7v7H4zM13 5h7v4h-7zM13 11h7v8h-7zM4 14h7v5H4z" />
                                        @break
                                    @case('products')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                                        @break
                                    @case('orders')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5h12M9 12h12M9 19h12M4 5h.01M4 12h.01M4 19h.01" />
                                        @break
                                    @case('payouts')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v18M17 8c0-1.7-2.2-3-5-3s-5 1.3-5 3 2.2 3 5 3 5 1.3 5 3-2.2 3-5 3-5-1.3-5-3" />
                                        @break
                                    @case('vouchers')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 9a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 1 0 0 4v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2a2 2 0 1 0 0-4z" />
                                        @break
                                    @case('settings')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z" />
                                        @break
                                    @case('distcatalog')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9h18M5 9V7l7-4 7 4v2M5 9v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9" />
                                        @break
                                    @case('myimports')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14" />
                                        @break
                                    @case('mypurchases')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 7h12l2 12H4L6 7zM9 7V5a3 3 0 0 1 6 0v2" />
                                        @break
                                    @case('onsiteappts')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3M4 11h16M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z" />
                                        @break
                                    @default
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 9h8M8 13h5M6 4h12a2 2 0 0 1 2 2v14l-4-2-4 2-4-2-4 2V6a2 2 0 0 1 2-2z" />
                                @endswitch
                            </svg>
                            <span class="truncate">{{ $item['label'] }}</span>
                            @if (isset($item['badge']))
                                <span class="ml-auto rounded-full bg-white/15 px-2 py-0.5 text-[10px] font-semibold text-white/80">{{ $item['badge'] }}</span>
                            @endif
                        </a>
                    @endforeach
                @endforeach
            </nav>
            <div class="hidden border-t border-white/10 p-4 lg:block">
                <a href="{{ route('site.home') }}" class="text-[13px] font-medium text-white/50 hover:text-white">Back to marketplace</a>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-sh-s200 bg-white px-5 py-3.5 md:px-8">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <h1 class="text-lg font-bold text-sh-s900 md:text-xl">Seller Dashboard</h1>
                        <p class="truncate text-[13px] text-sh-s500">Welcome back, <strong id="dash-greet">Seller</strong></p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                        <div class="relative">
                            <button
                                id="dash-user-toggle"
                                type="button"
                                class="flex min-h-11 items-center gap-2 rounded-[10px] border border-sh-s200 bg-white px-2 py-1.5 transition-colors hover:border-sh-blue/40 hover:bg-sh-blue-lt"
                                aria-expanded="false"
                                aria-controls="dash-user-menu"
                            >
                                <span id="dash-header-avatar" class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-sh-blue text-xs font-bold text-white">S</span>
                                <span class="hidden min-w-0 text-left sm:block">
                                    <span id="dash-header-name" class="block max-w-[140px] truncate text-[13px] font-semibold text-sh-s900">Seller</span>
                                    <span id="dash-header-username" class="block max-w-[140px] truncate text-[11px] text-sh-s500">Seller</span>
                                </span>
                                <svg class="hidden h-4 w-4 text-sh-s400 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="dash-user-menu" class="absolute right-0 top-[calc(100%+8px)] z-[350] hidden min-w-[220px] overflow-hidden rounded-[14px] border border-sh-s200 bg-white shadow-[0_16px_48px_rgba(15,23,42,0.14)]">
                                <button type="button" class="block w-full px-4 py-3 text-left text-[13px] font-medium text-sh-s700 hover:bg-sh-s50" data-dash-tab="settings">Store settings</button>
                                <button type="button" class="block w-full px-4 py-3 text-left text-[13px] font-medium text-sh-s700 hover:bg-sh-s50" data-open-my-store>View my store</button>
                                <button type="button" class="block w-full px-4 py-3 text-left text-[13px] font-medium text-sh-s700 hover:bg-sh-s50" data-auth-logout-other-devices>Sign out other devices</button>
                                <button type="button" class="block w-full border-t border-sh-s100 px-4 py-3 text-left text-[13px] font-medium text-red-600 hover:bg-red-50" data-dash-logout>Sign out</button>
                            </div>
                        </div>
                        <button type="button" class="sh-btn sh-btn-sm sh-btn-blue" data-modal-open="add-product-modal">+ Add Product</button>
                    </div>
                </div>
            </header>

            <main class="flex-1 bg-sh-s50 p-5 md:p-8">
                <div id="dash-overview" class="sh-dash-panel sh-dash-panel-active">
                    <div class="mb-6">
                        <h2 class="sh-dash-page-title">Overview</h2>
                        <p class="sh-dash-page-copy">Sales, catalog, and fulfillment at a glance.</p>
                    </div>
                    <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                        <div class="sh-dash-kpi">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-sh-s400">Revenue · 30d</div>
                            <div id="dash-stat-revenue" class="mt-2 text-2xl font-bold tracking-tight text-sh-s900 md:text-[28px]">$0</div>
                        </div>
                        <div class="sh-dash-kpi">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-sh-s400">Orders</div>
                            <div id="dash-stat-orders" class="mt-2 text-2xl font-bold tracking-tight text-sh-s900 md:text-[28px]">0</div>
                        </div>
                        <div class="sh-dash-kpi">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-sh-s400">Products</div>
                            <div id="dash-stat-products" class="mt-2 text-2xl font-bold tracking-tight text-sh-s900 md:text-[28px]">0</div>
                        </div>
                        <div class="sh-dash-kpi">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-sh-s400">Rating</div>
                            <div id="dash-stat-rating" class="mt-2 text-2xl font-bold tracking-tight text-sh-s900 md:text-[28px]">—</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-5">
                        <div class="sh-dash-card p-5 lg:col-span-3">
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-sh-s900">Recent orders</h3>
                                <button type="button" class="text-[12px] font-semibold text-sh-blue" data-dash-tab="orders">View all</button>
                            </div>
                            <div id="dash-orders-list" class="space-y-2"></div>
                        </div>
                        <div class="sh-dash-card p-5 lg:col-span-2">
                            <h3 class="mb-4 text-sm font-semibold text-sh-s900">Shortcuts</h3>
                            <div class="grid grid-cols-1 gap-2">
                                <button type="button" class="sh-dash-row text-left hover:border-sh-blue/40" data-open-my-store>
                                    <span class="text-sm font-semibold text-sh-s900">View storefront</span>
                                    <span class="text-[12px] text-sh-s400">Public shop</span>
                                </button>
                                <button type="button" class="sh-dash-row text-left hover:border-sh-blue/40" data-modal-open="add-product-modal">
                                    <span class="text-sm font-semibold text-sh-s900">List a product</span>
                                    <span class="text-[12px] text-sh-s400">New listing</span>
                                </button>
                                <a href="{{ route('site.quote') }}" class="sh-dash-row hover:border-sh-blue/40">
                                    <span class="text-sm font-semibold text-sh-s900">Delivery quote</span>
                                    <span class="text-[12px] text-sh-s400">CRS rates</span>
                                </a>
                                <a href="{{ route('site.contact') }}" class="sh-dash-row hover:border-sh-blue/40">
                                    <span class="text-sm font-semibold text-sh-s900">Support</span>
                                    <span class="text-[12px] text-sh-s400">Contact</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="dash-products" class="sh-dash-panel hidden">
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="sh-dash-page-title">Products</h2>
                            <p class="sh-dash-page-copy">Active listings on your storefront.</p>
                        </div>
                        <button type="button" class="sh-btn sh-btn-blue" data-modal-open="add-product-modal">Add product</button>
                    </div>
                    <div class="sh-dash-card p-3 md:p-4">
                        <div id="dash-products-list"></div>
                    </div>
                </div>

                <div id="dash-orders" class="sh-dash-panel hidden">
                    <div class="mb-6">
                        <h2 class="sh-dash-page-title">Orders</h2>
                        <p class="sh-dash-page-copy">Update fulfillment as items move from processing to delivered.</p>
                    </div>
                    <div class="sh-dash-card p-3 md:p-4">
                        <div id="dash-orders-tab-list"></div>
                    </div>
                </div>

                <div id="dash-payouts" class="sh-dash-panel hidden">
                    <div class="mb-6">
                        <h2 class="sh-dash-page-title">Payouts</h2>
                        <p id="dash-payout-note" class="sh-dash-page-copy">Payouts release Fridays after CRS delivery confirmation. Stripe Connect ships in Phase 3.</p>
                    </div>
                    <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="sh-dash-kpi">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-sh-s400">Available</div>
                            <div id="dash-payout-available" class="mt-2 text-2xl font-bold text-sh-green">$0</div>
                        </div>
                        <div class="sh-dash-kpi">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-sh-s400">Pending</div>
                            <div id="dash-payout-pending" class="mt-2 text-2xl font-bold text-sh-s900">$0</div>
                        </div>
                        <div class="sh-dash-kpi">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-sh-s400">Paid</div>
                            <div id="dash-payout-paid" class="mt-2 text-2xl font-bold text-sh-s900">$0</div>
                        </div>
                    </div>
                    <button type="button" class="sh-btn sh-btn-outline mb-6" disabled title="Stripe Connect ships in Phase 3">Request payout</button>
                    <div class="sh-dash-card p-5">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-semibold text-sh-s900">Bank accounts</h3>
                                <p class="text-[12px] text-sh-s500">Last 4 digits only. Full account numbers are never stored.</p>
                            </div>
                            <button type="button" class="sh-btn sh-btn-sm sh-btn-blue" data-open-bank-modal>Add bank</button>
                        </div>
                        <div id="dash-banks-list" class="space-y-2"></div>
                    </div>
                </div>

                <div id="dash-vouchers" class="sh-dash-panel hidden">
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="sh-dash-page-title">Vouchers</h2>
                            <p class="sh-dash-page-copy">Store coupons members can redeem in My Account.</p>
                        </div>
                        <button type="button" class="sh-btn sh-btn-blue" data-open-voucher-modal>Create voucher</button>
                    </div>
                    <div class="sh-dash-card p-3 md:p-4">
                        <div id="dash-vouchers-list"></div>
                    </div>
                </div>

                <div id="dash-settings" class="sh-dash-panel hidden">
                    <div class="mb-6">
                        <h2 class="sh-dash-page-title">Settings</h2>
                        <p class="sh-dash-page-copy">Brand, contact, pickup location, and customer-facing policies.</p>
                    </div>
                    <div class="grid max-w-4xl gap-5">
                        <div class="sh-dash-card p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-sh-s900">Public store</h3>
                                    <p id="set-public-url" class="mt-1 break-all font-mono text-[12px] text-sh-s500">smoovehub.com/store/my-store</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <span id="set-verified" class="sh-badge sh-badge-amber">Pending</span>
                                        <span id="set-rating" class="text-[12px] text-sh-s500">Rating —</span>
                                        <span id="set-member-since" class="text-[12px] text-sh-s500"></span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" class="sh-dash-ghost" data-copy-store-url>Copy link</button>
                                    <button type="button" class="sh-dash-ghost" data-open-my-store>View store</button>
                                </div>
                            </div>
                        </div>

                        <div class="sh-dash-card overflow-hidden">
                            <div class="border-b border-sh-s100 bg-sh-s50 px-5 py-4">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-sh-s400">Shopper preview</p>
                                <div class="mt-3 flex items-center gap-3">
                                    <div id="set-preview-logo" class="sh-store-logo">S</div>
                                    <div class="min-w-0">
                                        <div id="set-preview-name" class="truncate text-sm font-semibold text-sh-s900">My Store</div>
                                        <div id="set-preview-meta" class="truncate text-[12px] text-sh-s500">Location not set</div>
                                        <span id="set-preview-onsite" class="mt-1 hidden text-[11px] font-semibold text-sh-blue">Onsite visits welcome</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="mb-4 text-sm font-semibold text-sh-s900">Brand</h3>
                                <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center">
                                    <div id="set-logo-preview" class="sh-store-logo h-20 w-20 text-xl">S</div>
                                    <div>
                                        <label class="sh-dash-ghost cursor-pointer">
                                            Upload logo
                                            <input id="set-logo" class="sr-only" type="file" accept="image/jpeg,image/png">
                                        </label>
                                        <p class="mt-2 text-[12px] text-sh-s500">Square JPEG or PNG, up to 1 MB. Shown to shoppers, not your personal photo.</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="sh-form-field mb-0">
                                        <label class="sh-form-label" for="set-store-name">Store name</label>
                                        <input id="set-store-name" class="sh-form-input" type="text" placeholder="My Store">
                                    </div>
                                    <div class="sh-form-field mb-0">
                                        <label class="sh-form-label" for="set-description">Description</label>
                                        <textarea id="set-description" class="sh-form-input resize-none" rows="3" placeholder="Tell shoppers about your store"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sh-dash-card p-5">
                            <h3 class="mb-4 text-sm font-semibold text-sh-s900">Contact and location</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sh-form-field mb-0">
                                    <label class="sh-form-label" for="set-phone">Phone</label>
                                    <input id="set-phone" class="sh-form-input" type="tel" placeholder="(555) 000-0000">
                                </div>
                                <div class="sh-form-field mb-0">
                                    <label class="sh-form-label" for="set-email">Email</label>
                                    <input id="set-email" class="sh-form-input" type="email" disabled>
                                </div>
                                <div class="sh-form-field mb-0 sm:col-span-2">
                                    <label class="sh-form-label" for="set-street">Street</label>
                                    <input id="set-street" class="sh-form-input" type="text" placeholder="123 Warehouse Ave">
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
                                    <label class="sh-form-label" for="set-zip">Pickup ZIP</label>
                                    <input id="set-zip" class="sh-form-input" type="text" maxlength="10" placeholder="75201">
                                </div>
                                <div class="sh-form-field mb-0">
                                    <label class="sh-form-label" for="set-business-type">Business type</label>
                                    <input id="set-business-type" class="sh-form-input" type="text" placeholder="Reseller">
                                </div>
                                <label class="flex items-center gap-2 text-[13px] text-sh-s700 sm:col-span-2">
                                    <input id="set-onsite" type="checkbox" class="h-4 w-4 rounded border-sh-s300 text-sh-blue">
                                    Accept onsite showroom visits
                                </label>
                            </div>
                        </div>

                        <div class="sh-dash-card p-5">
                            <h3 class="mb-4 text-sm font-semibold text-sh-s900">Policies</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sh-form-field mb-0">
                                    <label class="sh-form-label" for="set-returns">Returns</label>
                                    <textarea id="set-returns" class="sh-form-input resize-none" rows="4" placeholder="Returns accepted within 7 days…"></textarea>
                                </div>
                                <div class="sh-form-field mb-0">
                                    <label class="sh-form-label" for="set-shipping">Shipping</label>
                                    <textarea id="set-shipping" class="sh-form-input resize-none" rows="4" placeholder="CRS delivery after purchase…"></textarea>
                                </div>
                            </div>
                            <button type="button" class="sh-btn sh-btn-blue mt-5" data-save-settings>Save settings</button>
                        </div>
                    </div>
                </div>

                <div id="dash-distcatalog" class="sh-dash-panel hidden">
                    <div class="mb-6">
                        <h2 class="sh-dash-page-title">Distribution catalog</h2>
                        <p class="sh-dash-page-copy">Import wholesale SKUs at reseller markup. Inventory uploads ship in Phase 5.</p>
                    </div>
                    <div class="sh-dash-card overflow-x-auto">
                        <table class="min-w-full text-left text-[13px]">
                            <thead class="border-b border-sh-s100 bg-sh-s50 text-[11px] font-semibold uppercase tracking-[0.12em] text-sh-s400">
                                <tr>
                                    <th class="px-4 py-3">SKU</th>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3">Wholesale</th>
                                    <th class="px-4 py-3">Qty</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody id="dash-dist-catalog-body">
                                <tr><td colspan="5" class="px-4 py-10 text-center text-[13px] text-sh-s400">Loading wholesale inventory…</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="dash-myimports" class="sh-dash-panel hidden">
                    <div class="mb-6">
                        <h2 class="sh-dash-page-title">Imports</h2>
                        <p class="sh-dash-page-copy">Wholesale SKUs you have imported into your catalog.</p>
                    </div>
                    <div class="sh-dash-card p-4">
                        <div id="dash-imports-list"></div>
                    </div>
                </div>

                <div id="dash-mypurchases" class="sh-dash-panel hidden">
                    <div class="mb-6">
                        <h2 class="sh-dash-page-title">Purchases</h2>
                        <p class="sh-dash-page-copy">Distribution purchase history for this store.</p>
                    </div>
                    <div class="sh-dash-card p-4">
                        <div id="dash-purchases-list"></div>
                    </div>
                </div>

                <div id="dash-onsiteappts" class="sh-dash-panel hidden">
                    <div class="mb-6">
                        <h2 class="sh-dash-page-title">Onsite visits</h2>
                        <p class="sh-dash-page-copy">Confirm or cancel showroom appointments from buyers.</p>
                    </div>
                    <div class="sh-dash-card p-4">
                        <div id="dash-onsite-list"></div>
                    </div>
                </div>

                <div id="dash-openapi" class="sh-dash-panel hidden">
                    <div class="mb-6">
                        <h2 class="sh-dash-page-title">Open API</h2>
                        <p class="sh-dash-page-copy">Issue a store token for inventory sync. The plain key is shown once.</p>
                    </div>
                    <div class="sh-dash-card max-w-2xl p-6">
                        <div id="dash-openapi-status" class="rounded-xl bg-sh-s50 px-4 py-3 font-mono text-xs text-sh-s700">No key issued yet.</div>
                        <div id="dash-openapi-plain" class="mt-3 hidden rounded-xl border border-sh-orange/30 bg-sh-orange-lt px-4 py-3 font-mono text-xs text-sh-s800"></div>
                        <div class="mt-5 flex flex-wrap gap-2">
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
