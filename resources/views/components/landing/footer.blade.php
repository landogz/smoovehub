<footer class="sh-footer">
    <div class="sh-container sh-footer-top">
        <div class="sh-footer-grid">
            <div>
                <img
                    src="{{ asset('images/landing/logo-footer.png') }}"
                    alt="SmooveHub"
                    class="mb-3 h-[68px] w-auto object-contain"
                >
                <p class="text-[13px] leading-relaxed text-white/40">
                    AI-powered multi-vendor furniture marketplace. CRM-coordinated delivery by CRS.
                </p>
                <p class="mt-2.5 text-xs italic text-white/25">Your Furniture. Our Promise.</p>
            </div>

            @foreach ($footerColumns as $column)
                <div>
                    <h4 class="sh-footer-col-title">{{ $column['title'] }}</h4>
                    @foreach ($column['links'] as $link)
                        <a
                            href="{{ isset($link['route']) ? route($link['route'], $link['params'] ?? []) : ($link['href'] ?? '#') }}"
                            class="sh-footer-link"
                        >{{ $link['label'] }}</a>
                    @endforeach
                </div>
            @endforeach

            <div>
                <h4 class="sh-footer-col-title">Contact Us</h4>
                <div class="flex flex-col gap-2.5">
                    <a href="tel:8773852919" class="sh-footer-contact-card">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-sh-blue to-sh-blue-mid text-[15px]">📞</div>
                        <div>
                            <div class="text-[11px] uppercase tracking-wider text-white/35">Phone Support</div>
                            <div class="sh-display text-[15px] font-extrabold tracking-wide text-white">(877) 385-2919</div>
                            <div class="text-[10px] text-white/35">Mon–Sat · 8AM–8PM CT</div>
                        </div>
                    </a>
                    <a href="mailto:support@smoovehub.com" class="sh-footer-contact-card hover:border-sh-orange/40 hover:bg-sh-orange/25">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-sh-orange to-orange-600 text-[15px]">✉️</div>
                        <div class="min-w-0">
                            <div class="text-[11px] uppercase tracking-wider text-white/35">Email Support</div>
                            <div class="break-all text-xs font-semibold text-orange-200">support@smoovehub.com</div>
                            <div class="text-[10px] text-white/35">Reply within 2 hours</div>
                        </div>
                    </a>
                    <a href="mailto:love@competitiverelocation.com" class="sh-footer-contact-card hover:border-green-500/40 hover:bg-green-900/20">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-sh-green to-green-700 text-[15px]">🚛</div>
                        <div class="min-w-0">
                            <div class="text-[11px] uppercase tracking-wider text-white/35">CRS Delivery</div>
                            <div class="break-all text-[11px] font-semibold text-green-300">love@competitiverelocation.com</div>
                            <div class="text-[10px] text-white/35">lamar@competitiverelocation.com</div>
                        </div>
                    </a>
                    <a
                        href="https://competitiverelocationcrm.com/auth/login"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="sh-footer-contact-card hover:border-violet-500/40 hover:bg-violet-900/25"
                    >
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-violet-600 to-violet-800 text-[15px]">🧑‍✈️</div>
                        <div class="min-w-0">
                            <div class="text-[11px] uppercase tracking-wider text-white/35">Driver Portal</div>
                            <div class="text-[11px] font-semibold text-violet-200">competitiverelocationcrm.com</div>
                            <div class="text-[10px] text-white/35">Login with approved credentials</div>
                        </div>
                    </a>
                    <a href="#contact" class="sh-btn sh-btn-sm w-full border border-sh-orange/35 bg-sh-orange/15 text-center text-orange-200 hover:bg-sh-orange/30">
                        View Full Contact Page →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="sh-container py-4">
        <div class="flex flex-col gap-3 text-xs text-white/30 sm:flex-row sm:items-center sm:justify-between">
            <span>
                © {{ date('Y') }} SmooveHub Inc. Powered by Competitive Relocation Service.
                · <a href="#privacy" class="sh-footer-link inline">Privacy Policy</a>
                · <a href="#terms" class="sh-footer-link inline">Terms of Service</a>
            </span>
            <div class="flex flex-wrap gap-4">
                <span>🔒 Stripe</span>
                <span>📦 CRS</span>
                <span>🤖 AI-powered</span>
            </div>
        </div>
    </div>
</footer>
