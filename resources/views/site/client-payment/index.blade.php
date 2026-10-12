<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SmooveHub client payment milestones and history.">
    <title>{{ $payment['title'] }} — SmooveHub</title>
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
                <span class="sh-pay-pill">{{ $payment['client']['invoice_ref'] }}</span>
                <a href="{{ url('/contact') }}" class="sh-pay-link">Support</a>
            </div>
        </div>
    </header>

    <main class="sh-pay-main">
        <div class="sh-pay-container">
            <section class="sh-pay-intro">
                <p class="sh-pay-eyebrow">Public client payment</p>
                <h1 class="sh-pay-title">{{ $payment['title'] }}</h1>
                <p class="sh-pay-subtitle">{{ $payment['subtitle'] }}</p>

                <div class="sh-pay-meta-grid">
                    <div class="sh-pay-meta-card">
                        <span class="sh-pay-meta-label">Client</span>
                        <span class="sh-pay-meta-value">{{ $payment['client']['name'] }}</span>
                    </div>
                    <div class="sh-pay-meta-card">
                        <span class="sh-pay-meta-label">Project</span>
                        <span class="sh-pay-meta-value">{{ $payment['client']['project'] }}</span>
                    </div>
                    <div class="sh-pay-meta-card">
                        <span class="sh-pay-meta-label">Paid</span>
                        <span class="sh-pay-meta-value sh-pay-meta-value-ok">${{ number_format($payment['summary']['paid']) }}</span>
                    </div>
                    <div class="sh-pay-meta-card">
                        <span class="sh-pay-meta-label">Pending</span>
                        <span class="sh-pay-meta-value sh-pay-meta-value-pending">${{ number_format($payment['summary']['pending'] ?? 0) }}</span>
                    </div>
                    <div class="sh-pay-meta-card">
                        <span class="sh-pay-meta-label">Outstanding</span>
                        <span class="sh-pay-meta-value sh-pay-meta-value-accent">${{ number_format($payment['summary']['outstanding']) }}</span>
                    </div>
                </div>
            </section>

            {{-- Milestone Schedule --}}
            <section class="sh-pay-section" aria-labelledby="milestone-heading">
                <h2 id="milestone-heading" class="sh-pay-section-title">2. Payment Milestone Schedule</h2>
                <div class="sh-pay-rule" aria-hidden="true"></div>

                <div class="sh-pay-table-wrap">
                    <table class="sh-pay-table">
                        <thead>
                            <tr>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Milestone</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Trigger / Condition</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-right">Amount</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-right">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payment['milestones'] as $milestone)
                                <tr>
                                    <td class="sh-pay-td sh-pay-td-left">
                                        <div class="sh-pay-milestone-name">{{ $milestone['name'] }}</div>
                                        <span @class([
                                            'sh-pay-status',
                                            'sh-pay-status-paid' => $milestone['status'] === 'paid',
                                            'sh-pay-status-due' => $milestone['status'] === 'due',
                                            'sh-pay-status-upcoming' => $milestone['status'] === 'upcoming',
                                        ])>
                                            {{ ucfirst($milestone['status']) }}
                                        </span>
                                    </td>
                                    <td class="sh-pay-td sh-pay-td-left">{{ $milestone['trigger'] }}</td>
                                    <td class="sh-pay-td sh-pay-td-right sh-pay-amount">${{ number_format($milestone['amount']) }}</td>
                                    <td class="sh-pay-td sh-pay-td-right">{{ $milestone['percent'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="sh-pay-tf sh-pay-tf-left" colspan="2">TOTAL</td>
                                <td class="sh-pay-tf sh-pay-tf-right sh-pay-tf-accent">${{ number_format($payment['total_amount']) }}</td>
                                <td class="sh-pay-tf sh-pay-tf-right sh-pay-tf-accent">{{ $payment['total_percent'] }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            {{-- Payment History --}}
            <section class="sh-pay-section" aria-labelledby="history-heading">
                <h2 id="history-heading" class="sh-pay-section-title">3. Payment History</h2>
                <div class="sh-pay-rule" aria-hidden="true"></div>

                <div class="sh-pay-table-wrap">
                    <table class="sh-pay-table">
                        <thead>
                            <tr>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Date</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Reference</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Milestone / Event</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-left">Method</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-right">Amount</th>
                                <th scope="col" class="sh-pay-th sh-pay-th-right">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payment['history'] as $entry)
                                <tr>
                                    <td class="sh-pay-td sh-pay-td-left whitespace-nowrap">
                                        {{ \Illuminate\Support\Carbon::parse($entry['date'])->format('M j, Y') }}
                                    </td>
                                    <td class="sh-pay-td sh-pay-td-left">{{ $entry['reference'] }}</td>
                                    <td class="sh-pay-td sh-pay-td-left">{{ $entry['milestone'] }}</td>
                                    <td class="sh-pay-td sh-pay-td-left">{{ $entry['method'] }}</td>
                                    <td class="sh-pay-td sh-pay-td-right sh-pay-amount">${{ number_format($entry['amount']) }}</td>
                                    <td class="sh-pay-td sh-pay-td-right">
                                        <span @class([
                                            'sh-pay-status',
                                            'sh-pay-status-paid' => $entry['status'] === 'paid',
                                            'sh-pay-status-due' => in_array($entry['status'], ['invoiced', 'pending'], true),
                                            'sh-pay-status-upcoming' => $entry['status'] === 'recorded',
                                        ])>
                                            {{ ucfirst($entry['status']) }}
                                        </span>
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
