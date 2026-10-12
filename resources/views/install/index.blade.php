<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Install SmooveHub — configure database and create your admin account.">
    <title>Install SmooveHub</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sh-s50 text-sh-s900 antialiased">
    <div id="toast-wrap" class="sh-toast-wrap" aria-live="polite" aria-atomic="true"></div>

    <script type="application/json" id="install-bootstrap">@json([
        'defaultAppUrl' => $defaultAppUrl,
        'status' => $status,
    ])</script>

    <div class="mx-auto flex min-h-screen w-full max-w-3xl flex-col px-4 py-8 sm:px-6 sm:py-12">
        <header class="mb-8 flex flex-col items-center gap-3 text-center sm:mb-10">
            <img
                src="{{ asset('images/landing/logo-nav.png') }}"
                alt="SmooveHub"
                class="h-10 w-auto sm:h-12"
            >
            <h1 class="sh-display text-3xl font-extrabold tracking-tight text-sh-navy sm:text-4xl">
                Install SmooveHub
            </h1>
            <p class="max-w-xl text-sm text-sh-s600 sm:text-base">
                Enter your MySQL database settings and create an admin account to finish setup.
            </p>
        </header>

        <section class="mb-6 rounded-2xl border border-sh-s200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="requirements-heading">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 id="requirements-heading" class="text-lg font-semibold text-sh-navy">Server requirements</h2>
                <span id="requirements-badge" class="rounded-full bg-sh-s100 px-3 py-1 text-xs font-semibold text-sh-s600">Checking…</span>
            </div>
            <ul id="requirements-list" class="space-y-2" role="list"></ul>
        </section>

        <form id="install-form" class="space-y-6 rounded-2xl border border-sh-s200 bg-white p-5 shadow-sm sm:p-6" novalidate>
            <fieldset class="space-y-4">
                <legend class="mb-1 text-lg font-semibold text-sh-navy">Application</legend>
                <div>
                    <label for="app_url" class="mb-1.5 block text-sm font-medium text-sh-s700">App URL</label>
                    <input
                        id="app_url"
                        name="app_url"
                        type="url"
                        required
                        autocomplete="url"
                        value="{{ $defaultAppUrl }}"
                        class="w-full rounded-xl border border-sh-s200 bg-white px-3.5 py-2.5 text-sm text-sh-s900 outline-none transition focus:border-sh-blue focus:ring-2 focus:ring-sh-blue/20"
                    >
                </div>
            </fieldset>

            <fieldset class="space-y-4">
                <legend class="mb-1 text-lg font-semibold text-sh-navy">Database</legend>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <label for="db_host" class="mb-1.5 block text-sm font-medium text-sh-s700">Host</label>
                        <input id="db_host" name="db_host" type="text" required value="127.0.0.1" autocomplete="off"
                            class="w-full rounded-xl border border-sh-s200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sh-blue focus:ring-2 focus:ring-sh-blue/20">
                    </div>
                    <div>
                        <label for="db_port" class="mb-1.5 block text-sm font-medium text-sh-s700">Port</label>
                        <input id="db_port" name="db_port" type="number" required min="1" max="65535" value="3306" autocomplete="off"
                            class="w-full rounded-xl border border-sh-s200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sh-blue focus:ring-2 focus:ring-sh-blue/20">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="db_database" class="mb-1.5 block text-sm font-medium text-sh-s700">Database name</label>
                        <input id="db_database" name="db_database" type="text" required value="smoovehub" autocomplete="off"
                            class="w-full rounded-xl border border-sh-s200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sh-blue focus:ring-2 focus:ring-sh-blue/20">
                    </div>
                    <div>
                        <label for="db_username" class="mb-1.5 block text-sm font-medium text-sh-s700">Username</label>
                        <input id="db_username" name="db_username" type="text" required value="root" autocomplete="username"
                            class="w-full rounded-xl border border-sh-s200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sh-blue focus:ring-2 focus:ring-sh-blue/20">
                    </div>
                    <div>
                        <label for="db_password" class="mb-1.5 block text-sm font-medium text-sh-s700">Password</label>
                        <input id="db_password" name="db_password" type="password" value="" autocomplete="new-password"
                            class="w-full rounded-xl border border-sh-s200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sh-blue focus:ring-2 focus:ring-sh-blue/20">
                    </div>
                </div>
            </fieldset>

            <fieldset class="space-y-4">
                <legend class="mb-1 text-lg font-semibold text-sh-navy">Admin account</legend>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="admin_name" class="mb-1.5 block text-sm font-medium text-sh-s700">Full name</label>
                        <input id="admin_name" name="admin_name" type="text" required autocomplete="name"
                            class="w-full rounded-xl border border-sh-s200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sh-blue focus:ring-2 focus:ring-sh-blue/20">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="admin_email" class="mb-1.5 block text-sm font-medium text-sh-s700">Email</label>
                        <input id="admin_email" name="admin_email" type="email" required autocomplete="email"
                            class="w-full rounded-xl border border-sh-s200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sh-blue focus:ring-2 focus:ring-sh-blue/20">
                    </div>
                    <div>
                        <label for="admin_password" class="mb-1.5 block text-sm font-medium text-sh-s700">Password</label>
                        <input id="admin_password" name="admin_password" type="password" required minlength="8" autocomplete="new-password"
                            class="w-full rounded-xl border border-sh-s200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sh-blue focus:ring-2 focus:ring-sh-blue/20">
                    </div>
                    <div>
                        <label for="admin_password_confirmation" class="mb-1.5 block text-sm font-medium text-sh-s700">Confirm password</label>
                        <input id="admin_password_confirmation" name="admin_password_confirmation" type="password" required minlength="8" autocomplete="new-password"
                            class="w-full rounded-xl border border-sh-s200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sh-blue focus:ring-2 focus:ring-sh-blue/20">
                    </div>
                </div>
            </fieldset>

            <div id="install-progress" class="hidden rounded-xl border border-sh-blue/20 bg-sh-blue-lt px-4 py-3 text-sm text-sh-blue" role="status" aria-live="polite">
                Installing… writing config, migrating, and seeding.
            </div>

            <button
                id="install-submit"
                type="submit"
                class="inline-flex w-full items-center justify-center rounded-xl bg-sh-orange px-5 py-3 text-sm font-bold text-white transition hover:bg-sh-orange-dk focus:outline-none focus:ring-2 focus:ring-sh-orange/40 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
            >
                Install SmooveHub
            </button>
        </form>

        <p class="mt-8 text-center text-xs text-sh-s400">
            SmooveHub Inc. Powered by Competitive Relocation Service.
        </p>
    </div>
</body>
</html>
