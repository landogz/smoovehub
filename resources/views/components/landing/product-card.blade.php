@props(['product', 'conditionLabels'])

@php
    $condition = $conditionLabels[$product['condition']] ?? ['label' => 'Item', 'class' => 'sh-badge-slate'];
    $original = ($product['original'] ?? 0) > 0
        ? '$' . number_format($product['original'])
        : null;
@endphp

<article class="sh-product-card cursor-pointer transition-shadow hover:shadow-lg" data-purchase-id="{{ $product['id'] }}" role="button" tabindex="0" aria-label="Buy {{ $product['name'] }}">
    <div class="sh-product-image">
        <span aria-hidden="true">{{ $product['icon'] }}</span>
        <div class="absolute left-2.5 top-2.5 flex flex-col gap-1">
            <span @class(['sh-badge', $condition['class']])>{{ $condition['label'] }}</span>
            @if ($product['trending'] ?? false)
                <span class="sh-badge sh-badge-orange">🔥</span>
            @endif
            @if ($product['same_day'] ?? false)
                <span class="sh-badge sh-badge-green">⚡</span>
            @endif
        </div>
        <span class="absolute right-2.5 top-2.5 flex h-[30px] w-[30px] items-center justify-center rounded-full bg-white/90 text-[13px]">❤️</span>
    </div>
    <div class="sh-product-info">
        <div class="sh-product-seller">{{ $product['seller'] }} ✓</div>
        <h3 class="sh-product-name">{{ $product['name'] }}</h3>
        <div class="flex items-center justify-between gap-2">
            <div>
                <span class="sh-product-price">${{ number_format($product['price']) }}</span>
                @if ($original)
                    <span class="sh-product-original">{{ $original }}</span>
                @endif
            </div>
            <span class="sh-product-cta">Buy →</span>
        </div>
    </div>
</article>
