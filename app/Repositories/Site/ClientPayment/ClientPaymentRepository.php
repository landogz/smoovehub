<?php

namespace App\Repositories\Site\ClientPayment;

class ClientPaymentRepository
{
    /**
     * @return array<string, mixed>
     */
    public function page(): array
    {
        return [
            'title' => 'Client Payment',
            'subtitle' => 'SmooveHub project payment milestones and transaction history.',
            'client' => [
                'name' => 'Competitive Relocation Service',
                'project' => 'SmooveHub Platform Build',
                'invoice_ref' => 'SH-2026-001',
            ],
            'milestones' => [
                [
                    'name' => 'Milestone 1 — Kickoff',
                    'trigger' => 'Contract signed; project start',
                    'amount' => 2500,
                    'percent' => 30,
                    'status' => 'due',
                ],
                [
                    'name' => 'Milestone 2 — Mid-point',
                    'trigger' => 'Phase 3 API delivery & review',
                    'amount' => 2900,
                    'percent' => 40,
                    'status' => 'upcoming',
                ],
                [
                    'name' => 'Milestone 3 — Final',
                    'trigger' => 'Phase 6 sign-off & handover',
                    'amount' => 1600,
                    'percent' => 30,
                    'status' => 'upcoming',
                ],
            ],
            'total_amount' => 7000,
            'total_percent' => 100,
            'history' => [
                [
                    'date' => '2026-10-18',
                    'reference' => 'PAY-90102',
                    'milestone' => 'Milestone 2 — Mid-point',
                    'method' => 'Partial payment',
                    'amount' => 200,
                    'status' => 'paid',
                ],
            ],
            'summary' => [
                'paid' => 200,
                'pending' => 0,
                'outstanding' => 6800,
                'currency' => 'USD',
            ],
        ];
    }
}
