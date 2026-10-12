<?php

namespace App\Repositories\Site\ProjectProgress;

class ProjectProgressRepository
{
    /**
     * @return array<string, mixed>
     */
    public function page(): array
    {
        return [
            'title' => 'Project Progress',
            'subtitle' => 'Track SmooveHub platform build phases from foundation through launch.',
            'client' => [
                'name' => 'Competitive Relocation Service',
                'project' => 'SmooveHub Platform Build',
                'ref' => 'SH-ROADMAP-2026',
            ],
            'phases' => [
                [
                    'id' => 0,
                    'name' => 'Phase 0 — Foundation',
                    'summary' => 'Installer, site shell, Sanctum auth APIs, Vite build, IONOS deploy.',
                    'status' => 'done',
                ],
                [
                    'id' => 1,
                    'name' => 'Phase 1 — Security',
                    'summary' => 'Portal Sanctum login + role gates for admin/distribute/dashboard; API rate limits. Demo accounts kept for fill-demo.',
                    'status' => 'done',
                ],
                [
                    'id' => 2,
                    'name' => 'Phase 2 — Catalog & Orders',
                    'summary' => 'DB-backed shop catalog, trusted purchase with order items.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 3,
                    'name' => 'Phase 3 — Stripe Payments',
                    'summary' => 'PaymentIntents/Checkout + webhooks; never mark paid without confirmation.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 4,
                    'name' => 'Phase 4 — Member Dashboard',
                    'summary' => 'Seller/member dashboard: product CRUD, orders, settings, media upload (full API).',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 5,
                    'name' => 'Phase 5 — Distribution Portal',
                    'summary' => 'Distribution portal: real inventory, orders, payouts, and API keys against DB.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 6,
                    'name' => 'Phase 6 — Admin Portal',
                    'summary' => 'Admin portal: live APIs + RBAC; replace mock admin data.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 7,
                    'name' => 'Phase 7 — Delivery & Design',
                    'summary' => 'Persist quotes/contact, deliveries, Design Room API, driver review.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 8,
                    'name' => 'Phase 8 — CMS',
                    'summary' => 'Blog, cities, and legal pages served from CMS tables.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 9,
                    'name' => 'Phase 9 — Launch Hardening',
                    'summary' => 'Tests, mail, monitoring, retire prototype mocks, launch checklist.',
                    'status' => 'upcoming',
                ],
            ],
            'portals' => [
                [
                    'name' => 'Admin Portal',
                    'path' => '/admin',
                    'audience' => 'Platform admins & super-admins',
                    'ui_status' => 'live',
                    'auth_status' => 'secured',
                    'data_status' => 'prototype',
                    'notes' => 'UI live; Sanctum + admin role required. Mock data until Phase 6.',
                ],
                [
                    'name' => 'Distribution Portal',
                    'path' => '/distribute',
                    'audience' => 'Distribution partners',
                    'ui_status' => 'live',
                    'auth_status' => 'secured',
                    'data_status' => 'prototype',
                    'notes' => 'UI live; Sanctum + distribution-partner role required. Mock/localStorage until Phase 5.',
                ],
                [
                    'name' => 'Member Dashboard',
                    'path' => '/dashboard',
                    'audience' => 'Sellers / members',
                    'ui_status' => 'live',
                    'auth_status' => 'secured',
                    'data_status' => 'prototype',
                    'notes' => 'Seller dashboard UI live; seller sign-in required. Full CRUD/APIs in Phase 4.',
                ],
            ],
            'history' => [
                [
                    'date' => '2026-10-13',
                    'title' => 'Portal tracker on progress page',
                    'detail' => 'Documented Admin Portal, Distribution Portal, and Member Dashboard status (UI live, auth secured, data still prototype).',
                    'status' => 'shipped',
                ],
                [
                    'date' => '2026-10-13',
                    'title' => 'Phase 1 — Security',
                    'detail' => 'Admin/distribute/dashboard now authenticate via Sanctum with role checks. Demo fill accounts kept. API throttle registered.',
                    'status' => 'shipped',
                ],
                [
                    'date' => '2026-10-12',
                    'title' => 'Public progress tracker',
                    'detail' => 'Added /project-progress so the client can follow roadmap status.',
                    'status' => 'shipped',
                ],
                [
                    'date' => '2026-10-12',
                    'title' => 'Client payment page',
                    'detail' => 'Public /client-payment with milestone schedule and payment history.',
                    'status' => 'shipped',
                ],
                [
                    'date' => '2026-10-12',
                    'title' => 'Installer + IONOS live',
                    'detail' => 'Web installer, GitHub repo, and live host at smoovehub.competitiverelocationcrm.com.',
                    'status' => 'shipped',
                ],
            ],
        ];
    }
}
