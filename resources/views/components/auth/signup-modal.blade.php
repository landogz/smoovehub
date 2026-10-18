<x-shared.modal id="signup-modal" title="Create your SmooveHub account" maxWidth="max-w-[480px]" :auth-stack="true">
    <form id="signup-form" class="space-y-4" novalidate>
        <div class="sh-form-alert hidden" data-form-alert role="alert"></div>

        <div class="sh-form-field">
            <label class="sh-form-label" for="signup-name">
                Full name<span class="text-red-500">*</span>
            </label>
            <input
                id="signup-name"
                class="sh-form-input"
                type="text"
                name="name"
                placeholder="Jane Smith"
                autocomplete="name"
                required
                data-modal-focus
            >
            <p class="sh-form-error hidden" data-field-error="name"></p>
        </div>

        <div class="sh-form-field">
            <label class="sh-form-label" for="signup-email">
                Email address<span class="text-red-500">*</span>
            </label>
            <input
                id="signup-email"
                class="sh-form-input"
                type="email"
                name="email"
                placeholder="you@email.com"
                autocomplete="email"
                required
            >
            <p class="sh-form-error hidden" data-field-error="email"></p>
        </div>

        <div class="sh-form-field">
            <label class="sh-form-label" for="signup-phone">Phone (optional)</label>
            <input
                id="signup-phone"
                class="sh-form-input"
                type="tel"
                name="phone"
                placeholder="(555) 000-0000"
                autocomplete="tel"
            >
            <p class="sh-form-error hidden" data-field-error="phone"></p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div class="sh-form-field">
                <label class="sh-form-label" for="signup-password">
                    Password<span class="text-red-500">*</span>
                </label>
                <input
                    id="signup-password"
                    class="sh-form-input"
                    type="password"
                    name="password"
                    placeholder="Min 8 characters"
                    autocomplete="new-password"
                    required
                >
                <p class="sh-form-error hidden" data-field-error="password"></p>
            </div>

            <div class="sh-form-field">
                <label class="sh-form-label" for="signup-password-confirmation">
                    Confirm<span class="text-red-500">*</span>
                </label>
                <input
                    id="signup-password-confirmation"
                    class="sh-form-input"
                    type="password"
                    name="password_confirmation"
                    placeholder="Repeat password"
                    autocomplete="new-password"
                    required
                >
            </div>
        </div>

        <button type="submit" class="sh-btn sh-btn-blue sh-btn-full sh-btn-lg">
            Create Account
        </button>

        <p class="text-center text-[13px] text-sh-s500">
            Already have an account?
            <button
                type="button"
                class="font-semibold text-sh-blue hover:underline"
                data-switch-from="signup-modal"
                data-switch-modal="login-modal"
            >
                Sign in
            </button>
        </p>
    </form>
</x-shared.modal>
