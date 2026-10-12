@extends('layouts.landing')

@section('title', 'Contact Us — SmooveHub')

@section('content')
    <div id="contact-page">
        {{-- Hero --}}
        <section class="sh-contact-hero">
            <div class="sh-hero-pattern" aria-hidden="true"></div>
            <div class="sh-contact-hero-glow" aria-hidden="true"></div>
            <div class="sh-container relative z-[1]">
                <div class="sh-contact-badge">{{ $site['hero_badge'] }}</div>
                <h1 class="sh-contact-title">
                    Contact<br>
                    <span class="text-sh-orange">SmooveHub</span>
                </h1>
                <p class="sh-contact-subtitle">{{ $site['hero_subtitle'] }}</p>
                <div class="flex flex-wrap gap-2.5">
                    <a href="{{ $site['phone_href'] }}" class="sh-contact-hero-pill">📞 Call {{ $site['phone'] }}</a>
                    <a href="mailto:{{ $site['support_email'] }}" class="sh-contact-hero-pill sh-contact-hero-pill-orange">✉️ Email us</a>
                    <a href="mailto:{{ $site['crs_email'] }}" class="sh-contact-hero-pill">🚛 CRS Delivery Line</a>
                </div>
            </div>
        </section>

        {{-- Hours strip --}}
        <div class="sh-contact-hours-strip">
            <div class="sh-container flex flex-wrap items-center justify-center gap-7 text-center text-[13px] font-semibold text-white">
                @foreach ($site['hours_strip'] as $index => $item)
                    @if ($index > 0)
                        <span class="hidden opacity-50 sm:inline">|</span>
                    @endif
                    <span>{{ $item }}</span>
                @endforeach
            </div>
        </div>

        {{-- Main body --}}
        <section class="sh-contact-body">
            <div class="sh-container">
                <div class="sh-contact-grid">
                    <div>
                        <h2 class="sh-contact-section-title">Get in Touch</h2>
                        <div class="mb-12 grid grid-cols-1 gap-4 md:grid-cols-2">
                            @foreach ($site['contact_cards'] as $card)
                                <div @class([
                                    'sh-contact-card',
                                    'sh-contact-card-blue' => ($card['tone'] ?? '') === 'blue',
                                    'sh-contact-card-orange' => ($card['tone'] ?? '') === 'orange',
                                    'sh-contact-card-green' => ($card['tone'] ?? '') === 'green',
                                    'sh-contact-card-purple' => ($card['tone'] ?? '') === 'purple',
                                ])>
                                    <div @class([
                                        'sh-contact-card-icon',
                                        'sh-contact-card-icon-blue' => ($card['tone'] ?? '') === 'blue',
                                        'sh-contact-card-icon-orange' => ($card['tone'] ?? '') === 'orange',
                                        'sh-contact-card-icon-green' => ($card['tone'] ?? '') === 'green',
                                        'sh-contact-card-icon-purple' => ($card['tone'] ?? '') === 'purple',
                                    ]) aria-hidden="true">{{ $card['icon'] }}</div>
                                    <div class="mb-1.5 text-base font-bold text-sh-s900">{{ $card['title'] }}</div>
                                    <p class="mb-3.5 text-[13px] leading-relaxed text-sh-s500">{{ $card['description'] }}</p>
                                    <div @class([
                                        'mb-0.5 break-all font-bold',
                                        'sh-display text-2xl tracking-wide text-sh-blue' => ($card['tone'] ?? '') === 'blue',
                                        'text-sm text-sh-orange-dk' => ($card['tone'] ?? '') === 'orange',
                                        'text-[13px] text-green-600' => ($card['tone'] ?? '') === 'green',
                                        'text-[13px] text-violet-600' => ($card['tone'] ?? '') === 'purple',
                                    ])>{{ $card['primary'] }}</div>
                                    <div class="mb-4 text-xs text-sh-s400">{{ $card['secondary'] }}</div>
                                    <a
                                        href="{{ $card['cta_href'] }}"
                                        @if (str_starts_with($card['cta_href'], 'http')) target="_blank" rel="noopener" @endif
                                        @class([
                                            'inline-flex items-center gap-1.5 rounded-[10px] px-[18px] py-2.5 text-[13px] font-semibold text-white no-underline transition-colors',
                                            'bg-sh-blue hover:bg-sh-blue-mid' => ($card['tone'] ?? '') === 'blue',
                                            'bg-sh-orange hover:bg-sh-orange-dk' => ($card['tone'] ?? '') === 'orange',
                                            'bg-green-600 hover:bg-green-700' => ($card['tone'] ?? '') === 'green',
                                            'bg-violet-600 hover:bg-violet-700' => ($card['tone'] ?? '') === 'purple',
                                        ])
                                    >{{ $card['cta_label'] }}</a>
                                </div>
                            @endforeach
                        </div>

                        {{-- Contact form --}}
                        <div class="sh-contact-form-card">
                            <div class="mb-1.5 flex items-center gap-3.5">
                                <div class="sh-contact-form-icon" aria-hidden="true">💬</div>
                                <h2 class="sh-contact-form-title">Send Us a Message</h2>
                            </div>
                            <p class="mb-6 ml-0 border-b border-sh-s100 pb-5 pl-0 text-[13px] text-sh-s500 sm:ml-[58px]">
                                Fill out the form below and we'll respond within 2 business hours.
                            </p>

                            <div id="contact-success" class="sh-contact-success hidden">
                                <div class="mb-3 text-[44px]" aria-hidden="true">✅</div>
                                <div class="sh-display mb-1.5 text-2xl font-extrabold text-sh-s900">Message Sent!</div>
                                <p class="text-sm leading-relaxed text-sh-s600">
                                    We'll get back to you at
                                    <strong id="contact-confirm-email" class="text-green-600"></strong>
                                    within 2 business hours.<br>
                                    For urgent issues call
                                    <a href="{{ $site['phone_href'] }}" class="font-semibold text-sh-blue">{{ $site['phone'] }}</a>.
                                </p>
                                <button type="button" id="contact-reset-btn" class="sh-btn sh-btn-blue mt-4">Send another message</button>
                            </div>

                            <div id="contact-form">
                                <div class="mb-3.5 grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                                    <div>
                                        <label class="sh-contact-label" for="cf-fn">First name <span class="text-red-600">*</span></label>
                                        <input id="cf-fn" class="sh-contact-input" type="text" placeholder="Jane" autocomplete="given-name">
                                    </div>
                                    <div>
                                        <label class="sh-contact-label" for="cf-ln">Last name <span class="text-red-600">*</span></label>
                                        <input id="cf-ln" class="sh-contact-input" type="text" placeholder="Smith" autocomplete="family-name">
                                    </div>
                                </div>
                                <div class="mb-3.5 grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                                    <div>
                                        <label class="sh-contact-label" for="cf-email">Email address <span class="text-red-600">*</span></label>
                                        <input id="cf-email" class="sh-contact-input" type="email" placeholder="jane@email.com" autocomplete="email">
                                    </div>
                                    <div>
                                        <label class="sh-contact-label" for="cf-phone">Phone number</label>
                                        <input id="cf-phone" class="sh-contact-input" type="tel" placeholder="(555) 000-0000" autocomplete="tel">
                                    </div>
                                </div>
                                <div class="mb-3.5">
                                    <label class="sh-contact-label" for="cf-role">I am a <span class="text-red-600">*</span></label>
                                    <select id="cf-role" class="sh-contact-input">
                                        @foreach ($site['role_options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3.5">
                                    <label class="sh-contact-label" for="cf-subject">Subject <span class="text-red-600">*</span></label>
                                    <input id="cf-subject" class="sh-contact-input" type="text" placeholder="e.g. Need help with order SH-4821">
                                </div>
                                <div class="mb-3.5">
                                    <label class="sh-contact-label" for="cf-order">Order number (if applicable)</label>
                                    <input id="cf-order" class="sh-contact-input max-w-[220px]" type="text" placeholder="e.g. SH-4821">
                                </div>
                                <div class="mb-5">
                                    <label class="sh-contact-label" for="cf-msg">Message <span class="text-red-600">*</span></label>
                                    <textarea id="cf-msg" class="sh-contact-input min-h-[120px] resize-y" rows="5" placeholder="Please describe your question or issue in as much detail as possible. Include your order number, delivery address, or any relevant information so we can help faster."></textarea>
                                </div>
                                <div id="cf-err" class="sh-contact-error hidden" role="alert"></div>
                                <button type="button" id="contact-submit-btn" class="sh-contact-submit-btn">Send Message →</button>
                                <p class="mt-2.5 text-center text-xs text-sh-s400">
                                    Your information is never sold. See our
                                    <a href="{{ route('site.privacy') }}" class="text-sh-blue underline">Privacy Policy</a>.
                                </p>
                            </div>
                        </div>

                        {{-- FAQ --}}
                        <div class="mt-[60px]">
                            <div class="mb-8 text-center">
                                <h2 class="sh-contact-faq-title">Frequently Asked Questions</h2>
                                <p class="text-sm text-sh-s500">Can't find what you need? Contact us directly above.</p>
                            </div>
                            <div id="faq-list" class="mx-auto flex max-w-[760px] flex-col gap-2.5">
                                @foreach ($site['faqs'] as $index => $faq)
                                    <div class="sh-contact-faq-item">
                                        <button
                                            type="button"
                                            class="sh-contact-faq-toggle"
                                            data-faq-toggle="{{ $index }}"
                                            aria-expanded="false"
                                            aria-controls="faq-body-{{ $index }}"
                                        >
                                            <span>{{ $faq['q'] }}</span>
                                            <span id="faq-icon-{{ $index }}" class="sh-contact-faq-icon text-sh-s400" data-faq-icon>+</span>
                                        </button>
                                        <div id="faq-body-{{ $index }}" class="sh-contact-faq-body" data-faq-body>
                                            <div class="border-t border-sh-s100 px-5 pb-[18px] pt-3.5 text-sm leading-relaxed text-sh-s600">
                                                {{ $faq['a'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <aside class="sh-contact-sidebar">
                        <div class="sh-contact-primary-card">
                            <div class="mb-[18px] text-[11px] font-bold uppercase tracking-[1.2px] text-white/45">Primary Contact</div>
                            <div class="flex flex-col gap-3">
                                <a href="{{ $site['phone_href'] }}" class="sh-contact-primary-link">
                                    <div class="sh-contact-primary-icon sh-contact-primary-icon-orange" aria-hidden="true">📞</div>
                                    <div>
                                        <div class="text-[10px] uppercase tracking-[0.8px] text-white/40">Phone</div>
                                        <div class="sh-display text-xl font-black tracking-wide text-white">{{ $site['phone'] }}</div>
                                        <div class="text-[11px] text-white/45">Mon–Sat · 8AM–8PM CT</div>
                                    </div>
                                </a>
                                <a href="mailto:{{ $site['support_email'] }}" class="sh-contact-primary-link">
                                    <div class="sh-contact-primary-icon" aria-hidden="true">✉️</div>
                                    <div class="min-w-0">
                                        <div class="text-[10px] uppercase tracking-[0.8px] text-white/40">Support Email</div>
                                        <div class="break-all text-[13px] font-semibold text-blue-300">{{ $site['support_email'] }}</div>
                                        <div class="text-[11px] text-white/40">Response within 2 hours</div>
                                    </div>
                                </a>
                                <a href="mailto:{{ $site['crs_email'] }}" class="sh-contact-primary-link">
                                    <div class="sh-contact-primary-icon sh-contact-primary-icon-green" aria-hidden="true">🚛</div>
                                    <div class="min-w-0">
                                        <div class="text-[10px] uppercase tracking-[0.8px] text-white/40">CRS Delivery</div>
                                        <div class="break-all text-xs font-semibold text-green-300">{{ $site['crs_email'] }}</div>
                                        <div class="mt-px text-[11px] text-white/35">{{ $site['crs_email_alt'] }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="sh-contact-side-card">
                            <div class="sh-contact-side-title">Business Hours</div>
                            <div class="flex flex-col gap-2.5">
                                @foreach ($site['business_hours'] as $hours)
                                    <div class="flex items-center justify-between text-[13px]">
                                        <span class="text-sh-s700">{{ $hours['label'] }}</span>
                                        <span @class([
                                            'rounded-full px-2.5 py-0.5 text-xs font-bold',
                                            'bg-green-50 text-green-600' => ($hours['tone'] ?? '') === 'open',
                                            'bg-red-50 text-red-600' => ($hours['tone'] ?? '') === 'closed',
                                        ])>{{ $hours['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-3.5 border-t border-sh-s100 pt-3 text-xs leading-relaxed text-sh-s500">
                                All times in <strong>Central Time (CT)</strong>. Emergency delivery issues can be emailed 24/7.
                            </p>
                        </div>

                        <div class="sh-contact-side-card">
                            <div class="sh-contact-side-title">Quick Actions</div>
                            <div class="flex flex-col gap-1.5">
                                @foreach ($site['quick_actions'] as $action)
                                    <a
                                        href="{{ route($action['route']) }}"
                                        @class([
                                            'sh-contact-quick-action',
                                            'sh-contact-quick-action-orange' => ($action['tone'] ?? '') === 'orange',
                                        ])
                                    >
                                        <span aria-hidden="true">{{ $action['icon'] }}</span>
                                        {{ $action['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="sh-contact-response-badge">
                            <div class="mb-2.5 text-[32px]" aria-hidden="true">⚡</div>
                            <div class="mb-1 text-base font-bold text-green-600">Fast Response</div>
                            <p class="text-[13px] leading-relaxed text-sh-s600">
                                Average email response under <strong>2 hours</strong> during business hours. Phone support is immediate.
                            </p>
                        </div>

                        <div class="sh-contact-crs-promo">
                            <div class="mb-2.5 text-[22px]" aria-hidden="true">🚛</div>
                            <div class="mb-1.5 text-[15px] font-bold text-white">Delivery by CRS</div>
                            <p class="mb-3.5 text-xs leading-relaxed text-white/50">
                                Every SmooveHub delivery is coordinated by Competitive Relocation Service — real-time tracking, live ETAs, and SMS updates.
                            </p>
                            <a href="{{ $site['crs_promo_url'] }}" target="_blank" rel="noopener" class="block rounded-lg bg-sh-orange/85 py-2 text-center text-xs font-semibold text-white no-underline hover:bg-sh-orange">
                                Visit CRS →
                            </a>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </div>
@endsection
