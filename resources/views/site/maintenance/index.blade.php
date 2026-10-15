<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <meta name="description" content="SmooveHub is temporarily under maintenance.">
    <title>{{ $maintenance['title'] }} — SmooveHub</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="sh-pay-body">
    <header class="sh-pay-topbar">
        <div class="sh-pay-container sh-pay-topbar-inner">
            <span class="sh-pay-brand">
                <img src="{{ asset('images/landing/logo-nav.png') }}" alt="SmooveHub" class="sh-pay-logo">
            </span>
            <div class="sh-pay-topbar-meta">
                <span class="sh-pay-pill">Maintenance</span>
            </div>
        </div>
    </header>

    <main class="sh-maint-main">
        <div class="sh-pay-container">
            <section class="sh-maint-card" aria-labelledby="maintenance-title">
                <p class="sh-pay-eyebrow">{{ $maintenance['eyebrow'] }}</p>
                <h1 id="maintenance-title" class="sh-pay-title">{{ $maintenance['title'] }}</h1>
                <p class="sh-maint-message">{{ $maintenance['message'] }}</p>
                <p class="sh-pay-footer-note">Marketplace pages are paused while we finish this update.</p>
            </section>
        </div>
    </main>
</body>
</html>
