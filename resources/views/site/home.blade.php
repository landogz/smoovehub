@extends('layouts.landing')

@section('title', 'SmooveHub — Your Furniture. Our Promise.')

@section('content')
    <section class="sh-hero">
        <div class="sh-hero-pattern" aria-hidden="true"></div>
        <div class="sh-container">
            <div class="sh-hero-grid">
                <div>
                    <div class="sh-hero-badge">{{ $site['hero']['badge'] }}</div>
                    <h1 class="sh-hero-title">
                        {{ $site['hero']['title'][0] }}<br>
                        You'll <span class="sh-hero-accent">Love.</span><br>
                        {{ $site['hero']['title'][2] }}
                    </h1>
                    <p class="sh-hero-subtitle">{{ $site['hero']['subtitle'] }}</p>
                    <div class="mb-8 flex flex-wrap gap-3">
                        <a href="{{ route('site.shop') }}" class="sh-btn sh-btn-lg sh-btn-orange">Shop Now →</a>
                        <a href="{{ route('site.quote') }}" class="sh-btn sh-btn-lg sh-btn-ghost">Get Delivery Quote</a>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[13px] text-white/50">
                        <span>🚚 Same-day delivery</span>
                        <span class="hidden text-white/30 sm:inline">·</span>
                        <span>🔒 Stripe-secured</span>
                        <span class="hidden text-white/30 sm:inline">·</span>
                        <span>⭐ 4.9 avg rating</span>
                    </div>
                </div>
                <div class="sh-hero-stats">
                    @foreach ($site['hero']['stats'] as $stat)
                        <div class="sh-stat-card">
                            <div class="sh-stat-value">{{ $stat['value'] }}</div>
                            <div class="sh-stat-label">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="sh-section-sm bg-white">
        <div class="sh-container">
            <div class="mb-6">
                <h2 class="sh-section-title">Shop by Category</h2>
                <p class="sh-section-subtitle">AI-curated for your region · Updated hourly</p>
            </div>
            <div class="sh-category-grid">
                @foreach ($site['categories'] as $category)
                    <a href="{{ route($category['route']) }}" class="sh-category-card">
                        <div class="sh-category-icon" aria-hidden="true">{{ $category['icon'] }}</div>
                        <div class="text-[13px] font-semibold">{{ $category['name'] }}</div>
                        <div class="mt-1 text-[11px] text-sh-s400">{{ $category['subtitle'] }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sh-section">
        <div class="sh-container">
            <div class="sh-section-header sh-section-header-lg">
                <div>
                    <h2 class="sh-section-title">Featured Products</h2>
                    <p class="sh-section-subtitle">AI-recommended for buyers this week</p>
                </div>
                <a href="{{ route('site.shop') }}" class="text-sm font-semibold text-sh-blue">View all →</a>
            </div>
            <div class="sh-product-grid">
                @foreach ($site['featured_products'] as $product)
                    <x-landing.product-card :product="$product" :condition-labels="$site['condition_labels']" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="sh-delivery">
        <div class="sh-container">
            <div class="sh-delivery-grid">
                <div>
                    <h2 class="sh-display text-[clamp(32px,4vw,48px)] font-black leading-none tracking-tight">
                        CRM-Coordinated Delivery
                    </h2>
                    <p class="mb-6 mt-3 max-w-[500px] text-[15px] leading-relaxed text-white/70">
                        Every delivery routed through Competitive Relocation Service — live driver tracking, real-time ETAs, and SMS updates.
                    </p>
                    <div class="mb-6 flex flex-wrap gap-3">
                        <a href="{{ route('site.quote') }}" class="sh-btn sh-btn-orange">Get Free Quote →</a>
                        <a href="tel:8773852919" class="sh-btn sh-btn-ghost">📞 877-385-2919</a>
                    </div>
                    <div class="sh-tier-grid">
                        @foreach ($site['delivery_tiers'] as $tier)
                            <div class="sh-tier-card">
                                <div class="sh-tier-range">{{ $tier['range'] }}</div>
                                <div @class([
                                    'sh-tier-status-ok' => $tier['tone'] === 'ok',
                                    'sh-tier-status-warn' => $tier['tone'] === 'warn',
                                    'sh-tier-status-danger' => $tier['tone'] === 'danger',
                                ])>{{ $tier['status'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="sh-phone-card">
                    <div class="text-xs text-white/40">Support line</div>
                    <div class="sh-display mt-1.5 text-[26px] font-black tracking-wide">877-385-2919</div>
                    <div class="mt-1 text-[11px] text-white/50">Mon–Sat · 8AM–8PM</div>
                </div>
            </div>
        </div>
    </section>

    <section class="sh-section bg-white">
        <div class="sh-container">
            <div class="mb-6">
                <h2 class="sh-section-title">🔥 Trending in Your Area</h2>
                <p class="sh-section-subtitle">Real-time demand signals · AI Trend Engine</p>
            </div>
            <div class="sh-product-grid">
                @foreach ($site['trending_products'] as $product)
                    <x-landing.product-card :product="$product" :condition-labels="$site['condition_labels']" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="sh-voice-section">
        <div class="sh-container">
            <p class="mb-2.5 text-xs font-medium text-blue-400">🎤 Voice &amp; AI Search Optimized</p>
            <h2 class="sh-section-title text-white">People Also Ask</h2>
            <div class="sh-voice-grid">
                @foreach ($site['voice_questions'] as $item)
                    <a href="{{ route($item['route']) }}" class="sh-voice-question">
                        <span>{{ $item['question'] }}</span>
                        <span aria-hidden="true">→</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sh-section">
        <div class="sh-container">
            <div class="mb-6">
                <h2 class="sh-section-title">Featured Vendors</h2>
                <p class="sh-section-subtitle">All verified · Stripe Connect onboarded</p>
            </div>
            <div class="sh-vendor-grid">
                @foreach ($site['vendors'] as $vendor)
                    <a href="{{ route('site.sellers') }}" class="sh-vendor-card">
                        <div @class(['sh-vendor-avatar', $vendor['avatar_class']])>{{ $vendor['initial'] }}</div>
                        <div class="text-xs font-semibold">{{ $vendor['name'] }}</div>
                        <div class="text-[11px] text-sh-s400">{{ $vendor['type'] }}</div>
                        <div class="mt-1 text-[11px] font-semibold text-sh-amber">★ {{ $vendor['rating'] }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sh-section bg-white">
        <div class="sh-container">
            <div class="mb-8 text-center">
                <h2 class="sh-section-title">What Our Buyers Say</h2>
            </div>
            <div class="sh-testimonial-grid">
                @foreach ($site['testimonials'] as $testimonial)
                    <article class="sh-testimonial-card">
                        <div class="mb-3 text-sm text-amber-500" aria-label="5 out of 5 stars">★★★★★</div>
                        <p class="sh-testimonial-quote">&ldquo;{{ $testimonial['quote'] }}&rdquo;</p>
                        <div class="flex items-center gap-2.5">
                            <div @class(['sh-testimonial-avatar', $testimonial['avatar_class']])>{{ $testimonial['initial'] }}</div>
                            <div>
                                <div class="text-[13px] font-semibold">{{ $testimonial['name'] }}</div>
                                <div class="text-[11px] text-sh-s400">{{ $testimonial['role'] }}</div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sh-newsletter">
        <div class="sh-container">
            <h2 class="sh-section-title text-white">Get Furniture Deals in Your City</h2>
            <p class="mt-2 text-sm text-white/45">AI-curated deals, new arrivals, and local trends — weekly.</p>
            <form id="newsletter-form" class="mx-auto mt-5 flex max-w-[440px] flex-col gap-2 sm:flex-row">
                @csrf
                <input
                    type="email"
                    name="email"
                    required
                    class="sh-newsletter-input"
                    placeholder="your@email.com"
                    aria-label="Email address"
                >
                <button id="newsletter-submit" type="submit" class="sh-btn sh-btn-orange shrink-0">Get Deals</button>
            </form>
            <p id="newsletter-success" class="mt-3 hidden text-[13px] text-green-400">✓ Subscribed! Check your inbox.</p>
        </div>
    </section>
@endsection
