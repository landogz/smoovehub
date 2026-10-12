<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="AI-powered multi-vendor furniture marketplace. 180+ verified sellers nationwide.">

    <title>@yield('title', 'SmooveHub — Your Furniture. Our Promise.')</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <div id="toast-wrap" class="sh-toast-wrap" aria-live="polite" aria-atomic="true"></div>

    <script type="application/json" id="prototype-bootstrap">@json($site['catalog_products'] ?? $site['products'] ?? $site['featured_products'] ?? [])</script>

    @include('components.landing.navbar', [
        'navLinks' => $site['nav_links'] ?? [],
        'navPills' => $site['nav_pills'] ?? [],
        'activeNav' => $activeNav ?? null,
    ])

    <main class="flex-1">
        @yield('content')
    </main>

    @include('components.landing.footer', [
        'footerColumns' => $site['footer_columns'] ?? [],
    ])

    @include('components.landing.cookie-bar')

    <x-auth.login-modal />
    <x-auth.signup-modal />
    <x-auth.seller-signup-modal />
    <x-shop.purchase-modal />
    <x-seller.add-product-modal />

    <x-shared.dolores-widget />

    @stack('scripts')
</body>
</html>
