<x-shared.modal id="seller-signup-modal" title="🏪 Create Your Seller Account" maxWidth="max-w-[480px]">
    <div class="mb-5 h-1.5 overflow-hidden rounded-full bg-sh-s100">
        <div id="ss-prog" class="h-full rounded-full bg-sh-blue transition-all duration-300" style="width:33%"></div>
    </div>

    <div class="mb-6 flex gap-2 overflow-x-auto">
        <div id="ss-t0" class="sh-step-tab sh-step-tab-active shrink-0"><span class="text-[10px] font-bold opacity-60">01</span> Account</div>
        <div id="ss-t1" class="sh-step-tab shrink-0"><span class="text-[10px] font-bold opacity-60">02</span> Business</div>
        <div id="ss-t2" class="sh-step-tab shrink-0"><span class="text-[10px] font-bold opacity-60">03</span> Finish</div>
    </div>

    <div id="ss-s0" class="sh-step-panel" data-ss-step="0">
        <div id="ss-err0" class="sh-form-alert mb-4 hidden" role="alert"></div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="ss-fn">First Name<span class="text-red-500">*</span></label>
                <input id="ss-fn" class="sh-form-input" type="text" placeholder="Jane" data-modal-focus>
            </div>
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="ss-ln">Last Name<span class="text-red-500">*</span></label>
                <input id="ss-ln" class="sh-form-input" type="text" placeholder="Smith">
            </div>
        </div>
        <div class="sh-form-field">
            <label class="sh-form-label" for="ss-email">Email Address<span class="text-red-500">*</span></label>
            <input id="ss-email" class="sh-form-input" type="email" placeholder="jane@mystore.com">
            <p class="mt-1 text-[11px] text-sh-s400">Login credentials will be sent to this email</p>
        </div>
        <div class="sh-form-field">
            <label class="sh-form-label" for="ss-phone">Phone Number<span class="text-red-500">*</span></label>
            <input id="ss-phone" class="sh-form-input" type="tel" placeholder="(555) 000-0000">
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="ss-pw">Password<span class="text-red-500">*</span></label>
                <input id="ss-pw" class="sh-form-input" type="password" placeholder="Min 8 characters">
            </div>
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="ss-pw2">Confirm Password<span class="text-red-500">*</span></label>
                <input id="ss-pw2" class="sh-form-input" type="password" placeholder="Repeat password">
            </div>
        </div>
        <button type="button" class="sh-btn sh-btn-blue sh-btn-full sh-btn-lg mt-2" data-ss-next="0">Continue →</button>
    </div>

    <div id="ss-s1" class="sh-step-panel hidden" data-ss-step="1">
        <div id="ss-err1" class="sh-form-alert mb-4 hidden" role="alert"></div>
        <div class="sh-form-field">
            <label class="sh-form-label" for="ss-store">Store / Business Name<span class="text-red-500">*</span></label>
            <input id="ss-store" class="sh-form-input" type="text" placeholder="Jane's Furniture Co.">
        </div>
        <div class="sh-form-field">
            <label class="sh-form-label" for="ss-btype">Business Type<span class="text-red-500">*</span></label>
            <select id="ss-btype" class="sh-form-input">
                <option value="">Select type…</option>
                <option>Furniture retailer</option>
                <option>Thrift / consignment store</option>
                <option>Wholesale / distributor</option>
                <option>Individual seller</option>
                <option>Interior designer</option>
            </select>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="ss-cat">Main Category<span class="text-red-500">*</span></label>
                <select id="ss-cat" class="sh-form-input">
                    <option value="">Select…</option>
                    <option>Commercial</option>
                    <option>Residential</option>
                    <option>Appliances</option>
                    <option>Mixed</option>
                </select>
            </div>
            <div class="sh-form-field mb-0">
                <label class="sh-form-label" for="ss-ein">Tax ID / EIN (optional)</label>
                <input id="ss-ein" class="sh-form-input" type="text" placeholder="12-3456789">
            </div>
        </div>
        <div class="sh-form-field">
            <label class="sh-form-label" for="ss-desc">Store Description</label>
            <textarea id="ss-desc" class="sh-form-input resize-none" rows="2" placeholder="Tell buyers about your store…"></textarea>
        </div>
        <div class="sh-form-field">
            <label class="sh-form-label" for="ss-size">How many products do you plan to list?</label>
            <select id="ss-size" class="sh-form-input">
                <option>1–10 products</option>
                <option>11–50 products</option>
                <option>51–200 products</option>
                <option>200+ products</option>
            </select>
        </div>
        <label class="mb-4 flex cursor-pointer items-start gap-2.5 text-[13px] text-sh-s700">
            <input id="ss-terms" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-sh-s300 text-sh-blue">
            <span>I agree to the <a href="{{ route('site.terms') }}" class="font-semibold text-sh-blue underline">SmooveHub Seller Terms of Service</a></span>
        </label>
        <div class="flex gap-3">
            <button type="button" class="sh-btn sh-btn-outline shrink-0" data-ss-prev>← Back</button>
            <button type="button" class="sh-btn sh-btn-blue min-w-0 flex-1" data-ss-next="1">Create Account →</button>
        </div>
    </div>

    <div id="ss-s2" class="sh-step-panel hidden text-center" data-ss-step="2">
        <div class="py-4">
            <div class="mb-4 text-5xl" aria-hidden="true">🎉</div>
            <h4 class="mb-2 text-xl font-bold text-sh-s900">Account Created!</h4>
            <p class="mb-4 text-[14px] leading-relaxed text-sh-s500">
                Welcome to SmooveHub! Credentials sent to <strong id="ss-confirm-email">your email</strong>.
            </p>
            <div class="mb-6 rounded-xl border border-sh-orange/30 bg-sh-orange-lt px-4 py-3 text-left">
                <div class="text-[10px] font-bold uppercase tracking-widest text-sh-orange">Your Store URL</div>
                <div id="ss-store-url-display" class="mt-1 break-all font-mono text-[13px] font-semibold text-sh-orange">smoovehub.com/store/my-store</div>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="button" class="sh-btn sh-btn-blue flex-1" data-ss-go-dashboard>Seller Dashboard →</button>
                <button type="button" class="sh-btn sh-btn-outline flex-1" data-modal-close="seller-signup-modal">Close</button>
            </div>
        </div>
    </div>
</x-shared.modal>
