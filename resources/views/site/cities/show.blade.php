@extends('layouts.landing')

@section('title', $site['city']['label'] . ' Furniture — SmooveHub')

@section('content')
    @php $city = $site['city']; @endphp

    <x-site.page-hero
        :badge="$city['emoji'] . ' ' . $city['label']"
        :title="'Furniture in ' . $city['name']"
        :subtitle="'Shop verified sellers, same-day delivery, and AI-curated picks in the ' . $city['label'] . ' metro area.'"
        gradient="navy"
    >
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('site.shop') }}" class="sh-btn sh-btn-lg sh-btn-orange">Shop All →</a>
            <a href="{{ route('site.quote') }}" class="sh-btn sh-btn-lg sh-btn-ghost">Delivery Quote</a>
            <a href="{{ route('site.cities') }}" class="sh-btn sh-btn-lg sh-btn-ghost">← All Cities</a>
        </div>
    </x-site.page-hero>

    <section class="sh-section">
        <div class="sh-container">
            <div class="mb-6">
                <h2 class="sh-section-title">Furniture in {{ $city['label'] }}</h2>
                <p class="sh-section-subtitle">Local inventory from verified sellers</p>
            </div>
            <div class="sh-product-grid">
                @foreach ($site['products'] as $product)
                    <x-landing.product-card :product="$product" :condition-labels="$site['condition_labels']" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="sh-section bg-white">
        <div class="sh-container">
            <div class="mb-6 text-center">
                <h2 class="sh-section-title">More Cities</h2>
                <p class="sh-section-subtitle">Explore furniture in other metro areas</p>
            </div>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach ($site['cities'] as $otherCity)
                    @if ($otherCity['slug'] !== $city['slug'])
                        <a
                            href="{{ route('site.cities.show', $otherCity['slug']) }}"
                            class="inline-flex items-center gap-2 rounded-full border-[1.5px] border-sh-s200 bg-white px-4 py-2.5 text-sm font-semibold text-sh-s700 transition-all hover:border-sh-blue hover:bg-sh-blue-lt hover:text-sh-blue"
                        >
                            <span aria-hidden="true">{{ $otherCity['emoji'] }}</span>
                            <span>{{ $otherCity['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endsection
