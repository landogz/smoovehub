<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <meta name="description" content="This SmooveHub page could not be found.">
    <title>Page not found — SmooveHub</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="sh-error-body">
    <header class="sh-error-topbar">
        <div class="sh-error-wrap sh-error-topbar-inner">
            <a href="{{ route('site.home') }}" class="inline-flex items-center">
                <img src="{{ asset('images/landing/logo-nav.png') }}" alt="SmooveHub" class="sh-error-logo">
            </a>
            <span class="sh-error-pill">Error 404</span>
        </div>
    </header>

    <main class="sh-error-main">
        <div class="sh-error-wrap sh-error-panel">
            <p class="sh-error-code" aria-hidden="true">404</p>
            <h1 class="sh-error-title">This page is not on the floor.</h1>
            <p class="sh-error-copy">
                The link may be outdated, or the listing has already moved. Shop verified furniture, request a CRS delivery quote, or head back to the marketplace.
            </p>
            <div class="sh-error-actions">
                <a href="{{ route('site.home') }}" class="sh-btn sh-btn-orange">Back to home</a>
                <a href="{{ route('site.shop') }}" class="sh-btn sh-btn-ghost">Browse the shop</a>
            </div>
            <nav class="sh-error-links" aria-label="Helpful links">
                <a href="{{ route('site.sellers') }}">Sellers</a>
                <a href="{{ route('site.quote') }}">Delivery quote</a>
                <a href="{{ route('site.contact') }}">Contact</a>
            </nav>
        </div>
    </main>
</body>
</html>
