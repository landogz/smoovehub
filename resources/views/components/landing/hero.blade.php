<section class="sh-hero">
    <div class="sh-hero-pattern" aria-hidden="true"></div>
    <div class="sh-container">
        <div class="sh-hero-grid">
            <div>
                <div class="sh-hero-badge">{{ $hero['badge'] }}</div>
                <h1 class="sh-hero-title">
                    Furniture<br>
                    You'll <span class="sh-hero-accent">Love.</span><br>
                    Delivered Fast.
                </h1>
                <p class="sh-hero-subtitle">{{ $hero['subtitle'] }}</p>
                <div class="mb-8 flex flex-wrap gap-3">
                    <a href="#shop" class="sh-btn sh-btn-lg sh-btn-orange">Shop Now →</a>
                    <a href="#quote" class="sh-btn sh-btn-lg sh-btn-ghost">Get Delivery Quote</a>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[13px] text-white/50">
                    <span>🚚 Same-day delivery</span>
                    <span class="hidden sm:inline text-white/30">·</span>
                    <span>🔒 Stripe-secured</span>
                    <span class="hidden sm:inline text-white/30">·</span>
                    <span>⭐ 4.9 avg rating</span>
                </div>
            </div>
            <div class="sh-hero-stats">
                @foreach ($hero['stats'] as $stat)
                    <div class="sh-stat-card">
                        <div class="sh-stat-value">{{ $stat['value'] }}</div>
                        <div class="sh-stat-label">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
