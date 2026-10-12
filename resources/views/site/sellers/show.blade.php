@extends('layouts.landing')

@section('title', $site['seller']['name'] . ' — SmooveHub Store')

@section('content')
    @php $seller = $site['seller']; @endphp

    <section class="border-b border-sh-s200 bg-gradient-to-br from-sh-navy via-sh-blue to-sh-blue-mid py-10 text-white md:py-14">
        <div class="sh-container">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="flex items-start gap-5">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-2xl font-black md:h-20 md:w-20">
                        {{ strtoupper(substr($seller['name'], 0, 1)) }}
                    </div>
                    <div>
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <span class="sh-badge sh-badge-green">Verified Seller</span>
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs text-white/70">{{ $seller['category'] }}</span>
                        </div>
                        <h1 class="sh-display text-2xl font-black md:text-4xl">{{ $seller['name'] }}</h1>
                        <p class="mt-1 text-sm text-white/60">{{ $seller['btype'] }}</p>
                        <p class="mt-3 max-w-xl text-[13px] leading-relaxed text-white/70">{{ $seller['desc'] }}</p>
                        <div class="mt-4 flex flex-wrap gap-4 text-sm text-white/60">
                            <span class="font-semibold text-amber-300">★ {{ $seller['rating'] }}</span>
                            <span>{{ $seller['sales'] }} sales</span>
                            <span>Member since {{ $seller['since'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row md:flex-col">
                    <a href="mailto:{{ $seller['email'] }}" class="sh-btn sh-btn-orange">Contact Seller</a>
                    <a href="{{ route('site.sellers') }}" class="sh-btn sh-btn-ghost">← All Sellers</a>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-sh-s200 bg-white">
        <div class="sh-container">
            <div class="flex gap-1 overflow-x-auto" role="tablist" aria-label="Store sections">
                <button
                    type="button"
                    id="tab-products"
                    class="sh-nav-link sh-nav-link-active shrink-0 rounded-none border-b-2 border-sh-blue px-5 py-4"
                    role="tab"
                    aria-selected="true"
                    aria-controls="panel-products"
                    data-store-tab="products"
                >Products</button>
                <button
                    type="button"
                    id="tab-reviews"
                    class="sh-nav-link shrink-0 rounded-none border-b-2 border-transparent px-5 py-4"
                    role="tab"
                    aria-selected="false"
                    aria-controls="panel-reviews"
                    data-store-tab="reviews"
                >Reviews</button>
                <button
                    type="button"
                    id="tab-about"
                    class="sh-nav-link shrink-0 rounded-none border-b-2 border-transparent px-5 py-4"
                    role="tab"
                    aria-selected="false"
                    aria-controls="panel-about"
                    data-store-tab="about"
                >About</button>
            </div>
        </div>
    </section>

    <section class="sh-section">
        <div class="sh-container">
            <div id="panel-products" role="tabpanel" aria-labelledby="tab-products" data-store-panel="products">
                <div class="mb-6">
                    <h2 class="sh-section-title">Store Products</h2>
                    <p class="sh-section-subtitle">{{ count($site['products']) }} items available</p>
                </div>
                <div class="sh-product-grid">
                    @foreach ($site['products'] as $product)
                        <x-landing.product-card :product="$product" :condition-labels="$site['condition_labels']" />
                    @endforeach
                </div>
            </div>

            <div id="panel-reviews" class="hidden" role="tabpanel" aria-labelledby="tab-reviews" data-store-panel="reviews">
                <div class="mx-auto max-w-2xl space-y-4">
                    @foreach ([['M', 'Maria K.', '★★★★★', 'Beautiful quality and fast delivery. Highly recommend this seller!'], ['J', 'James T.', '★★★★☆', 'Great selection and fair prices. Will shop again.'], ['S', 'Sandra R.', '★★★★★', 'Exactly as described. CRS delivery was seamless.']] as $review)
                        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-5">
                            <div class="mb-3 flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-sh-blue-lt text-sm font-bold text-sh-blue">{{ $review[0] }}</span>
                                <div>
                                    <div class="text-sm font-semibold text-sh-s900">{{ $review[1] }}</div>
                                    <div class="text-xs text-amber-500">{{ $review[2] }}</div>
                                </div>
                            </div>
                            <p class="text-[13px] leading-relaxed text-sh-s600">{{ $review[3] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="panel-about" class="hidden" role="tabpanel" aria-labelledby="tab-about" data-store-panel="about">
                <div class="mx-auto max-w-2xl rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6 md:p-8">
                    <h2 class="mb-4 text-lg font-bold text-sh-s900">About {{ $seller['name'] }}</h2>
                    <p class="mb-6 text-[15px] leading-relaxed text-sh-s600">{{ $seller['desc'] }}</p>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Business Type</dt>
                            <dd class="mt-1 text-sm font-medium text-sh-s900">{{ $seller['btype'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Category</dt>
                            <dd class="mt-1 text-sm font-medium text-sh-s900">{{ $seller['category'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Rating</dt>
                            <dd class="mt-1 text-sm font-medium text-sh-amber">★ {{ $seller['rating'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Total Sales</dt>
                            <dd class="mt-1 text-sm font-medium text-sh-s900">{{ $seller['sales'] }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Contact</dt>
                            <dd class="mt-1 text-sm font-medium text-sh-blue">
                                <a href="mailto:{{ $seller['email'] }}">{{ $seller['email'] }}</a>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </section>
@endsection
