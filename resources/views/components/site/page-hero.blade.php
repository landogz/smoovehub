@props([
    'badge' => null,
    'title',
    'highlight' => null,
    'subtitle' => null,
    'gradient' => 'navy',
])

@php
    $gradientClass = match ($gradient) {
        'orange' => 'from-orange-600 via-sh-orange to-orange-500',
        'purple' => 'from-violet-800 via-purple-700 to-violet-600',
        default => 'from-sh-navy via-sh-blue to-sh-blue-mid',
    };
@endphp

<section @class(['relative overflow-hidden bg-gradient-to-br py-12 text-white md:py-16 lg:py-20', $gradientClass])>
    <div class="pointer-events-none absolute inset-0 bg-[repeating-linear-gradient(-55deg,transparent,transparent_20px,rgba(255,255,255,0.025)_20px,rgba(255,255,255,0.025)_21px)]" aria-hidden="true"></div>
    <div class="sh-container relative z-[1]">
        @if ($badge)
            <div class="sh-hero-badge">{{ $badge }}</div>
        @endif
        <h1 class="sh-display text-[clamp(32px,6vw,56px)] font-black leading-tight tracking-tight">
            {{ $title }}
            @if ($highlight)
                <span class="text-sh-orange">{{ $highlight }}</span>
            @endif
        </h1>
        @if ($subtitle)
            <p class="mt-4 max-w-2xl text-base leading-relaxed text-white/70 md:text-[17px]">{{ $subtitle }}</p>
        @endif
        @if (isset($slot) && ! $slot->isEmpty())
            <div class="mt-6">{{ $slot }}</div>
        @endif
    </div>
</section>
