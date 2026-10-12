<div id="dolores-widget" class="sh-dolores-root" aria-live="polite">
    <button type="button" id="dolores-fab" class="sh-dolores-fab" aria-label="Open Ms. Dolores AI assistant" aria-expanded="false" aria-controls="dolores-win">
        <span aria-hidden="true">🤖</span>
        <span class="sh-dolores-pulse" aria-hidden="true"></span>
    </button>

    <div id="dolores-win" class="sh-dolores-win" role="dialog" aria-label="Ms. Dolores chat">
        <div class="sh-dolores-head">
            <div class="sh-dolores-avatar-sm" aria-hidden="true">✨</div>
            <div class="min-w-0 flex-1">
                <div class="font-bold text-sm">Ms. Dolores</div>
                <div class="mt-0.5 flex items-center gap-1.5 text-[10px] text-white/65">
                    <span class="sh-dolores-status-dot" aria-hidden="true"></span>
                    AI Furniture Assistant · Online
                </div>
            </div>
            <button type="button" id="dolores-close" class="sh-modal-close text-white/80 hover:bg-white/10 hover:text-white" aria-label="Close chat">✕</button>
        </div>

        <div id="dolores-msgs" class="sh-dolores-msgs">
            <div class="sh-dolores-msg">
                <div class="sh-dolores-bubble sh-dolores-bubble-bot">
                    Hi! I'm Ms. Dolores 👋 Ask me about furniture, delivery quotes, or finding sellers near you.
                </div>
            </div>
        </div>

        <div class="sh-dolores-inp-area">
            <div class="sh-dolores-quick">
                <button type="button" class="sh-dolores-quick-btn" data-dolores-quick="Find sofas near me">Find sofas</button>
                <button type="button" class="sh-dolores-quick-btn" data-dolores-quick="Delivery quote">Delivery quote</button>
                <button type="button" class="sh-dolores-quick-btn" data-dolores-quick="Sell on SmooveHub">Sell furniture</button>
            </div>
            <div class="sh-dolores-inp-row">
                <input id="dolores-inp" type="text" class="sh-dolores-inp" placeholder="Ask Ms. Dolores anything…" autocomplete="off">
                <button type="button" id="dolores-send" class="sh-dolores-send" aria-label="Send message">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </div>
            <p class="sh-dolores-api-notice">Prototype assistant · Connect OpenAI in production</p>
        </div>
    </div>
</div>
