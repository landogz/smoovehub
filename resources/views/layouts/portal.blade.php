<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SmooveHub Portal')</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal min-h-screen bg-sh-s50 text-sh-s900 antialiased">
    <div id="toast-wrap" class="sh-toast-wrap" aria-live="polite" aria-atomic="true"></div>

    <script type="application/json" id="prototype-bootstrap">@json($portal['catalog_products'] ?? [])</script>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <x-auth.login-modal />
    <x-auth.signup-modal />
    <x-auth.seller-signup-modal />
    <x-shop.purchase-modal />
    <x-seller.add-product-modal />

    <x-shared.dolores-widget />

    @stack('scripts')
</body>
</html>
