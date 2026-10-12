<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Install SmooveHub — configure database and create your admin account.">
    <title>Install SmooveHub</title>
    <style>
        :root {
            --navy: #0d1b3e;
            --blue: #1b3c87;
            --blue-lt: #eef2ff;
            --orange: #f97316;
            --orange-dk: #e8620a;
            --s50: #f8fafc;
            --s100: #f1f5f9;
            --s200: #e2e8f0;
            --s400: #94a3b8;
            --s600: #475569;
            --s700: #334155;
            --s900: #0f172a;
            --green: #16a34a;
            --green-bg: #f0fdf4;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: var(--s50);
            color: var(--s900);
            line-height: 1.5;
        }
        .wrap { max-width: 48rem; margin: 0 auto; padding: 2rem 1rem 3rem; }
        .header { text-align: center; margin-bottom: 2rem; }
        .header img { height: 2.75rem; width: auto; }
        .header h1 { margin: 0.75rem 0 0.35rem; color: var(--navy); font-size: clamp(1.75rem, 4vw, 2.25rem); }
        .header p { margin: 0 auto; max-width: 36rem; color: var(--s600); font-size: 0.95rem; }
        .card {
            background: #fff;
            border: 1px solid var(--s200);
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .card h2, .card legend {
            margin: 0 0 0.75rem;
            color: var(--navy);
            font-size: 1.05rem;
            font-weight: 700;
        }
        .row-between { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 1rem; }
        .badge {
            border-radius: 999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            background: var(--s100);
            color: var(--s600);
        }
        .badge.ok { background: var(--green-bg); color: var(--green); }
        .badge.bad { background: #fef2f2; color: #b91c1c; }
        .req-list { list-style: none; margin: 0; padding: 0; display: grid; gap: 0.5rem; }
        .req-item {
            display: flex; justify-content: space-between; gap: 0.75rem;
            border: 1px solid var(--s200); border-radius: 0.75rem; padding: 0.65rem 0.85rem; font-size: 0.9rem;
        }
        .req-item.pass { border-color: rgba(22, 163, 74, 0.3); background: var(--green-bg); color: var(--green); }
        .req-item.fail { border-color: #fecaca; background: #fef2f2; color: #b91c1c; }
        .req-item .detail { font-size: 0.75rem; opacity: 0.85; margin-top: 0.15rem; }
        .grid { display: grid; gap: 1rem; }
        @media (min-width: 640px) { .grid-2 { grid-template-columns: 1fr 1fr; } .span-2 { grid-column: span 2; } }
        label { display: block; margin-bottom: 0.4rem; font-size: 0.875rem; font-weight: 600; color: var(--s700); }
        input {
            width: 100%; border: 1px solid var(--s200); border-radius: 0.75rem;
            padding: 0.65rem 0.85rem; font: inherit; color: var(--s900); background: #fff;
        }
        input:focus { outline: 2px solid rgba(27, 60, 135, 0.2); border-color: var(--blue); }
        fieldset { border: 0; margin: 0 0 1.25rem; padding: 0; }
        .progress {
            display: none; border: 1px solid rgba(27, 60, 135, 0.2); background: var(--blue-lt);
            color: var(--blue); border-radius: 0.75rem; padding: 0.75rem 1rem; font-size: 0.9rem; margin-bottom: 1rem;
        }
        .progress.show { display: block; }
        button[type="submit"] {
            border: 0; border-radius: 0.75rem; background: var(--orange); color: #fff;
            font-weight: 700; font-size: 0.9rem; padding: 0.8rem 1.25rem; cursor: pointer;
        }
        button[type="submit"]:hover { background: var(--orange-dk); }
        button[type="submit"]:disabled { opacity: 0.6; cursor: not-allowed; }
        .footer { text-align: center; color: var(--s400); font-size: 0.75rem; margin-top: 2rem; }
        #toast-wrap {
            position: fixed; top: 1rem; right: 1rem; z-index: 50;
            display: flex; flex-direction: column; gap: 0.5rem; max-width: min(22rem, calc(100vw - 2rem));
        }
        .toast {
            display: flex; gap: 0.65rem; align-items: flex-start;
            background: #fff; border: 1px solid var(--s200); border-radius: 0.75rem;
            padding: 0.75rem 0.9rem; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        }
        .toast.success { border-color: rgba(22, 163, 74, 0.35); }
        .toast.error { border-color: #fecaca; }
        .toast-title { font-weight: 700; font-size: 0.875rem; }
        .toast-msg { font-size: 0.8rem; color: var(--s600); margin-top: 0.1rem; }
    </style>
</head>
<body>
    <div id="toast-wrap" aria-live="polite" aria-atomic="true"></div>

    <script type="application/json" id="install-bootstrap">@json([
        'defaultAppUrl' => $defaultAppUrl,
        'status' => $status,
    ])</script>

    <div class="wrap">
        <header class="header">
            <img src="{{ asset('images/landing/logo-nav.png') }}" alt="SmooveHub">
            <h1>Install SmooveHub</h1>
            <p>Enter your MySQL database settings and create an admin account to finish setup.</p>
        </header>

        <section class="card" aria-labelledby="requirements-heading">
            <div class="row-between">
                <h2 id="requirements-heading">Server requirements</h2>
                <span id="requirements-badge" class="badge">Checking…</span>
            </div>
            <ul id="requirements-list" class="req-list" role="list"></ul>
        </section>

        <form id="install-form" class="card" novalidate>
            <fieldset>
                <legend>Application</legend>
                <div>
                    <label for="app_url">App URL</label>
                    <input id="app_url" name="app_url" type="url" required autocomplete="url" value="{{ $defaultAppUrl }}">
                </div>
            </fieldset>

            <fieldset>
                <legend>Database</legend>
                <div class="grid grid-2">
                    <div>
                        <label for="db_host">Host</label>
                        <input id="db_host" name="db_host" type="text" required value="127.0.0.1" autocomplete="off">
                    </div>
                    <div>
                        <label for="db_port">Port</label>
                        <input id="db_port" name="db_port" type="number" required min="1" max="65535" value="3306" autocomplete="off">
                    </div>
                    <div class="span-2">
                        <label for="db_database">Database name</label>
                        <input id="db_database" name="db_database" type="text" required value="smoovehub" autocomplete="off">
                    </div>
                    <div>
                        <label for="db_username">Username</label>
                        <input id="db_username" name="db_username" type="text" required value="root" autocomplete="username">
                    </div>
                    <div>
                        <label for="db_password">Password</label>
                        <input id="db_password" name="db_password" type="password" value="" autocomplete="new-password">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Admin account</legend>
                <div class="grid grid-2">
                    <div class="span-2">
                        <label for="admin_name">Full name</label>
                        <input id="admin_name" name="admin_name" type="text" required autocomplete="name">
                    </div>
                    <div class="span-2">
                        <label for="admin_email">Email</label>
                        <input id="admin_email" name="admin_email" type="email" required autocomplete="email">
                    </div>
                    <div>
                        <label for="admin_password">Password</label>
                        <input id="admin_password" name="admin_password" type="password" required minlength="8" autocomplete="new-password">
                    </div>
                    <div>
                        <label for="admin_password_confirmation">Confirm password</label>
                        <input id="admin_password_confirmation" name="admin_password_confirmation" type="password" required minlength="8" autocomplete="new-password">
                    </div>
                </div>
            </fieldset>

            <div id="install-progress" class="progress" role="status" aria-live="polite">
                Installing… writing config, migrating, and seeding.
            </div>

            <button id="install-submit" type="submit">Install SmooveHub</button>
        </form>

        <p class="footer">SmooveHub Inc. Powered by Competitive Relocation Service.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios@1.11.0/dist/axios.min.js"></script>
    <script>
        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrf) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf;
            }
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

            function showToast(title, message, type) {
                const wrap = document.getElementById('toast-wrap');
                if (!wrap) return;
                const toast = document.createElement('div');
                toast.className = 'toast ' + (type === 'error' ? 'error' : 'success');
                toast.innerHTML = '<div><div class="toast-title"></div><div class="toast-msg"></div></div>';
                toast.querySelector('.toast-title').textContent = title;
                toast.querySelector('.toast-msg').textContent = message || '';
                wrap.appendChild(toast);
                setTimeout(function () { toast.remove(); }, 4000);
            }

            function readBootstrap() {
                try {
                    return JSON.parse(document.getElementById('install-bootstrap')?.textContent || '{}');
                } catch (e) {
                    return null;
                }
            }

            function renderRequirements(checks, ready) {
                const list = document.getElementById('requirements-list');
                const badge = document.getElementById('requirements-badge');
                if (!list || !badge) return;

                list.innerHTML = (checks || []).map(function (check) {
                    return '<li class="req-item ' + (check.passed ? 'pass' : 'fail') + '">' +
                        '<div><div><strong></strong></div><div class="detail"></div></div>' +
                        '<span></span></li>';
                }).join('');

                Array.from(list.children).forEach(function (li, i) {
                    const check = checks[i];
                    li.querySelector('strong').textContent = check.label;
                    li.querySelector('.detail').textContent = check.detail;
                    li.querySelector('span').textContent = check.passed ? 'OK' : 'Fail';
                });

                badge.textContent = ready ? 'Ready' : 'Action needed';
                badge.className = 'badge ' + (ready ? 'ok' : 'bad');
            }

            function setInstalling(on) {
                const form = document.getElementById('install-form');
                const submit = document.getElementById('install-submit');
                const progress = document.getElementById('install-progress');
                if (submit) {
                    submit.disabled = on;
                    submit.textContent = on ? 'Installing…' : 'Install SmooveHub';
                }
                if (progress) progress.classList.toggle('show', on);
                if (form) {
                    form.querySelectorAll('input, button').forEach(function (el) {
                        if (el.id !== 'install-submit') el.disabled = on;
                    });
                }
            }

            function firstValidationError(errors) {
                const values = Object.values(errors || {});
                if (!values.length) return null;
                return Array.isArray(values[0]) ? values[0][0] : String(values[0]);
            }

            async function loadStatus(fallback) {
                try {
                    const response = await axios.get('/api/v1/install/status');
                    const data = response.data?.data || {};
                    renderRequirements(data.checks || [], !!data.ready);
                    return data;
                } catch (e) {
                    if (fallback?.status) {
                        renderRequirements(fallback.status.checks || [], !!fallback.status.ready);
                        return fallback.status;
                    }
                    showToast('Could not load status', 'Refresh the page and try again.', 'error');
                    return null;
                }
            }

            async function submitInstall(event) {
                event.preventDefault();
                const form = event.currentTarget;
                const fd = new FormData(form);
                const payload = {
                    app_url: String(fd.get('app_url') || '').trim(),
                    db_host: String(fd.get('db_host') || '').trim(),
                    db_port: Number(fd.get('db_port') || 3306),
                    db_database: String(fd.get('db_database') || '').trim(),
                    db_username: String(fd.get('db_username') || '').trim(),
                    db_password: String(fd.get('db_password') || ''),
                    admin_name: String(fd.get('admin_name') || '').trim(),
                    admin_email: String(fd.get('admin_email') || '').trim(),
                    admin_password: String(fd.get('admin_password') || ''),
                    admin_password_confirmation: String(fd.get('admin_password_confirmation') || ''),
                };

                if (payload.admin_password !== payload.admin_password_confirmation) {
                    showToast('Passwords do not match', 'Confirm your admin password and try again.', 'error');
                    return;
                }

                setInstalling(true);
                try {
                    const response = await axios.post('/api/v1/install', payload);
                    const data = response.data?.data || {};
                    showToast('Install complete', response.data?.message || 'SmooveHub is ready.', 'success');
                    setTimeout(function () {
                        window.location.href = data.redirect || '/admin';
                    }, 900);
                } catch (error) {
                    const message =
                        firstValidationError(error.response?.data?.errors) ||
                        error.response?.data?.message ||
                        'Installation failed. Check your database settings.';
                    showToast('Install failed', message, 'error');
                    setInstalling(false);
                    await loadStatus(readBootstrap());
                }
            }

            const bootstrap = readBootstrap();
            const form = document.getElementById('install-form');
            if (!form) return;
            if (bootstrap?.status) {
                renderRequirements(bootstrap.status.checks || [], !!bootstrap.status.ready);
            }
            loadStatus(bootstrap);
            form.addEventListener('submit', submitInstall);
        })();
    </script>
</body>
</html>
