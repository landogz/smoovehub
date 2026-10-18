@extends('layouts.landing')

@section('title', 'My Account — SmooveHub')

@section('content')
    <div id="member-account" class="bg-sh-s50">
        <div class="sh-container py-6 md:py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
                <aside class="w-full shrink-0 rounded-2xl border border-sh-s200 bg-white p-4 lg:w-64">
                    <div class="mb-4 flex items-center gap-3 border-b border-sh-s100 pb-4">
                        <div id="member-sidebar-avatar" class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full bg-sh-blue text-sm font-bold text-white">M</div>
                        <div class="min-w-0">
                            <div id="member-sidebar-username" class="truncate text-sm font-bold text-sh-s900">Member</div>
                            <button type="button" class="text-xs font-medium text-sh-blue" data-member-tab="profile">Edit Profile</button>
                        </div>
                    </div>

                    <nav class="flex gap-1 overflow-x-auto lg:flex-col lg:overflow-visible" aria-label="Member account">
                        <p class="hidden px-3 pb-1 text-[11px] font-bold uppercase tracking-widest text-sh-s400 lg:block">My Account</p>
                        <button type="button" class="member-nav-btn is-active" data-member-tab="profile">Profile</button>
                        <button type="button" class="member-nav-btn" data-member-tab="payments">Banks &amp; Cards</button>
                        <button type="button" class="member-nav-btn" data-member-tab="addresses">Addresses</button>
                        <button type="button" class="member-nav-btn" data-member-tab="password">Change Password</button>
                        <button type="button" class="member-nav-btn" data-member-tab="privacy">Privacy Settings</button>
                        <button type="button" class="member-nav-btn" data-member-tab="notif-settings">Notification Settings</button>
                        <button type="button" class="member-nav-btn" data-member-tab="order-settings">Order Settings</button>

                        <p class="mt-3 hidden px-3 pb-1 text-[11px] font-bold uppercase tracking-widest text-sh-s400 lg:block">Shopping</p>
                        <button type="button" class="member-nav-btn" data-member-tab="purchases">My Purchase</button>
                        <button type="button" class="member-nav-btn" data-member-tab="notifications">Notifications</button>
                        <button type="button" class="member-nav-btn" data-member-tab="vouchers">My Vouchers</button>
                    </nav>
                </aside>

                <div class="min-w-0 flex-1 rounded-2xl border border-sh-s200 bg-white p-5 md:p-8">
                    <section data-member-panel="profile">
                        <h1 class="text-xl font-bold text-sh-s900">My Profile</h1>
                        <p class="mt-1 text-[13px] text-sh-s500">Manage and protect your account</p>
                        <form id="member-profile-form" class="mt-6 grid gap-8 lg:grid-cols-[1fr_220px]" novalidate>
                            <div class="space-y-4">
                                <div class="sh-form-field">
                                    <label class="sh-form-label" for="member-username">Username</label>
                                    <input id="member-username" name="username" class="sh-form-input" required>
                                </div>
                                <div class="sh-form-field">
                                    <label class="sh-form-label" for="member-name">Name</label>
                                    <input id="member-name" name="name" class="sh-form-input" required>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-sh-s400">Email</div>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-sh-s800">
                                        <span id="member-email-masked"></span>
                                        <button type="button" class="font-semibold text-sh-blue" data-toggle-email>Change</button>
                                    </div>
                                    <input id="member-email" name="email" type="email" class="sh-form-input mt-2 hidden">
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-sh-s400">Phone Number</div>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-sh-s800">
                                        <span id="member-phone-masked"></span>
                                        <button type="button" class="font-semibold text-sh-blue" data-toggle-phone>Change</button>
                                    </div>
                                    <input id="member-phone" name="phone" class="sh-form-input mt-2 hidden">
                                </div>
                                <fieldset>
                                    <legend class="sh-form-label">Gender</legend>
                                    <div class="mt-2 flex flex-wrap gap-4 text-sm">
                                        <label class="flex items-center gap-2"><input type="radio" name="gender" value="male"> Male</label>
                                        <label class="flex items-center gap-2"><input type="radio" name="gender" value="female"> Female</label>
                                        <label class="flex items-center gap-2"><input type="radio" name="gender" value="other"> Other</label>
                                    </div>
                                </fieldset>
                                <div class="sh-form-field">
                                    <label class="sh-form-label" for="member-dob">Date of birth</label>
                                    <input id="member-dob" name="date_of_birth" type="date" class="sh-form-input">
                                </div>
                                <button type="submit" class="sh-btn sh-btn-blue">Save</button>
                            </div>
                            <div class="text-center">
                                <div id="member-avatar-preview" class="mx-auto flex h-32 w-32 items-center justify-center overflow-hidden rounded-full bg-sh-s100 text-2xl font-bold text-sh-s500">M</div>
                                <label class="sh-btn sh-btn-outline mt-4 inline-flex cursor-pointer">
                                    Select Image
                                    <input id="member-avatar-input" type="file" accept="image/jpeg,image/png" class="hidden">
                                </label>
                                <p class="mt-2 text-xs text-sh-s400">JPEG or PNG, maximum 1 MB</p>
                            </div>
                        </form>
                    </section>

                    <section data-member-panel="payments" class="hidden">
                        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                            <h1 class="text-xl font-bold text-sh-s900">Credit / Debit Card</h1>
                            <button type="button" class="sh-btn sh-btn-blue" data-open-payment="card">+ Add New Card</button>
                        </div>
                        <div id="member-cards-list" class="space-y-3"></div>
                        <div class="mt-10 mb-4 flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-lg font-bold text-sh-s900">My Bank Accounts</h2>
                            <button type="button" class="sh-btn sh-btn-blue" data-open-payment="bank">+ Add New Bank Account</button>
                        </div>
                        <div id="member-banks-list" class="space-y-3"></div>
                    </section>

                    <section data-member-panel="addresses" class="hidden">
                        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                            <h1 class="text-xl font-bold text-sh-s900">My Addresses</h1>
                            <button type="button" class="sh-btn sh-btn-blue" data-open-address>+ Add Address</button>
                        </div>
                        <div id="member-addresses-list" class="space-y-4"></div>
                    </section>

                    <section data-member-panel="password" class="hidden">
                        <h1 class="text-xl font-bold text-sh-s900">Change Password</h1>
                        <form id="member-password-form" class="mt-6 max-w-md space-y-4" novalidate>
                            <div class="sh-form-field">
                                <label class="sh-form-label" for="member-current-password">Current password</label>
                                <input id="member-current-password" name="current_password" type="password" class="sh-form-input" required>
                            </div>
                            <div class="sh-form-field">
                                <label class="sh-form-label" for="member-new-password">New password</label>
                                <input id="member-new-password" name="password" type="password" class="sh-form-input" required>
                            </div>
                            <div class="sh-form-field">
                                <label class="sh-form-label" for="member-new-password-confirm">Confirm password</label>
                                <input id="member-new-password-confirm" name="password_confirmation" type="password" class="sh-form-input" required>
                            </div>
                            <button type="submit" class="sh-btn sh-btn-blue">Update password</button>
                        </form>
                    </section>

                    <section data-member-panel="privacy" class="hidden">
                        <h1 class="text-xl font-bold text-sh-s900">Privacy Settings</h1>
                        <div class="mt-6 space-y-4 text-sm">
                            <label class="flex items-start justify-between gap-4 rounded-xl border border-sh-s100 p-4">
                                <span>Share order activity with sellers for support</span>
                                <input id="set-share-activity" type="checkbox">
                            </label>
                            <label class="flex items-start justify-between gap-4 rounded-xl border border-sh-s100 p-4">
                                <span>Personalized furniture recommendations</span>
                                <input id="set-personalized" type="checkbox">
                            </label>
                        </div>
                    </section>

                    <section data-member-panel="notif-settings" class="hidden">
                        <h1 class="text-xl font-bold text-sh-s900">Notification Settings</h1>
                        <div class="mt-6 space-y-4 text-sm">
                            <label class="flex items-start justify-between gap-4 rounded-xl border border-sh-s100 p-4">
                                <span>Order updates</span>
                                <input id="set-notif-orders" type="checkbox">
                            </label>
                            <label class="flex items-start justify-between gap-4 rounded-xl border border-sh-s100 p-4">
                                <span>Promotions and vouchers</span>
                                <input id="set-notif-promos" type="checkbox">
                            </label>
                            <label class="flex items-start justify-between gap-4 rounded-xl border border-sh-s100 p-4">
                                <span>Wallet updates</span>
                                <input id="set-notif-wallet" type="checkbox">
                            </label>
                        </div>
                    </section>

                    <section data-member-panel="order-settings" class="hidden">
                        <h1 class="text-xl font-bold text-sh-s900">Order Settings</h1>
                        <div class="mt-6 max-w-md space-y-4">
                            <div class="sh-form-field">
                                <label class="sh-form-label" for="set-delivery-type">Default delivery type</label>
                                <select id="set-delivery-type" class="sh-form-input">
                                    <option value="CURBSIDE">Curbside</option>
                                    <option value="IN_HOME">In-home</option>
                                </select>
                            </div>
                            <label class="flex items-center justify-between gap-4 rounded-xl border border-sh-s100 p-4 text-sm">
                                <span>Allow similar item substitution</span>
                                <input id="set-substitution" type="checkbox">
                            </label>
                        </div>
                    </section>

                    <section data-member-panel="purchases" class="hidden">
                        <div class="flex flex-wrap gap-2 border-b border-sh-s100 pb-3">
                            @foreach ([
                                'all' => 'All',
                                'to_pay' => 'To Pay',
                                'to_ship' => 'To Ship',
                                'to_receive' => 'To Receive',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'return_refund' => 'Return Refund',
                            ] as $key => $label)
                                <button type="button" class="member-purchase-tab {{ $key === 'all' ? 'is-active' : '' }}" data-purchase-tab="{{ $key }}">{{ $label }}</button>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <input id="member-order-search" class="sh-form-input" type="search" placeholder="Search by seller, order ID, or product name">
                        </div>
                        <div id="member-orders-list" class="mt-4 space-y-3"></div>
                    </section>

                    <section data-member-panel="notifications" class="hidden">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h1 class="text-xl font-bold text-sh-s900">Notifications</h1>
                            <button type="button" class="text-sm font-semibold text-sh-blue" data-read-all-notifications>Mark all as read</button>
                        </div>
                        <div id="member-notifications-list" class="space-y-2"></div>
                    </section>

                    <section data-member-panel="vouchers" class="hidden">
                        <h1 class="text-xl font-bold text-sh-s900">My Vouchers</h1>
                        <form id="member-voucher-form" class="mt-4 flex flex-col gap-2 sm:flex-row">
                            <input id="member-voucher-code" name="code" class="sh-form-input flex-1" placeholder="Enter voucher code" required>
                            <button type="submit" class="sh-btn sh-btn-blue">Redeem</button>
                        </form>
                        <div id="member-vouchers-list" class="mt-6 grid gap-3 sm:grid-cols-2"></div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <x-shared.modal id="member-address-modal" title="Address" maxWidth="max-w-[520px]">
        <form id="member-address-form" class="space-y-3" novalidate>
            <input type="hidden" name="uuid" id="address-uuid">
            <input id="address-recipient" name="recipient_name" class="sh-form-input" placeholder="Recipient name" required>
            <input id="address-phone" name="phone" class="sh-form-input" placeholder="Phone" required>
            <input id="address-line1" name="line1" class="sh-form-input" placeholder="Street address" required>
            <input id="address-line2" name="line2" class="sh-form-input" placeholder="Apt, suite (optional)">
            <div class="grid grid-cols-2 gap-3">
                <input id="address-city" name="city" class="sh-form-input" placeholder="City" required>
                <input id="address-state" name="state" class="sh-form-input" placeholder="State" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input id="address-zip" name="zip" class="sh-form-input" placeholder="ZIP" required>
                <input id="address-label" name="label" class="sh-form-input" placeholder="Label (Home)">
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_default" id="address-default"> Set as default</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_pickup" id="address-pickup"> Pickup address</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_return" id="address-return"> Return address</label>
            <button type="submit" class="sh-btn sh-btn-blue sh-btn-full">Save address</button>
        </form>
    </x-shared.modal>

    <x-shared.modal id="member-payment-modal" title="Payment method" maxWidth="max-w-[480px]">
        <form id="member-payment-form" class="space-y-3" novalidate>
            <input type="hidden" name="type" id="payment-type" value="card">
            <p class="text-[13px] text-sh-s500">Store brand and last 4 digits only. Full card numbers are not saved.</p>
            <div id="payment-card-fields" class="space-y-3">
                <select id="payment-brand" name="brand" class="sh-form-input">
                    <option value="Visa">Visa</option>
                    <option value="Mastercard">Mastercard</option>
                    <option value="Amex">Amex</option>
                    <option value="Discover">Discover</option>
                </select>
                <div class="grid grid-cols-2 gap-3">
                    <input id="payment-exp-month" name="exp_month" class="sh-form-input" placeholder="Exp month" inputmode="numeric">
                    <input id="payment-exp-year" name="exp_year" class="sh-form-input" placeholder="Exp year" inputmode="numeric">
                </div>
            </div>
            <input id="payment-bank-name" name="bank_name" class="sh-form-input hidden" placeholder="Bank name">
            <input id="payment-last4" name="last4" class="sh-form-input" placeholder="Last 4 digits" maxlength="4" inputmode="numeric" required>
            <input id="payment-holder" name="holder_name" class="sh-form-input" placeholder="Name on account" required>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_default" id="payment-default"> Set as default</label>
            <button type="submit" class="sh-btn sh-btn-blue sh-btn-full">Save</button>
        </form>
    </x-shared.modal>

    <x-shared.modal id="member-confirm-modal" title="Please confirm" maxWidth="max-w-[400px]">
        <p id="member-confirm-text" class="text-sm text-sh-s600">Delete this item?</p>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="sh-btn sh-btn-outline" data-modal-close="member-confirm-modal">Cancel</button>
            <button type="button" id="member-confirm-ok" class="sh-btn sh-btn-blue">Delete</button>
        </div>
    </x-shared.modal>
@endsection
