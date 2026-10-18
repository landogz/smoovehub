<x-shared.modal id="purchase-modal" title="🛒 Confirm Purchase" maxWidth="max-w-[440px]">
    <div id="pm-product-info" class="mb-4 rounded-xl bg-sh-s50 p-4"></div>

    <div class="sh-form-field">
        <label class="sh-form-label" for="pm-addr">Delivery address<span class="text-red-500">*</span></label>
        <input id="pm-addr" class="sh-form-input" type="text" placeholder="123 Main St, Dallas, TX 75201" data-modal-focus data-address-suggest data-suggest-full="pm-addr" data-suggest-zip="pm-zip">
    </div>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="sh-form-field mb-0">
            <label class="sh-form-label" for="pm-zip">Delivery ZIP<span class="text-red-500">*</span></label>
            <input id="pm-zip" class="sh-form-input" type="text" placeholder="75201" data-pm-recalc data-address-suggest data-suggest-zip="pm-zip">
        </div>
        <div class="sh-form-field mb-0">
            <label class="sh-form-label" for="pm-dtype">Delivery type</label>
            <select id="pm-dtype" class="sh-form-input" data-pm-recalc>
                <option value="CURBSIDE">Curbside (included)</option>
                <option value="IN_HOME">In-home (no extra charge)</option>
            </select>
        </div>
    </div>

    <div class="my-4 rounded-xl border-[1.5px] border-indigo-200 bg-indigo-50 p-4">
        <div class="mb-3 text-[13px] font-bold text-sh-navy">🧾 Order Summary</div>
        <div class="space-y-2 text-[13px]">
            <div class="flex justify-between"><span class="text-sh-s700">Base price</span><span id="pm-base-price" class="font-semibold"></span></div>
            <div id="pm-markup-row" class="flex justify-between"><span id="pm-markup-label" class="text-sh-s700">Marketplace markup</span><span id="pm-markup-amt" class="font-semibold text-sh-navy"></span></div>
            <div class="flex justify-between"><span class="text-sh-s700">Processing fee (3%)</span><span id="pm-proc-fee" class="font-semibold text-sh-s600"></span></div>
            <div class="flex justify-between"><span class="text-sh-s700">CRS delivery fee</span><span id="pm-del-fee" class="font-semibold"></span></div>
            <div id="pm-inhome-row" class="hidden flex justify-between"><span class="text-sh-s700">In-home placement</span><span class="font-semibold text-sh-green">Included — FREE</span></div>
            <div class="flex justify-between"><span class="text-sh-s700">Delivery coordination (3%)</span><span id="pm-del-pct" class="font-semibold text-sh-s600"></span></div>
            <hr class="border-indigo-200">
            <div class="flex justify-between text-base font-extrabold text-sh-s900"><span>Total</span><span id="pm-total" class="text-sh-navy"></span></div>
        </div>
    </div>

    <x-slot:footer>
        <p class="mb-3 text-[11px] text-sh-s400">🔒 Payment secured by Stripe · 30-day return on new items</p>
        <button type="button" class="sh-btn sh-btn-orange sh-btn-full sh-btn-lg" data-purchase-submit>Pay Now →</button>
    </x-slot:footer>
</x-shared.modal>
