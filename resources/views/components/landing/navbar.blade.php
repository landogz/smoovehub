<nav class="sh-nav" aria-label="Primary">
    <div class="sh-nav-wrap">
        <div class="sh-nav-inner">
            <a href="{{ route('site.home') }}" class="sh-nav-logo">
                <img
                    src="{{ asset('images/landing/logo-nav.png') }}"
                    alt="SmooveHub"
                >
            </a>

            <div class="sh-nav-search max-[1100px]:hidden">
                <svg class="sh-nav-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35"></path>
                </svg>
                <input
                    id="nav-search"
                    type="search"
                    class="sh-nav-search-input"
                    placeholder="Search furniture, sellers, cities…"
                    aria-label="Search furniture"
                    data-search-target="{{ route('site.shop') }}"
                >
            </div>

            <div class="sh-nav-links hidden lg:flex" id="desktop-nav-links">
                @foreach ($navLinks as $link)
                    <a href="{{ route($link['route']) }}" @class([
                        'sh-nav-link',
                        $link['class'] ?? '',
                        'sh-nav-link-active' => ($activeNav ?? null) === ($link['key'] ?? null),
                    ])>{{ $link['label'] }}</a>
                @endforeach
                @foreach ($navPills ?? [] as $pill)
                    <a href="{{ route($pill['route']) }}" @class(['sh-nav-pill sh-nav-pill-inline hidden 2xl:inline-flex', $pill['class'] ?? ''])>{{ $pill['label'] }}</a>
                @endforeach
            </div>

            @if (! empty($navPills))
                <div class="relative hidden shrink-0 lg:block 2xl:hidden">
                    <button
                        id="nav-portals-toggle"
                        type="button"
                        class="sh-nav-portals-btn"
                        aria-expanded="false"
                        aria-controls="nav-portals-menu"
                        aria-haspopup="true"
                    >
                        <span>Portals</span>
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="nav-portals-menu" class="sh-nav-dropdown" role="menu">
                        @foreach ($navPills as $pill)
                            <a
                                href="{{ route($pill['route']) }}"
                                @class(['sh-nav-dropdown-item', 'sh-nav-dropdown-item-' . str_replace('sh-nav-pill-', '', $pill['class'] ?? 'default')])
                                role="menuitem"
                            >{{ $pill['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="sh-nav-actions">
                <div id="nav-notifications" class="relative hidden">
                    <button type="button" id="notif-toggle" class="sh-icon-btn" title="Notifications" aria-label="Notifications">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span data-notif-count class="sh-cart-badge hidden">0</span>
                    </button>
                    <div id="notif-panel" class="absolute right-0 top-[calc(100%+8px)] z-[350] hidden w-[min(100vw-2rem,320px)] overflow-hidden rounded-[14px] border border-sh-s200 bg-white shadow-[0_16px_48px_rgba(15,23,42,0.14)]">
                        <div class="flex items-center justify-between border-b border-sh-s100 px-4 py-3">
                            <span class="text-sm font-bold text-sh-s900">Notifications</span>
                            <button type="button" id="notif-mark-all" class="text-xs font-semibold text-sh-blue">Mark all read</button>
                        </div>
                        <div id="notif-list" class="max-h-80 overflow-y-auto"></div>
                    </div>
                </div>

                <a href="{{ route('site.account') }}#purchases" class="sh-icon-btn" title="Cart" aria-label="Shopping cart" data-cart-link>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span data-cart-count class="sh-cart-badge">0</span>
                </a>

                <div id="nav-guest" class="sh-nav-auth max-lg:hidden lg:flex">
                    <button type="button" class="sh-btn sh-btn-sm sh-btn-outline" data-modal-open="login-modal">Log In</button>
                    <button type="button" class="sh-btn sh-btn-sm sh-btn-blue" data-modal-open="signup-modal">Sign Up</button>
                </div>

                <div id="nav-user" class="relative hidden items-center">
                    <button
                        id="nav-user-toggle"
                        type="button"
                        class="flex min-h-11 min-w-11 items-center gap-2 rounded-[10px] border border-sh-s200 bg-white px-2 py-1.5 transition-colors hover:border-sh-blue/40 hover:bg-sh-blue-lt"
                        aria-expanded="false"
                        aria-controls="nav-user-menu"
                    >
                        <span id="nav-user-avatar" class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-sh-blue text-xs font-bold text-white">U</span>
                        <span class="hidden min-w-0 text-left lg:block">
                            <span id="nav-user-name" class="block max-w-[120px] truncate text-[13px] font-semibold text-sh-s900">User</span>
                            <span id="nav-user-role" class="block text-[11px] text-sh-s500">Customer</span>
                        </span>
                        <svg class="hidden h-4 w-4 text-sh-s400 lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div id="nav-user-menu" class="absolute right-0 top-[calc(100%+8px)] z-[350] hidden min-w-[220px] overflow-hidden rounded-[14px] border border-sh-s200 bg-white py-1 shadow-[0_16px_48px_rgba(15,23,42,0.14)]">
                        <a id="nav-link-account" href="{{ route('site.account') }}" class="sh-nav-user-item hidden">My Account</a>
                        <a id="nav-link-seller" href="{{ route('site.dashboard') }}" class="sh-nav-user-item hidden">Seller Dashboard</a>
                        <button type="button" class="sh-nav-user-item" data-auth-logout-other-devices>
                            Sign out other devices
                        </button>
                        <button type="button" class="sh-nav-user-item sh-nav-user-item-danger" data-auth-logout>
                            Sign out
                        </button>
                    </div>
                </div>

                <button
                    id="mobile-menu-toggle"
                    type="button"
                    class="sh-nav-menu-btn inline-flex lg:hidden"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                    aria-label="Toggle navigation menu"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="sh-mobile-menu">
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}" @class([
                    'sh-mobile-menu-link',
                    'sh-mobile-menu-link-sell' => ($link['class'] ?? '') === 'sh-nav-link-sell',
                ])>{{ $link['label'] }}</a>
            @endforeach
            @foreach ($navPills ?? [] as $pill)
                <a href="{{ route($pill['route']) }}" @class([
                    'sh-mobile-menu-link',
                    'sh-mobile-menu-link-purple' => ($pill['class'] ?? '') === 'sh-nav-pill-purple',
                    'sh-mobile-menu-link-orange' => ($pill['class'] ?? '') === 'sh-nav-pill-orange',
                    'sh-mobile-menu-link-navy' => ($pill['class'] ?? '') === 'sh-nav-pill-navy',
                    'sh-mobile-menu-link-red' => ($pill['class'] ?? '') === 'sh-nav-pill-red',
                ])>{{ $pill['label'] }}</a>
            @endforeach
            <div id="mobile-nav-guest" class="sh-mobile-menu-auth lg:hidden">
                <button type="button" class="sh-btn sh-btn-sm sh-btn-outline flex-1" data-modal-open="login-modal">Log In</button>
                <button type="button" class="sh-btn sh-btn-sm sh-btn-blue flex-1" data-modal-open="signup-modal">Sign Up</button>
            </div>
            <div id="mobile-nav-user" class="hidden flex-col gap-2 p-4 lg:hidden">
                <a id="nav-link-account-mobile" href="{{ route('site.account') }}" class="hidden sh-btn sh-btn-sm sh-btn-outline w-full">My Account</a>
                <a id="nav-link-seller-mobile" href="{{ route('site.dashboard') }}" class="hidden sh-btn sh-btn-sm sh-btn-outline w-full">Seller Dashboard</a>
                <button type="button" class="sh-btn sh-btn-sm sh-btn-outline w-full" data-auth-logout-other-devices>Sign out other devices</button>
                <button type="button" class="sh-btn sh-btn-sm sh-btn-outline w-full text-red-600" data-auth-logout>Sign out</button>
            </div>
        </div>
    </div>
</nav>
