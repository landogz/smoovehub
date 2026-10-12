<?php

namespace App\Repositories\Site\Admin;

use App\Repositories\Site\Distribute\DistributePortalData;

class AdminPortalRepository
{
    /**
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        return [
            'nav_sections' => [
                [
                    'label' => 'Overview',
                    'items' => [
                        ['key' => 'adm-overview', 'label' => '📊 Overview'],
                    ],
                ],
                [
                    'label' => 'People',
                    'items' => [
                        ['key' => 'adm-sellers', 'label' => '🏪 Sellers'],
                        ['key' => 'adm-shopprods', 'label' => '🛍 Shop Products', 'badge' => 'ADMIN'],
                        ['key' => 'adm-drivers', 'label' => '🚛 Drivers'],
                        ['key' => 'adm-customers', 'label' => '👥 Customers'],
                        ['key' => 'adm-dist', 'label' => '🏭 Distributors'],
                    ],
                ],
                [
                    'label' => 'Operations',
                    'items' => [
                        ['key' => 'adm-orders', 'label' => '📦 Orders'],
                        ['key' => 'adm-distorders', 'label' => '🏭 Dist. Orders'],
                        ['key' => 'adm-payouts', 'label' => '💰 Payouts'],
                        ['key' => 'adm-delivery', 'label' => '🚚 Delivery'],
                        ['key' => 'adm-fulfillment', 'label' => '📦 Fulfillment', 'badge' => 'SOURCE', 'badge_color' => 'orange'],
                    ],
                ],
                [
                    'label' => 'System',
                    'items' => [
                        ['key' => 'adm-analytics', 'label' => '📈 Analytics'],
                        ['key' => 'adm-apihub', 'label' => '🔌 API Hub', 'badge' => 'ALL', 'badge_color' => 'orange'],
                        ['key' => 'adm-filemanager', 'label' => '🗂 File Manager', 'badge' => 'NEW', 'badge_color' => 'purple'],
                        ['key' => 'adm-settings', 'label' => '⚙️ Settings'],
                    ],
                ],
            ],
            'credentials' => AdminPortalData::credentials(),
            'sellers' => AdminPortalData::sellers(),
            'drivers' => AdminPortalData::drivers(),
            'customers' => AdminPortalData::customers(),
            'distributors' => AdminPortalData::distributors(),
            'company_banks' => AdminPortalData::companyBanks(),
            'order_ledger' => AdminPortalData::orderLedger(),
            'shop_products' => AdminPortalData::shopProducts(),
            'file_records' => AdminPortalData::fileRecords(),
            'orders' => AdminPortalData::orders(),
            'payouts' => DistributePortalData::payouts(),
            'inventory' => DistributePortalData::inventory(),
            'delivery_integrations' => DistributePortalData::deliveryIntegrations(),
            'api_sections' => AdminPortalData::apiSections(),
            'crm_url' => 'https://competitiverelocationcrm.com/auth/login',
            'home_url' => route('site.home'),
            'distribute_url' => route('site.distribute'),
        ];
    }
}
