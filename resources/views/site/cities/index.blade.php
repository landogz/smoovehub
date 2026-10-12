@extends('layouts.landing')

@section('title', 'Furniture by City — SmooveHub')

@section('content')
    @php $featured = $site['featured_city']; @endphp

    <section class="relative overflow-hidden bg-gradient-to-br from-sh-navy via-sh-blue to-sh-blue-mid py-12 text-white md:py-16 lg:py-20">
        <div class="pointer-events-none absolute inset-0 bg-[repeating-linear-gradient(-55deg,transparent,transparent_20px,rgba(255,255,255,0.025)_20px,rgba(255,255,255,0.025)_21px)]" aria-hidden="true"></div>
        <div class="sh-container relative z-[1]">
            <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2">
                <div>
                    <div class="sh-hero-badge">🌎 Nationwide Coverage · AI City Targeting</div>
                    <h1 class="sh-display text-[clamp(32px,6vw,56px)] font-black leading-tight tracking-tight">
                        Furniture in <span class="text-sh-orange">{{ $featured['name'] }}</span>
                    </h1>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-white/70 md:text-[17px]">
                        Shop local inventory, same-day delivery, and verified sellers in the {{ $featured['label'] }} metro area.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('site.cities.show', $featured['slug']) }}" class="sh-btn sh-btn-lg sh-btn-orange">Shop {{ $featured['name'] }} →</a>
                        <a href="{{ route('site.quote') }}" class="sh-btn sh-btn-lg sh-btn-ghost">Get Delivery Quote</a>
                    </div>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/10 p-8 text-center">
                    <div class="text-6xl" aria-hidden="true">{{ $featured['emoji'] }}</div>
                    <div class="sh-display mt-4 text-3xl font-black">{{ $featured['label'] }}</div>
                    <p class="mt-2 text-sm text-white/60">Featured city · Same-day delivery available</p>
                    <a href="{{ route('site.cities.show', $featured['slug']) }}" class="sh-btn sh-btn-orange mt-6">Explore {{ $featured['name'] }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="sh-section bg-white">
        <div class="sh-container">
            <div class="mb-8 text-center">
                <h2 class="sh-section-title">All Cities</h2>
                <p class="sh-section-subtitle">Select your city to browse local furniture</p>
            </div>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach ($site['cities'] as $city)
                    <a
                        href="{{ route('site.cities.show', $city['slug']) }}"
                        class="inline-flex items-center gap-2 rounded-full border-[1.5px] border-sh-s200 bg-white px-4 py-2.5 text-sm font-semibold text-sh-s700 transition-all hover:border-sh-blue hover:bg-sh-blue-lt hover:text-sh-blue"
                    >
                        <span aria-hidden="true">{{ $city['emoji'] }}</span>
                        <span>{{ $city['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sh-section">
        <div class="sh-container">
            <div class="mb-6">
                <h2 class="sh-section-title">Popular in {{ $featured['name'] }}</h2>
                <p class="sh-section-subtitle">Trending products in the {{ $featured['label'] }} area</p>
            </div>
            <div class="sh-product-grid">
                @foreach ($site['products'] as $product)
                    <x-landing.product-card :product="$product" :condition-labels="$site['condition_labels']" />
                @endforeach
            </div>
        </div>
    </section>
@endsection
