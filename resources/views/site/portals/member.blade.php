@extends('layouts.landing')

@section('title', 'My Account — SmooveHub')

@section('content')
    <div id="member-account" class="sh-container py-8 md:py-12">
        <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-sh-s400">Member</p>
                <h1 class="text-2xl font-black text-sh-s900 md:text-3xl">My Account</h1>
                <p class="mt-1 text-[13px] text-sh-s500">Welcome back, <strong id="member-greet">Member</strong></p>
            </div>
            <a href="{{ route('site.shop') }}" class="sh-btn sh-btn-blue">Continue shopping</a>
        </div>

        <div class="mb-8 grid grid-cols-2 gap-4">
            <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Orders</div>
                <div id="member-stat-orders" class="sh-display mt-2 text-3xl font-black text-sh-s900">0</div>
            </div>
            <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-widest text-sh-s400">Awaiting payment</div>
                <div id="member-stat-unpaid" class="sh-display mt-2 text-3xl font-black text-sh-s900">0</div>
            </div>
        </div>

        <div class="rounded-2xl border-[1.5px] border-sh-s200 bg-white p-5 md:p-6">
            <h2 class="mb-4 text-sm font-bold text-sh-s900">My orders</h2>
            <div id="member-orders-list" class="space-y-3">
                <p class="py-6 text-center text-[13px] text-sh-s400">Sign in to see orders you placed on the shop.</p>
            </div>
        </div>
    </div>
@endsection
