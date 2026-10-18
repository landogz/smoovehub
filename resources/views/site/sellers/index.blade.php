@extends('layouts.landing')

@section('title', 'Verified Sellers — SmooveHub')

@section('content')
    {{-- HERO --}}
    <section class="sh-sdir-hero">
        <div class="sh-container">
            <h1 class="sh-display mb-2 text-[clamp(36px,5vw,56px)] font-black tracking-tight">
                All Verified Sellers
            </h1>
            <p class="mb-6 text-[15px] text-white/65">
                Browse our network of
                <strong id="sellers-count" class="text-white">{{ count($site['sellers']) }}</strong>
                trusted furniture stores
            </p>
            <div class="relative mx-auto max-w-[480px]">
                <input
                    id="sellers-search"
                    type="search"
                    class="sh-sdir-search"
                    placeholder="Search stores by name or category…"
                    aria-label="Search stores by name or category"
                    autocomplete="off"
                >
            </div>
        </div>
    </section>

    {{-- SELLER GRID --}}
    <section class="py-8 md:py-[32px] md:pb-16">
        <div class="sh-container">
            <div id="sellers-grid" class="sh-sdir-grid">
                @foreach ($site['sellers'] as $seller)
                    <a
                        href="{{ route('site.store.show', $seller['slug']) }}"
                        class="sh-sdir-card"
                        data-seller-card
                        data-seller-name="{{ strtolower($seller['name']) }}"
                        data-seller-category="{{ strtolower($seller['category']) }}"
                    >
                        <div class="sh-sdir-card-banner">
                            <div class="sh-sdir-avatar" aria-hidden="true">
                                @if (! empty($seller['logo_url']))
                                    <img src="{{ $seller['logo_url'] }}" alt="" class="h-full w-full object-cover">
                                @else
                                    {{ $seller['initials'] ?? strtoupper(substr($seller['name'], 0, 1)) }}
                                @endif
                            </div>
                        </div>

                        <div class="sh-sdir-body">
                            <div class="mb-1.5 flex items-start justify-between gap-2">
                                <h2 class="text-[15px] font-bold leading-tight text-sh-s900">{{ $seller['name'] }}</h2>
                                <span class="shrink-0 rounded-full bg-sh-green-bg px-[7px] py-0.5 text-[10px] font-bold text-sh-green">✓ Verified</span>
                            </div>

                            <span @class([
                                'mb-2 inline-block rounded-full px-2 py-0.5 text-[10px] font-bold',
                                'sh-sdir-category-residential' => ($seller['category_tone'] ?? '') === 'residential',
                                'sh-sdir-category-commercial' => ($seller['category_tone'] ?? '') === 'commercial',
                                'sh-sdir-category-mixed' => ($seller['category_tone'] ?? '') === 'mixed',
                                'sh-sdir-category-appliances' => ($seller['category_tone'] ?? '') === 'appliances',
                            ])>{{ $seller['category'] }}</span>

                            <p class="mb-3 line-clamp-2 text-xs leading-snug text-sh-s500">{{ $seller['desc'] }}</p>

                            <div class="flex items-center justify-between text-xs">
                                <div class="text-amber-500">
                                    ★★★★★
                                    <span class="ml-1 text-sh-s400">{{ number_format($seller['rating'], 1) }}</span>
                                </div>
                                <div class="text-sh-s500">{{ $seller['sales'] }} sales · Since {{ $seller['since'] }}</div>
                            </div>

                            <div class="sh-sdir-visit-btn">Visit Store →</div>
                        </div>
                    </a>
                @endforeach
            </div>

            <p id="sellers-empty" class="hidden py-16 text-center text-[13px] text-sh-s500">
                No stores match your search. Try a different name or category.
            </p>
        </div>
    </section>
@endsection
