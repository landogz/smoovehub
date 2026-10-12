<x-shared.modal id="add-product-modal" title="+ Add New Product" maxWidth="max-w-[480px]">
    <div id="ap-err" class="sh-form-alert mb-4 hidden" role="alert"></div>

    <div class="sh-form-field">
        <label class="sh-form-label" for="ap-name">Product Name<span class="text-red-500">*</span></label>
        <input id="ap-name" class="sh-form-input" type="text" placeholder="Japandi Oak Dining Table" data-modal-focus>
    </div>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="sh-form-field mb-0">
            <label class="sh-form-label" for="ap-price">Base Price ($)<span class="text-red-500">*</span></label>
            <input id="ap-price" class="sh-form-input" type="number" placeholder="1000" min="1">
        </div>
        <div class="sh-form-field mb-0">
            <label class="sh-form-label" for="ap-cond">Condition<span class="text-red-500">*</span></label>
            <select id="ap-cond" class="sh-form-input">
                <option value="BRAND_NEW">Brand New</option>
                <option value="THRIFT">Thrift</option>
                <option value="FLOOR_MODEL">Floor Model</option>
                <option value="REFURBISHED">Refurbished</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="sh-form-field mb-0">
            <label class="sh-form-label" for="ap-cat">Category<span class="text-red-500">*</span></label>
            <select id="ap-cat" class="sh-form-input">
                <option value="COMMERCIAL">Commercial</option>
                <option value="RESIDENTIAL">Residential</option>
                <option value="APPLIANCES">Appliances</option>
            </select>
        </div>
        <div class="sh-form-field mb-0">
            <label class="sh-form-label" for="ap-qty">Stock Quantity<span class="text-red-500">*</span></label>
            <input id="ap-qty" class="sh-form-input" type="number" placeholder="1" min="1" value="1">
        </div>
    </div>
    <div class="sh-form-field">
        <label class="sh-form-label" for="ap-desc">Description</label>
        <textarea id="ap-desc" class="sh-form-input resize-none" rows="3" placeholder="Describe your product…"></textarea>
    </div>
    <div class="sh-form-field">
        <label class="sh-form-label">Product Photo</label>
        <button type="button" class="w-full rounded-xl border-2 border-dashed border-sh-s200 bg-sh-s50 px-4 py-8 text-center transition-colors hover:border-sh-blue hover:bg-sh-blue-lt" data-photo-upload>
            <div class="mb-2 text-3xl" aria-hidden="true">📸</div>
            <p class="text-[13px] text-sh-s500">Click to upload photos</p>
            <small class="text-[11px] text-sh-s400">JPG, PNG — up to 10MB each</small>
        </button>
    </div>
    <div class="sh-form-field">
        <span class="sh-form-label">Available for same-day delivery?</span>
        <div class="mt-2 flex gap-4">
            <label class="flex cursor-pointer items-center gap-2 text-[13px] text-sh-s700">
                <input type="radio" name="same-day" value="yes" class="h-4 w-4 text-sh-blue"> Yes
            </label>
            <label class="flex cursor-pointer items-center gap-2 text-[13px] text-sh-s700">
                <input type="radio" name="same-day" value="no" class="h-4 w-4 text-sh-blue" checked> No
            </label>
        </div>
    </div>
    <div class="flex gap-3">
        <button type="button" class="sh-btn sh-btn-outline shrink-0" data-modal-close="add-product-modal">Cancel</button>
        <button type="button" class="sh-btn sh-btn-blue min-w-0 flex-1" data-add-product-submit>List Product →</button>
    </div>
</x-shared.modal>
