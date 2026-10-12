@extends('layouts.landing')

@section('title', 'Terms of Service — SmooveHub')

@section('content')
    <x-site.page-hero
        badge="📋 Updated June 2026"
        title="Terms of "
        highlight="Service"
        subtitle="The terms and conditions governing your use of the SmooveHub marketplace platform."
        gradient="navy"
    />

    <section class="sh-section">
        <div class="sh-container">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[220px_minmax(0,1fr)_260px]">
                <nav class="lg:sticky lg:top-24 lg:self-start" aria-label="Table of contents">
                    <h2 class="mb-4 text-xs font-semibold uppercase tracking-widest text-sh-s400">Contents</h2>
                    <ul class="space-y-2">
                        @foreach ($site['sections'] as $section)
                            <li>
                                <a href="#{{ $section['id'] }}" class="block rounded-lg px-3 py-2 text-[13px] font-medium text-sh-s600 transition-colors hover:bg-sh-blue-lt hover:text-sh-blue">
                                    {{ $section['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <div class="min-w-0 space-y-8">
                    @foreach ($site['sections'] as $section)
                        <section id="{{ $section['id'] }}" class="scroll-mt-24 rounded-2xl border-[1.5px] border-sh-s200 bg-white p-6 md:p-8">
                            <h2 class="mb-4 text-lg font-bold text-sh-s900">{{ $section['title'] }}</h2>
                            <ul class="space-y-2">
                                @foreach ($section['bullets'] as $bullet)
                                    <li class="flex gap-2.5 text-[13px] leading-relaxed text-sh-s600">
                                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-sh-blue" aria-hidden="true"></span>
                                        <span>{{ $bullet }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>

                <aside class="lg:sticky lg:top-24 lg:self-start">
                    <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-sh-s50 p-6">
                        <h2 class="mb-4 text-xs font-semibold uppercase tracking-widest text-sh-s400">Key Points</h2>
                        <ul class="space-y-3">
                            @foreach ($site['sidebar']['summary'] as $point)
                                <li class="flex gap-2 text-[13px] text-sh-s600">
                                    <span class="text-sh-green" aria-hidden="true">✓</span>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route($site['sidebar']['other_route']) }}" class="sh-btn sh-btn-outline sh-btn-full mt-6">
                            View {{ $site['sidebar']['other_label'] }} →
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
