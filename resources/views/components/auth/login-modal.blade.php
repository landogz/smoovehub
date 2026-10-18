<x-shared.modal id="login-modal" title="Sign in to SmooveHub" :auth-stack="true">
    <form id="login-form" class="space-y-4" novalidate>
        <div class="sh-form-alert hidden" data-form-alert role="alert"></div>

        <div class="sh-form-field">
            <label class="sh-form-label" for="login-email">
                Email address<span class="text-red-500">*</span>
            </label>
            <input
                id="login-email"
                class="sh-form-input"
                type="email"
                name="email"
                placeholder="you@email.com"
                autocomplete="email"
                required
                data-modal-focus
            >
            <p class="sh-form-error hidden" data-field-error="email"></p>
        </div>

        <div class="sh-form-field">
            <label class="sh-form-label" for="login-password">
                Password<span class="text-red-500">*</span>
            </label>
            <input
                id="login-password"
                class="sh-form-input"
                type="password"
                name="password"
                placeholder="••••••••"
                autocomplete="current-password"
                required
            >
            <p class="sh-form-error hidden" data-field-error="password"></p>
        </div>

        <button type="submit" class="sh-btn sh-btn-blue sh-btn-full sh-btn-lg">
            Sign In
        </button>

        <p class="text-center text-[13px] text-sh-s500">
            Don't have an account?
            <button
                type="button"
                class="font-semibold text-sh-blue hover:underline"
                data-switch-from="login-modal"
                data-switch-modal="signup-modal"
            >
                Sign up free
            </button>
        </p>

        <p class="text-center text-[13px] text-sh-s500">
            Selling?
            <button
                type="button"
                class="font-semibold text-sh-blue hover:underline"
                data-seller-signup
            >
                Create seller account
            </button>
        </p>

        <div class="rounded-[10px] border border-sh-s200 bg-sh-s50 px-3 py-2.5 text-[12px] leading-relaxed text-sh-s600">
            Shop (member): <strong>member@smoovehub.com</strong> / <strong>Member1234!</strong><br>
            Seller: <strong>demo@smoovehub.com</strong> / <strong>Demo1234!</strong>
        </div>
    </form>
</x-shared.modal>
