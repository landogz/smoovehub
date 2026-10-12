@extends('layouts.landing')

@section('title', 'Sell on SmooveHub — Start Your Storefront')

@section('content')
    {{-- Hero --}}
    <section class="sh-sell-hero">
        <div class="sh-container">
            <h1 class="sh-sell-title">Sell on SmooveHub</h1>
            <p class="sh-sell-subtitle">{{ $site['hero_subtitle'] }}</p>
            <button type="button" class="sh-btn sh-btn-lg sh-btn-dark" data-modal-open="seller-signup-modal">
                Create Seller Account →
            </button>
            <p class="mt-3.5 text-[13px] text-white/60">
                Already have an account?
                <button type="button" class="underline hover:text-white" data-modal-open="login-modal">Log in</button>
            </p>
        </div>
    </section>

    {{-- Benefits + pricing --}}
    <section class="sh-sell-body">
        <div class="sh-container">
            <div class="mb-12 grid grid-cols-1 gap-5 md:grid-cols-3">
                @foreach ($site['benefits'] as $benefit)
                    <div class="sh-sell-benefit-card">
                        <div class="mb-3.5 text-[40px]" aria-hidden="true">{{ $benefit['icon'] }}</div>
                        <h3 class="sh-sell-benefit-title">{{ $benefit['title'] }}</h3>
                        <p class="text-sm leading-relaxed text-sh-s500">{{ $benefit['text'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="sh-sell-pricing-panel">
                <h2 class="sh-sell-pricing-title">How pricing works</h2>

                <div class="mx-auto grid max-w-[560px] grid-cols-1 gap-5 sm:grid-cols-2">
                    @foreach ($site['pricing'] as $tier)
                        <div class="sh-sell-pricing-tier">
                            <div @class([
                                'sh-sell-pricing-value',
                                'text-green-400' => ($tier['tone'] ?? '') === 'green',
                                'text-violet-300' => ($tier['tone'] ?? '') === 'purple',
                            ])>{{ $tier['value'] }}</div>
                            <div class="mt-1.5 text-[13px] font-semibold text-white/70">{{ $tier['label'] }}</div>
                            <div class="mt-1 text-[11px] leading-snug text-white/40">{{ $tier['description'] }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="sh-sell-pricing-example">
                    💡 <strong>Example:</strong> You price an item at <strong>$500</strong>. SmooveHub adds $50 (10%) — customer pays <strong>$550</strong>. You receive the full <strong>$500</strong> in your bank account.
                </div>
            </div>
        </div>
    </section>
@endsection
