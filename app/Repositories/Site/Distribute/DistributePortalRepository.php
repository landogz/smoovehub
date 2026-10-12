<?php

namespace App\Repositories\Site\Distribute;

class DistributePortalRepository
{
    /**
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        return [
            'nav_sections' => [
                [
                    'label' => 'Main',
                    'items' => [
                        ['key' => 'overview', 'label' => '📊 Overview'],
                        ['key' => 'inventory', 'label' => '🏭 Inventory'],
                        ['key' => 'upload', 'label' => '📤 Upload'],
                        ['key' => 'orders', 'label' => '📋 Orders'],
                    ],
                ],
                [
                    'label' => 'Finance',
                    'items' => [
                        ['key' => 'payout', 'label' => '💰 Payouts'],
                    ],
                ],
                [
                    'label' => 'Logistics',
                    'items' => [
                        ['key' => 'delivery', 'label' => '🚛 Delivery'],
                        ['key' => 'dquote', 'label' => '📦 Delivery Quote', 'badge' => 'CRS'],
                    ],
                ],
                [
                    'label' => 'Developer',
                    'items' => [
                        ['key' => 'api', 'label' => '🔌 API & Webhooks'],
                        ['key' => 'analytics', 'label' => '📈 Analytics'],
                        ['key' => 'settings', 'label' => '⚙️ Settings'],
                    ],
                ],
            ],
            'company' => [
                'name' => 'ACME Furniture Co.',
                'tier' => 'Enterprise',
            ],
            'inventory' => DistributePortalData::inventory(),
            'orders' => DistributePortalData::orders(),
            'payouts' => DistributePortalData::payouts(),
            'delivery_integrations' => DistributePortalData::deliveryIntegrations(),
            'demo_accounts' => DistributePortalData::demoAccounts(),
            'crs_pricing' => DistributePortalData::crsPricing(),
            'api_base_url' => 'https://api.smoovehub.com',
            'crm_url' => 'https://competitiverelocationcrm.com/auth/login',
            'home_url' => route('site.home'),
            'categories' => ['Dining', 'Living', 'Bedroom', 'Office', 'Commercial', 'Appliances', 'Outdoor', 'Other'],
        ];
    }
}
