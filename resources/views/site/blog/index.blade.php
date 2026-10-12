@extends('layouts.landing')

@section('title', 'Blog — SmooveHub')

@section('content')
    <x-site.page-hero
        badge="📰 Trends · Tips · Delivery · Commercial"
        title="SmooveHub "
        highlight="Blog"
        subtitle="Furniture trends, delivery guides, seller tips, and city spotlights — curated by our editorial team."
        gradient="navy"
    />

    <section class="sh-section">
        <div class="sh-container">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($site['posts'] as $post)
                    <article class="flex flex-col overflow-hidden rounded-2xl border-[1.5px] border-sh-s200 bg-white transition-all duration-200 hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-[0_2px_8px_rgba(15,23,42,0.07),0_8px_24px_rgba(15,23,42,0.05)]">
                        <div class="flex aspect-[16/9] items-center justify-center bg-sh-s100 text-5xl" aria-hidden="true">
                            {{ $post['icon'] }}
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <span class="sh-badge sh-badge-blue mb-3 w-fit">{{ $post['tag'] }}</span>
                            <h2 class="mb-2 line-clamp-2 text-base font-bold leading-snug text-sh-s900">{{ $post['title'] }}</h2>
                            <p class="mb-4 flex-1 line-clamp-3 text-[13px] leading-relaxed text-sh-s500">{{ $post['excerpt'] }}</p>
                            <div class="flex items-center justify-between gap-3 border-t border-sh-s100 pt-4">
                                <span class="text-xs text-sh-s400">{{ $post['author'] }}</span>
                                <a href="{{ route('site.blog') }}" class="text-xs font-semibold text-sh-blue">Read →</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
