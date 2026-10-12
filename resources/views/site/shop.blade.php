@extends('layouts.landing')

@section('title', 'Shop Furniture — SmooveHub')

@section('content')
    <section class="border-b border-sh-s200 bg-white py-8 md:py-10">
        <div class="sh-container">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="sh-section-title text-2xl md:text-3xl">Shop Furniture</h1>
                    <p class="sh-section-subtitle"><span id="shop-result-count">{{ $site['total'] }}</span> products from verified sellers nationwide</p>
                </div>
                <div class="flex items-center gap-3">
                    <label for="shop-sort" class="sh-form-label mb-0 shrink-0">Sort by</label>
                    <select id="shop-sort" class="sh-form-input w-full min-w-[180px] md:w-auto">
                        @foreach ($site['sort_options'] as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </section>

    <section class="sh-section">
        <div class="sh-container">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[240px_minmax(0,1fr)]">
                <aside class="space-y-6">
                    @foreach ($site['filters'] as $filter)
                        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-5">
                            <h2 class="mb-4 text-xs font-semibold uppercase tracking-widest text-sh-s500">{{ $filter['label'] }}</h2>
                            <ul class="space-y-2">
                                @foreach ($filter['items'] as $item)
                                    <li>
                                        <label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1.5 text-[13px] text-sh-s700 transition-colors hover:bg-sh-s50">
                                            <input type="checkbox" class="h-4 w-4 rounded border-sh-s300 text-sh-blue focus:ring-sh-blue" data-shop-filter="{{ $item['filter'] ?? '' }}">
                                            <span aria-hidden="true">{{ $item['icon'] }}</span>
                                            <span>{{ $item['label'] }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </aside>

                <div>
                    <div id="shop-product-grid" class="sh-product-grid" data-condition-labels='@json($site['condition_labels'])'>
                        @foreach ($site['products'] as $product)
                            <x-landing.product-card :product="$product" :condition-labels="$site['condition_labels']" />
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
