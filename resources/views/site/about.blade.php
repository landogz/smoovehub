@extends('layouts.landing')

@section('title', 'About SmooveHub — Your Furniture. Our Promise.')

@section('content')
    {{-- HERO --}}
    <section class="sh-about-hero">
        <div class="sh-hero-pattern" aria-hidden="true"></div>
        <div class="sh-about-hero-glow" aria-hidden="true"></div>
        <div class="sh-container relative z-[1]">
            <div class="sh-hero-badge">{{ $site['hero_badge'] }}</div>
            <h1 class="sh-hero-title">
                About<br>
                <span class="sh-hero-accent">SmooveHub</span>
            </h1>
            <p class="mb-8 max-w-[580px] text-lg leading-relaxed text-white/70 md:mb-9">
                {{ $site['hero_subtitle'] }}
            </p>
            <div class="flex flex-wrap gap-3">
                @foreach ($site['hero_stats'] as $stat)
                    <div class="sh-about-stat-pill">
                        <div @class([
                            'sh-display text-[28px] font-black leading-none',
                            'text-white' => ($stat['tone'] ?? 'white') === 'white',
                            'text-sh-orange' => ($stat['tone'] ?? '') === 'orange',
                            'text-green-400' => ($stat['tone'] ?? '') === 'green',
                        ])>{{ $stat['value'] }}</div>
                        <div class="mt-0.5 text-xs text-white/55">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- INTRO / OUR STORY --}}
    <section class="sh-about-section bg-white">
        <div class="sh-container">
            <div class="sh-about-split">
                <div>
                    <div class="sh-about-kicker">Our Story</div>
                    <h2 class="sh-display mb-4 text-[clamp(30px,4vw,44px)] font-black leading-[1.05] tracking-tight text-sh-s900">
                        Built to modernize the furniture industry
                    </h2>
                    @foreach ($site['intro'] as $paragraph)
                        <p class="mb-4 text-[15px] leading-[1.8] text-sh-s600 last:mb-6">{{ $paragraph }}</p>
                    @endforeach
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('site.sell') }}" class="sh-btn sh-btn-blue">Become a Seller →</a>
                        <a href="{{ route('site.shop') }}" class="sh-btn sh-btn-outline">Browse Furniture →</a>
                    </div>
                </div>

                <div class="sh-about-visual-grid">
                    @foreach ($site['story_cards'] as $card)
                        <div @class([
                            'sh-about-visual-card',
                            'sh-about-visual-navy' => ($card['tone'] ?? '') === 'navy',
                            'sh-about-visual-orange' => ($card['tone'] ?? '') === 'orange',
                            'sh-about-visual-green' => ($card['tone'] ?? '') === 'green',
                            'sh-about-visual-amber' => ($card['tone'] ?? '') === 'amber',
                        ])>
                            <div class="mb-3 text-[30px]" aria-hidden="true">{{ $card['icon'] }}</div>
                            <div @class([
                                'sh-display mb-1.5 text-lg font-extrabold',
                                'text-white' => in_array($card['tone'] ?? '', ['navy', 'orange'], true),
                                'text-sh-s900' => ! in_array($card['tone'] ?? '', ['navy', 'orange'], true),
                            ])>{{ $card['title'] }}</div>
                            <div @class([
                                'text-xs leading-snug',
                                'text-white/55' => ($card['tone'] ?? '') === 'navy',
                                'text-white/75' => ($card['tone'] ?? '') === 'orange',
                                'text-sh-s600' => ! in_array($card['tone'] ?? '', ['navy', 'orange'], true),
                            ])>{{ $card['text'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- WHAT WE DO --}}
    <section class="sh-about-section bg-sh-s50">
        <div class="sh-container">
            <div class="mb-10 text-center md:mb-12">
                <div class="sh-about-kicker">What We Do</div>
                <h2 class="sh-display mb-2.5 text-[clamp(28px,4vw,42px)] font-black tracking-tight text-sh-s900">
                    Everything you need, in one place
                </h2>
                <p class="mx-auto max-w-[540px] text-[15px] text-sh-s500">
                    SmooveHub provides an all-in-one online marketplace connecting buyers with sellers and coordinating deliveries through CRS.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-7 lg:grid-cols-2">
                {{-- Buyers --}}
                <div class="sh-about-feature-panel">
                    <div class="sh-about-feature-badge-buyer">🛒 For Buyers</div>
                    <div class="flex flex-col gap-3">
                        @foreach ($site['buyer_features'] as $feature)
                            @if (! empty($feature['route']))
                                <a href="{{ route($feature['route']) }}" class="group sh-about-feature-row sh-about-feature-row-link -mx-2 px-2 py-1">
                                    <span class="sh-about-feature-icon-buyer" aria-hidden="true">{{ $feature['icon'] }}</span>
                                    <span>
                                        <span class="block text-sm font-semibold text-sh-s900">{{ $feature['title'] }}</span>
                                        <span class="mt-0.5 block text-xs text-sh-s500">{{ $feature['text'] }}</span>
                                    </span>
                                </a>
                            @else
                                <div class="sh-about-feature-row">
                                    <span class="sh-about-feature-icon-static-buyer" aria-hidden="true">{{ $feature['icon'] }}</span>
                                    <span>
                                        <span class="block text-sm font-semibold text-sh-s900">{{ $feature['title'] }}</span>
                                        <span class="mt-0.5 block text-xs text-sh-s500">{{ $feature['text'] }}</span>
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <a href="{{ route('site.shop') }}" class="sh-btn sh-btn-blue sh-btn-full mt-6">Start Shopping →</a>
                </div>

                {{-- Vendors --}}
                <div class="sh-about-feature-panel">
                    <div class="sh-about-feature-badge-vendor">🏪 For Vendors &amp; Stores</div>
                    <div class="flex flex-col gap-3">
                        @foreach ($site['vendor_features'] as $feature)
                            @if (! empty($feature['route']))
                                <a href="{{ route($feature['route']) }}" class="group sh-about-feature-row sh-about-feature-row-link -mx-2 px-2 py-1">
                                    <span class="sh-about-feature-icon-vendor" aria-hidden="true">{{ $feature['icon'] }}</span>
                                    <span>
                                        <span class="block text-sm font-semibold text-sh-s900">{{ $feature['title'] }}</span>
                                        <span class="mt-0.5 block text-xs text-sh-s500">{{ $feature['text'] }}</span>
                                    </span>
                                </a>
                            @else
                                <div class="sh-about-feature-row">
                                    <span class="sh-about-feature-icon-static-vendor" aria-hidden="true">{{ $feature['icon'] }}</span>
                                    <span>
                                        <span class="block text-sm font-semibold text-sh-s900">{{ $feature['title'] }}</span>
                                        <span class="mt-0.5 block text-xs text-sh-s500">{{ $feature['text'] }}</span>
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <button type="button" class="sh-btn sh-btn-orange sh-btn-full mt-6" data-modal-open="seller-signup-modal">Start Selling Free →</button>
                </div>
            </div>
        </div>
    </section>

    {{-- OUR MISSION --}}
    <section class="sh-about-mission">
        <div class="sh-container">
            <div class="sh-about-split">
                <div>
                    <div class="sh-about-kicker">Our Mission</div>
                    <h2 class="sh-display mb-4 text-[clamp(28px,4vw,44px)] font-black leading-[1.05] tracking-tight">
                        Modernize the furniture marketplace industry
                    </h2>
                    <p class="mb-4 text-[15px] leading-[1.8] text-white/70">
                        Our mission is to help local stores and independent sellers compete in the digital world through smarter technology, logistics integration, and scalable online visibility.
                    </p>
                    <p class="mb-7 text-[15px] leading-[1.8] text-white/70">
                        We believe great furniture businesses deserve powerful online tools without the complexity of building everything from scratch.
                    </p>
                    <div class="sh-about-vision-box">
                        <div class="sh-display mb-2.5 text-lg font-bold text-sh-orange">Our Vision</div>
                        <p class="text-sm leading-[1.75] text-white/65">
                            SmooveHub aims to become a leading online destination for furniture, home furnishings, and delivery coordination — combining eCommerce, logistics, and automation into one connected ecosystem.
                        </p>
                    </div>
                </div>

                <div>
                    <div class="mb-4 text-[13px] font-semibold uppercase tracking-widest text-white/50">
                        We are continuously building smarter tools for:
                    </div>
                    <div class="flex flex-col gap-2.5">
                        @foreach ($site['building_for'] as $item)
                            <div class="sh-about-building-item">
                                <span class="shrink-0 text-[22px]" aria-hidden="true">{{ $item['icon'] }}</span>
                                <div>
                                    <div class="text-sm font-semibold text-white">{{ $item['title'] }}</div>
                                    <div class="mt-0.5 text-xs text-white/50">{{ $item['text'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- WHY SMOOVEHUB --}}
    <section class="sh-about-section bg-white">
        <div class="sh-container">
            <div class="mb-10 text-center md:mb-12">
                <div class="sh-about-kicker">Why SmooveHub</div>
                <h2 class="sh-display mb-2.5 text-[clamp(28px,4vw,42px)] font-black tracking-tight text-sh-s900">
                    Built differently from the start
                </h2>
                <p class="mx-auto max-w-[500px] text-[15px] text-sh-s500">
                    Five core pillars that make SmooveHub the smarter choice for furniture buyers and sellers.
                </p>
            </div>

            @php
                $pillarIcons = [
                    'blue' => 'from-sh-blue-lt to-indigo-200',
                    'orange' => 'from-sh-orange-lt to-orange-200',
                    'purple' => 'from-violet-100 to-violet-200',
                    'green' => 'from-sh-green-bg to-green-200',
                ];
            @endphp

            <div class="mb-5 grid grid-cols-1 gap-5 md:grid-cols-3">
                @foreach (array_slice($site['pillars'], 0, 3) as $pillar)
                    <article @class([
                        'sh-about-pillar',
                        'sh-about-pillar-blue' => ($pillar['hover'] ?? '') === 'blue',
                        'sh-about-pillar-orange' => ($pillar['hover'] ?? '') === 'orange',
                        'sh-about-pillar-purple' => ($pillar['hover'] ?? '') === 'purple',
                        'sh-about-pillar-green' => ($pillar['hover'] ?? '') === 'green',
                    ])>
                        <div @class(['sh-about-pillar-icon bg-gradient-to-br', $pillarIcons[$pillar['hover'] ?? 'blue'] ?? 'from-sh-blue-lt to-indigo-200']) aria-hidden="true">
                            {{ $pillar['icon'] }}
                        </div>
                        <h3 class="sh-display mb-2 text-xl font-extrabold text-sh-s900">{{ $pillar['title'] }}</h3>
                        <p class="text-[13px] leading-[1.7] text-sh-s600">{{ $pillar['text'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                @foreach (array_slice($site['pillars'], 3) as $pillar)
                    <article @class([
                        'sh-about-pillar',
                        'sh-about-pillar-blue' => ($pillar['hover'] ?? '') === 'blue',
                        'sh-about-pillar-orange' => ($pillar['hover'] ?? '') === 'orange',
                        'sh-about-pillar-purple' => ($pillar['hover'] ?? '') === 'purple',
                        'sh-about-pillar-green' => ($pillar['hover'] ?? '') === 'green',
                    ])>
                        <div @class(['sh-about-pillar-icon bg-gradient-to-br', $pillarIcons[$pillar['hover'] ?? 'blue'] ?? 'from-sh-blue-lt to-indigo-200']) aria-hidden="true">
                            {{ $pillar['icon'] }}
                        </div>
                        <h3 class="sh-display mb-2 text-xl font-extrabold text-sh-s900">{{ $pillar['title'] }}</h3>
                        <p class="text-[13px] leading-[1.7] text-sh-s600">{{ $pillar['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- JOIN THE NETWORK --}}
    <section class="sh-about-section bg-sh-s50">
        <div class="sh-container">
            <div class="mb-10 text-center md:mb-12">
                <div class="sh-about-kicker">Join the Network</div>
                <h2 class="sh-display mb-2.5 text-[clamp(28px,4vw,42px)] font-black tracking-tight text-sh-s900">
                    SmooveHub is designed for you
                </h2>
                <p class="mx-auto max-w-[500px] text-[15px] text-sh-s500">
                    Whether you are a furniture store, thrift shop, distributor, reseller, or buyer — we make the process smoother, smarter, and more connected.
                </p>
            </div>

            <div class="mb-10 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($site['audience_cards'] as $card)
                    <a
                        href="{{ route($card['route']) }}"
                        @class([
                            'sh-about-audience-card',
                            'sh-about-audience-card-shop' => ($card['route'] ?? '') === 'site.shop',
                        ])
                    >
                        <div class="mb-2.5 text-[28px]" aria-hidden="true">{{ $card['icon'] }}</div>
                        <div class="text-[13px] font-bold text-sh-s900">{{ $card['title'] }}</div>
                        <div class="mt-1 text-[11px] text-sh-s500">{{ $card['text'] }}</div>
                    </a>
                @endforeach
            </div>

            <div class="sh-about-final-cta">
                <h3 class="sh-display mb-2.5 text-[clamp(24px,3vw,36px)] font-black">Ready to get started?</h3>
                <p class="mx-auto mb-7 max-w-[460px] text-[15px] leading-relaxed text-white/65">
                    Join the SmooveHub network today. No setup fees. No complexity. Just a smarter way to sell and buy furniture online.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3 md:gap-3.5">
                    <button type="button" class="sh-btn sh-btn-lg sh-btn-orange" data-modal-open="seller-signup-modal">Create Seller Account →</button>
                    <a href="{{ route('site.shop') }}" class="sh-btn sh-btn-lg sh-btn-ghost">Browse Furniture</a>
                    <a href="{{ route('site.contact') }}" class="sh-btn sh-btn-lg sh-btn-ghost">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
@endsection
