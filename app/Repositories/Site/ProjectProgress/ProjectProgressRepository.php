<?php

namespace App\Repositories\Site\ProjectProgress;

class ProjectProgressRepository
{
    /**
     * Source of truth aligned with Docs/:
     * - smoovehub-platform-analysis.md
     * - smoovehub-developer-flowchart.md
     * - smoovehub-phase-1-functions.md
     *
     * @return array<string, mixed>
     */
    public function page(): array
    {
        return [
            'title' => 'Project Progress',
            'subtitle' => 'Roadmap and feature inventory aligned with the SmooveHub Docs prototype (Admin, Distribution, Member Dashboard, and public surfaces).',
            'client' => [
                'name' => 'Competitive Relocation Service',
                'project' => 'SmooveHub Platform Build',
                'ref' => 'SH-ROADMAP-2026',
            ],
            'docs_source' => 'Docs/smoovehub-platform-analysis.md + developer flowchart + phase-1 functions',
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
                    'summary' => 'Portal Sanctum login + role gates (admin / distribute / dashboard); API rate limits. Demo fill accounts kept.',
                    'status' => 'done',
                ],
                [
                    'id' => 2,
                    'name' => 'Phase 2 — Catalog & Orders',
                    'summary' => 'DB shop catalog, markup engine (B2B/B2C), trusted purchase with order items — Docs: The Shop + purchase flow.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 3,
                    'name' => 'Phase 3 — Stripe Payments',
                    'summary' => 'PaymentIntents/Checkout + Connect; fee breakdown (processing, CRS, coordination); webhook-confirmed paid only.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 4,
                    'name' => 'Phase 4 — Member Dashboard',
                    'summary' => 'Seller SPA APIs: Overview, Products, Orders, Payouts, Settings, Dist Catalog, Imports, Purchases, Onsite Appts, Open API.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 5,
                    'name' => 'Phase 5 — Distribution Portal',
                    'summary' => 'Live inventory/CSV/API upload, orders, payouts, delivery, CRS quote, analytics, settings, buy/sell API keys.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 6,
                    'name' => 'Phase 6 — Admin Portal',
                    'summary' => 'All 15 admin modules on live APIs + RBAC: sellers, shop products, drivers, customers, dist, orders, payouts, fulfillment, files, API hub.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 7,
                    'name' => 'Phase 7 — Delivery & Design',
                    'summary' => 'CRS quotes, GoShare webhooks, QR/tracking/waybills, driver onboarding, Design Room + Design Me APIs.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 8,
                    'name' => 'Phase 8 — CMS',
                    'summary' => 'Blog, city landings, About/Terms/Privacy from CMS; Ms. Dolores proxied server-side.',
                    'status' => 'upcoming',
                ],
                [
                    'id' => 9,
                    'name' => 'Phase 9 — Launch Hardening',
                    'summary' => 'Tests, mail/SMS, monitoring, retire prototype mocks, launch checklist.',
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
                    'phase_api' => 6,
                    'notes' => 'Docs §8 — all sidebar modules UI live; Sanctum + admin role. Production APIs in Phase 6.',
                    'modules' => [
                        ['key' => 'adm-overview', 'name' => 'Overview', 'detail' => 'Revenue, sellers, drivers, customers, CRS live stats', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-sellers', 'name' => 'Sellers', 'detail' => 'Approve, suspend, Stripe status, revenue', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-shopprods', 'name' => 'Shop Products', 'detail' => 'Admin-managed marketplace listings CRUD', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-drivers', 'name' => 'Drivers', 'detail' => 'MC/DOT, insurance, deliveries, payout method', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-customers', 'name' => 'Customers', 'detail' => 'Customer directory and order history', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-dist', 'name' => 'Distributors', 'detail' => 'Partner companies + inventory linkage', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-orders', 'name' => 'Orders', 'detail' => 'All marketplace orders', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-distorders', 'name' => 'Dist. Orders', 'detail' => 'Wholesale / sourcing order ledger', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-payouts', 'name' => 'Payouts', 'detail' => 'Cross-party payout monitoring (seller / dist / CRS)', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-delivery', 'name' => 'Delivery', 'detail' => 'In-transit shipments + carrier connections', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-fulfillment', 'name' => 'Fulfillment', 'detail' => 'Source routing (seller vs distributor vs SmooveHub)', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-analytics', 'name' => 'Analytics', 'detail' => 'Platform-wide KPIs', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-apihub', 'name' => 'API Hub', 'detail' => 'Endpoint documentation hub', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-filemanager', 'name' => 'File Manager', 'detail' => 'Photos, documents, contracts', 'status' => 'prototype', 'phase' => 6],
                        ['key' => 'adm-settings', 'name' => 'Settings', 'detail' => 'Platform configuration', 'status' => 'prototype', 'phase' => 6],
                    ],
                ],
                [
                    'name' => 'Distribution Portal',
                    'path' => '/distribute',
                    'audience' => 'Distribution partners',
                    'ui_status' => 'live',
                    'auth_status' => 'secured',
                    'data_status' => 'prototype',
                    'phase_api' => 5,
                    'notes' => 'Docs §3 — 10 tabs match prototype. Auth secured; mock/localStorage until Phase 5.',
                    'modules' => [
                        ['key' => 'overview', 'name' => 'Overview', 'detail' => 'KPIs, recent orders, inventory snapshot, CRS strip', 'status' => 'prototype', 'phase' => 5],
                        ['key' => 'inventory', 'name' => 'Inventory', 'detail' => 'SKU table with supplier / reseller pricing', 'status' => 'prototype', 'phase' => 5],
                        ['key' => 'upload', 'name' => 'Upload', 'detail' => 'CSV/XLSX, manual entry, Open API sync, template download', 'status' => 'prototype', 'phase' => 5],
                        ['key' => 'orders', 'name' => 'Orders', 'detail' => 'Reseller purchase orders from dist catalog', 'status' => 'prototype', 'phase' => 5],
                        ['key' => 'payout', 'name' => 'Payouts', 'detail' => 'ACH/card setup, history, CRS-triggered release', 'status' => 'prototype', 'phase' => 5],
                        ['key' => 'delivery', 'name' => 'Delivery', 'detail' => 'Carrier integrations + shipment tracking', 'status' => 'prototype', 'phase' => 5],
                        ['key' => 'dquote', 'name' => 'Delivery Quote', 'detail' => 'CRS-linked quote form (access details)', 'status' => 'prototype', 'phase' => 5],
                        ['key' => 'api', 'name' => 'API & Webhooks', 'detail' => '11 REST endpoints, OAuth, webhook config', 'status' => 'prototype', 'phase' => 5],
                        ['key' => 'analytics', 'name' => 'Analytics', 'detail' => 'Revenue, categories, top sellers', 'status' => 'prototype', 'phase' => 5],
                        ['key' => 'settings', 'name' => 'Settings', 'detail' => 'Company profile + markup settings', 'status' => 'prototype', 'phase' => 5],
                    ],
                ],
                [
                    'name' => 'Member Dashboard',
                    'path' => '/dashboard',
                    'audience' => 'Sellers / resellers / members',
                    'ui_status' => 'live',
                    'auth_status' => 'secured',
                    'data_status' => 'prototype',
                    'phase_api' => 4,
                    'notes' => 'Docs §9.2 — seller dashboard tabs. Seller sign-in required; full CRUD/APIs in Phase 4.',
                    'modules' => [
                        ['key' => 'overview', 'name' => 'Overview', 'detail' => 'Orders snapshot + activity feed', 'status' => 'prototype', 'phase' => 4],
                        ['key' => 'products', 'name' => 'Products', 'detail' => 'Listings CRUD + media upload', 'status' => 'prototype', 'phase' => 4],
                        ['key' => 'orders', 'name' => 'Orders', 'detail' => 'Customer orders', 'status' => 'prototype', 'phase' => 4],
                        ['key' => 'payouts', 'name' => 'Payouts', 'detail' => 'Friday payout policy + bank / Stripe Connect', 'status' => 'prototype', 'phase' => 4],
                        ['key' => 'settings', 'name' => 'Settings', 'detail' => 'Store profile + branding', 'status' => 'prototype', 'phase' => 4],
                        ['key' => 'distcatalog', 'name' => 'Distribution Catalog', 'detail' => 'Browse / import distributor SKUs', 'status' => 'prototype', 'phase' => 4],
                        ['key' => 'myimports', 'name' => 'My Imports', 'detail' => 'Imported distribution listings', 'status' => 'prototype', 'phase' => 4],
                        ['key' => 'mypurchases', 'name' => 'My Purchases', 'detail' => 'Distribution purchase history', 'status' => 'prototype', 'phase' => 4],
                        ['key' => 'onsiteappts', 'name' => 'Onsite Appointments', 'detail' => 'Customer visit requests (not on dist portal)', 'status' => 'prototype', 'phase' => 4],
                        ['key' => 'openapi', 'name' => 'Open API', 'detail' => 'GoShare samples, distributor quick-connect', 'status' => 'prototype', 'phase' => 4],
                    ],
                ],
            ],
            'surfaces' => [
                ['name' => 'Home', 'path' => '/', 'detail' => 'Hero, categories, featured, CRS CTA, newsletter', 'status' => 'prototype', 'phase' => 2],
                ['name' => 'The Shop', 'path' => '/shop', 'detail' => 'Catalog grid, filters, purchase modal', 'status' => 'prototype', 'phase' => 2],
                ['name' => 'Sell / Onboarding', 'path' => '/sell', 'detail' => 'Seller signup CTA + wizard', 'status' => 'prototype', 'phase' => 4],
                ['name' => 'Sellers Directory', 'path' => '/sellers', 'detail' => 'Reseller directory', 'status' => 'prototype', 'phase' => 4],
                ['name' => 'Seller Storefront', 'path' => '/store/{slug}', 'detail' => 'Branded B2C store page', 'status' => 'prototype', 'phase' => 4],
                ['name' => 'Delivery Quote', 'path' => '/quote', 'detail' => 'CRS multi-step quote wizard', 'status' => 'prototype', 'phase' => 7],
                ['name' => 'Driver Sign-up', 'path' => '/drive', 'detail' => 'Multi-step wizard; login via CRS CRM', 'status' => 'prototype', 'phase' => 7],
                ['name' => 'Design Your Room', 'path' => '/design-room', 'detail' => 'Canvas, camera/upload, catalog place, save/share', 'status' => 'prototype', 'phase' => 7],
                ['name' => 'Design Me', 'path' => '/design-me', 'detail' => 'Custom furniture quote builder', 'status' => 'prototype', 'phase' => 7],
                ['name' => 'About / Contact', 'path' => '/about · /contact', 'detail' => 'Company story + contact forms', 'status' => 'prototype', 'phase' => 8],
                ['name' => 'Legal', 'path' => '/terms · /privacy', 'detail' => 'Seller/customer terms + privacy', 'status' => 'prototype', 'phase' => 8],
                ['name' => 'Blog & Cities', 'path' => '/blog · /cities', 'detail' => 'CMS content + local SEO landings', 'status' => 'prototype', 'phase' => 8],
                ['name' => 'QR / Tracking / Waybills', 'path' => '(modals)', 'detail' => 'Product QR, timeline, onsite, printable waybill', 'status' => 'prototype', 'phase' => 7],
                ['name' => 'Stripe Checkout', 'path' => '(purchase modal)', 'detail' => 'Fee breakdown + PaymentIntent/Connect', 'status' => 'upcoming', 'phase' => 3],
                ['name' => 'GoShare + CRS Webhooks', 'path' => '/api/v1/…', 'detail' => 'Job create + delivery webhook → payouts', 'status' => 'upcoming', 'phase' => 7],
                ['name' => 'Ms. Dolores AI', 'path' => '(widget)', 'detail' => 'Chat assistant; server-proxied Anthropic', 'status' => 'prototype', 'phase' => 8],
                ['name' => 'Markup Engine', 'path' => '(pricing)', 'detail' => 'B2B reseller + B2C backend markups (Docs Blueprint)', 'status' => 'upcoming', 'phase' => 2],
                ['name' => 'Payout Split Engine', 'path' => '(webhooks)', 'detail' => 'Seller + distributor + CRS fee on DELIVERED', 'status' => 'upcoming', 'phase' => 5],
                ['name' => 'Under Maintenance', 'path' => '/maintenance', 'detail' => 'SITE_MAINTENANCE env toggle; marketplace 503. Client payment + progress stay open.', 'status' => 'production', 'phase' => 9],
            ],
            'history' => [
                [
                    'date' => '2026-10-18',
                    'title' => 'Partial payment received',
                    'detail' => '$200 partial payment marked paid on /client-payment (PAY-90102).',
                    'status' => 'shipped',
                ],
                [
                    'date' => '2026-10-15',
                    'title' => 'Under-maintenance page',
                    'detail' => 'Added SITE_MAINTENANCE env flag. Marketplace/portals/APIs return 503; client payment and project progress stay reachable.',
                    'status' => 'shipped',
                ],
                [
                    'date' => '2026-10-13',
                    'title' => 'Progress aligned to Docs inventory',
                    'detail' => 'Mapped Admin (15), Distribution (10), Member Dashboard (10) modules plus public surfaces from Docs platform analysis / flowchart / functions.',
                    'status' => 'shipped',
                ],
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
