<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SmooveHub project progress tracker — roadmap phases and updates.">
    <title>{{ $progress['title'] }} — SmooveHub</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="sh-pay-body">
    <div id="toast-wrap" class="sh-toast-wrap" aria-live="polite" aria-atomic="true"></div>

    <header class="sh-pay-topbar">
        <div class="sh-pay-container sh-pay-topbar-inner">
            <a href="{{ url('/') }}" class="sh-pay-brand">
                <img src="{{ asset('images/landing/logo-nav.png') }}" alt="SmooveHub" class="sh-pay-logo">
            </a>
            <div class="sh-pay-topbar-meta">
                <span class="sh-pay-pill">{{ $progress['client']['ref'] }}</span>
                <a href="{{ url('/client-payment') }}" class="sh-pay-link">Payments</a>
            </div>
        </div>
    </header>

    <main class="sh-pay-main">
        <div class="sh-pay-container">
            <section class="sh-pay-intro">
                <p class="sh-pay-eyebrow">Public project tracker</p>
                <h1 class="sh-pay-title">{{ $progress['title'] }}</h1>
                <p class="sh-pay-subtitle">{{ $progress['subtitle'] }}</p>

                <div class="sh-pay-meta-grid">
                    <div class="sh-pay-meta-card">
                        <span class="sh-pay-meta-label">Client</span>
                        <span class="sh-pay-meta-value">{{ $progress['client']['name'] }}</span>
                    </div>
                    <div class="sh-pay-meta-card">
                        <span class="sh-pay-meta-label">Project</span>
                        <span class="sh-pay-meta-value">{{ $progress['client']['project'] }}</span>
                    </div>
                    <div class="sh-pay-meta-card">
                        <span class="sh-pay-meta-label">Done</span>
                        <span class="sh-pay-meta-value sh-pay-meta-value-ok">{{ $progress['counts']['done'] }}</span>
                    </div>
                    <div class="sh-pay-meta-card">
                        <span class="sh-pay-meta-label">In progress</span>
                        <span class="sh-pay-meta-value sh-pay-meta-value-pending">{{ $progress['counts']['in_progress'] }}</span>
                    </div>
                    <div class="sh-pay-meta-card">
                        <span class="sh-pay-meta-label">Upcoming</span>
                        <span class="sh-pay-meta-value">{{ $progress['counts']['upcoming'] }}</span>
                    </div>
                </div>

                <div class="sh-prog-overall" aria-label="Overall progress {{ $progress['overall_percent'] }} percent">
                    <div class="sh-prog-overall-head">
                        <span class="sh-pay-meta-label">Overall progress</span>
                        <span class="sh-prog-overall-pct">{{ $progress['overall_percent'] }}%</span>
                    </div>
                    <div class="sh-prog-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress['overall_percent'] }}">
                        <div class="sh-prog-bar-fill" style="width: {{ $progress['overall_percent'] }}%"></div>
                    </div>
                </div>
            </section>

            <section class="sh-pay-section" aria-labelledby="portals-heading">
                <h2 id="portals-heading" class="sh-pay-section-title">1. Portals</h2>
                <div class="sh-pay-rule" aria-hidden="true"></div>

                <div class="sh-pay-table-wrap">
                    <table class="sh-pay-table">
                        <thead>
                            <tr>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Portal</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Audience</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">UI</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Auth</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Data</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($progress['portals'] ?? [] as $portal)
                                <tr>
                                    <td class="sh-pay-td sh-pay-td-left">
                                        <div class="sh-pay-milestone-name">{{ $portal['name'] }}</div>
                                        <a href="{{ url($portal['path']) }}" class="sh-pay-link text-xs">{{ $portal['path'] }}</a>
                                    </td>
                                    <td class="sh-pay-td sh-pay-td-left">{{ $portal['audience'] }}</td>
                                    <td class="sh-pay-td sh-pay-td-left">
                                        <span @class([
                                            'sh-pay-status',
                                            'sh-pay-status-paid' => ($portal['ui_status'] ?? '') === 'live',
                                            'sh-pay-status-upcoming' => ($portal['ui_status'] ?? '') !== 'live',
                                        ])>{{ ucfirst($portal['ui_status']) }}</span>
                                    </td>
                                    <td class="sh-pay-td sh-pay-td-left">
                                        <span @class([
                                            'sh-pay-status',
                                            'sh-pay-status-paid' => ($portal['auth_status'] ?? '') === 'secured',
                                            'sh-pay-status-due' => ($portal['auth_status'] ?? '') === 'open',
                                        ])>{{ ucfirst($portal['auth_status']) }}</span>
                                    </td>
                                    <td class="sh-pay-td sh-pay-td-left">
                                        <span @class([
                                            'sh-pay-status',
                                            'sh-pay-status-paid' => ($portal['data_status'] ?? '') === 'production',
                                            'sh-pay-status-due' => ($portal['data_status'] ?? '') === 'prototype',
                                        ])>{{ ucfirst($portal['data_status']) }}</span>
                                    </td>
                                    <td class="sh-pay-td sh-pay-td-left">{{ $portal['notes'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="sh-pay-section" aria-labelledby="phases-heading">
                <h2 id="phases-heading" class="sh-pay-section-title">2. Roadmap Phases</h2>
                <div class="sh-pay-rule" aria-hidden="true"></div>

                <div class="sh-pay-table-wrap">
                    <table class="sh-pay-table">
                        <thead>
                            <tr>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Phase</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Summary</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-right">Progress</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-right">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($progress['phases'] as $phase)
                                <tr>
                                    <td class="sh-pay-td sh-pay-td-left">
                                        <div class="sh-pay-milestone-name">{{ $phase['name'] }}</div>
                                    </td>
                                    <td class="sh-pay-td sh-pay-td-left">{{ $phase['summary'] }}</td>
                                    <td class="sh-pay-td sh-pay-td-right sh-pay-amount">{{ $phase['percent'] }}%</td>
                                    <td class="sh-pay-td sh-pay-td-right">
                                        <span @class([
                                            'sh-pay-status',
                                            'sh-pay-status-paid' => $phase['status'] === 'done',
                                            'sh-pay-status-due' => $phase['status'] === 'in_progress',
                                            'sh-pay-status-upcoming' => $phase['status'] === 'upcoming',
                                        ])>
                                            {{ str_replace('_', ' ', ucfirst($phase['status'])) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="sh-pay-section" aria-labelledby="history-heading">
                <h2 id="history-heading" class="sh-pay-section-title">3. Update History</h2>
                <div class="sh-pay-rule" aria-hidden="true"></div>

                <div class="sh-pay-table-wrap">
                    <table class="sh-pay-table">
                        <thead>
                            <tr>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Date</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Update</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Detail</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-right">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($progress['history'] as $entry)
                                <tr>
                                    <td class="sh-pay-td sh-pay-td-left whitespace-nowrap">
                                        {{ \Illuminate\Support\Carbon::parse($entry['date'])->format('M j, Y') }}
                                    </td>
                                    <td class="sh-pay-td sh-pay-td-left">
                                        <div class="sh-pay-milestone-name">{{ $entry['title'] }}</div>
                                    </td>
                                    <td class="sh-pay-td sh-pay-td-left">{{ $entry['detail'] }}</td>
                                    <td class="sh-pay-td sh-pay-td-right">
                                        <span class="sh-pay-status sh-pay-status-paid">{{ ucfirst($entry['status']) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
