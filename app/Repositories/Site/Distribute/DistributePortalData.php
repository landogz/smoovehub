<?php

namespace App\Repositories\Site\Distribute;

class DistributePortalData
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function inventory(): array
    {
        return [
            [
                'id' => 'INV-001', 'sku' => 'SKU-JDT-001', 'upc' => '012345678901',
                'name' => 'Japandi Solid Oak Dining Table', 'brand' => 'LuxCraft',
                'company' => 'ACME Furniture Co.', 'company_id' => 'ACME',
                'category' => 'Dining', 'supplier_price' => 620, 'qty' => 18,
                'warehouse' => 'Dallas TX WH-A', 'eta' => '2–3 days', 'condition' => 'BRAND_NEW',
                'material' => 'Solid Oak', 'color' => 'Natural Walnut',
            ],
            [
                'id' => 'INV-006', 'sku' => 'SKU-MSS-006', 'upc' => '012345678906',
                'name' => 'Modular Sectional Sofa — Grey', 'brand' => 'LuxCraft',
                'company' => 'ACME Furniture Co.', 'company_id' => 'ACME',
                'category' => 'Living', 'supplier_price' => 1150, 'qty' => 9,
                'warehouse' => 'Dallas TX WH-A', 'eta' => '3–5 days', 'condition' => 'BRAND_NEW',
                'material' => 'Performance Fabric', 'color' => 'Charcoal Grey',
            ],
            [
                'id' => 'INV-007', 'sku' => 'SKU-CRT-007', 'upc' => '012345678907',
                'name' => 'Commercial Restaurant Table Set (4-Top)', 'brand' => 'LuxCraft',
                'company' => 'ACME Furniture Co.', 'company_id' => 'ACME',
                'category' => 'Commercial', 'supplier_price' => 480, 'qty' => 22,
                'warehouse' => 'Houston TX WH-B', 'eta' => '2–4 days', 'condition' => 'BRAND_NEW',
                'material' => 'Steel + Laminate', 'color' => 'Espresso',
            ],
            [
                'id' => 'INV-002', 'sku' => 'SKU-VLS-002', 'upc' => '012345678902',
                'name' => 'Velvet L-Shape Sectional', 'brand' => 'ComfyHome',
                'company' => 'Premier Furniture Supply', 'company_id' => 'PREMIER',
                'category' => 'Living', 'supplier_price' => 980, 'qty' => 7,
                'warehouse' => 'Houston TX WH-B', 'eta' => '3–5 days', 'condition' => 'BRAND_NEW',
                'material' => 'Velvet + Solid Wood Frame', 'color' => 'Charcoal',
            ],
            [
                'id' => 'INV-003', 'sku' => 'SKU-EXC-003', 'upc' => '012345678903',
                'name' => 'Executive Standing Desk Pro', 'brand' => 'WorkWell',
                'company' => 'Premier Furniture Supply', 'company_id' => 'PREMIER',
                'category' => 'Office', 'supplier_price' => 310, 'qty' => 34,
                'warehouse' => 'Dallas TX WH-A', 'eta' => '1–2 days', 'condition' => 'BRAND_NEW',
                'material' => 'Steel + MDF', 'color' => 'Matte Black',
            ],
            [
                'id' => 'INV-008', 'sku' => 'SKU-OFC-008', 'upc' => '012345678908',
                'name' => 'Ergonomic Mesh Executive Chair', 'brand' => 'WorkWell',
                'company' => 'Premier Furniture Supply', 'company_id' => 'PREMIER',
                'category' => 'Office', 'supplier_price' => 285, 'qty' => 48,
                'warehouse' => 'Dallas TX WH-A', 'eta' => '1–2 days', 'condition' => 'BRAND_NEW',
                'material' => 'Mesh + Aluminum', 'color' => 'Black/Silver',
            ],
            [
                'id' => 'INV-009', 'sku' => 'SKU-BST-009', 'upc' => '012345678909',
                'name' => '4-Drawer Lateral File Cabinet', 'brand' => 'WorkWell',
                'company' => 'Premier Furniture Supply', 'company_id' => 'PREMIER',
                'category' => 'Office', 'supplier_price' => 390, 'qty' => 16,
                'warehouse' => 'Houston TX WH-B', 'eta' => '2–3 days', 'condition' => 'BRAND_NEW',
                'material' => 'Cold-rolled steel', 'color' => 'Gunmetal',
            ],
            [
                'id' => 'INV-004', 'sku' => 'SKU-KBF-004', 'upc' => '012345678904',
                'name' => 'King Bed Frame — Linen Upholstered', 'brand' => 'RestWell',
                'company' => 'National Wholesale Dist.', 'company_id' => 'NATIONAL',
                'category' => 'Bedroom', 'supplier_price' => 540, 'qty' => 12,
                'warehouse' => 'Atlanta GA WH-C', 'eta' => '4–6 days', 'condition' => 'BRAND_NEW',
                'material' => 'Linen + Solid Wood + MDF', 'color' => 'Ivory Linen',
            ],
            [
                'id' => 'INV-005', 'sku' => 'SKU-RST-005', 'upc' => '012345678905',
                'name' => 'Restaurant Booth Set (4-seat, double-sided)', 'brand' => 'BizDine',
                'company' => 'National Wholesale Dist.', 'company_id' => 'NATIONAL',
                'category' => 'Commercial', 'supplier_price' => 740, 'qty' => 5,
                'warehouse' => 'Houston TX WH-B', 'eta' => '5–7 days', 'condition' => 'BRAND_NEW',
                'material' => 'Commercial-grade Vinyl + Steel', 'color' => 'Burgundy',
            ],
            [
                'id' => 'INV-010', 'sku' => 'SKU-NTB-010', 'upc' => '012345678910',
                'name' => '5-Drawer Bedroom Dresser', 'brand' => 'RestWell',
                'company' => 'National Wholesale Dist.', 'company_id' => 'NATIONAL',
                'category' => 'Bedroom', 'supplier_price' => 380, 'qty' => 19,
                'warehouse' => 'Atlanta GA WH-C', 'eta' => '3–5 days', 'condition' => 'BRAND_NEW',
                'material' => 'Solid Poplar + Plywood', 'color' => 'White Oak',
            ],
            [
                'id' => 'INV-011', 'sku' => 'SKU-APP-011', 'upc' => '012345678911',
                'name' => 'French Door Refrigerator — Stainless', 'brand' => 'RestWell',
                'company' => 'National Wholesale Dist.', 'company_id' => 'NATIONAL',
                'category' => 'Appliances', 'supplier_price' => 1280, 'qty' => 4,
                'warehouse' => 'Atlanta GA WH-C', 'eta' => '7–10 days', 'condition' => 'BRAND_NEW',
                'material' => 'Stainless Steel', 'color' => 'Brushed Steel',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function orders(): array
    {
        return [
            ['id' => 'SH-4921', 'product' => 'Japandi Solid Oak Dining Table', 'qty' => 2, 'customer' => 'HomeStyle Co.', 'date' => 'May 17, 2025', 'status' => 'DELIVERED', 'total' => 1302, 'payout_status' => 'PAID'],
            ['id' => 'SH-4918', 'product' => 'Velvet L-Shape Sectional', 'qty' => 1, 'customer' => 'LuxLiving', 'date' => 'May 16, 2025', 'status' => 'IN_TRANSIT', 'total' => 1029, 'payout_status' => 'PROCESSING'],
            ['id' => 'SH-4915', 'product' => 'Executive Standing Desk Pro', 'qty' => 3, 'customer' => 'OfficeFirst', 'date' => 'May 15, 2025', 'status' => 'PROCESSING', 'total' => 975, 'payout_status' => 'PENDING'],
            ['id' => 'SH-4910', 'product' => 'King Bed Frame — Linen', 'qty' => 1, 'customer' => 'Thrift Palace', 'date' => 'May 14, 2025', 'status' => 'DELIVERED', 'total' => 567, 'payout_status' => 'PAID'],
            ['id' => 'SH-4905', 'product' => 'Restaurant Booth Set (4-seat)', 'qty' => 2, 'customer' => 'BizFurniture', 'date' => 'May 12, 2025', 'status' => 'DELIVERED', 'total' => 1554, 'payout_status' => 'PAID'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function payouts(): array
    {
        return [
            ['id' => 'PAY-0041', 'date' => 'May 16, 2025', 'method' => 'ACH · Chase', 'amount' => 2871.00, 'status' => 'PAID', 'orders' => 3, 'ref' => 'ACH-991872'],
            ['id' => 'PAY-0038', 'date' => 'May 9, 2025', 'method' => 'ACH · Chase', 'amount' => 1554.00, 'status' => 'PAID', 'orders' => 2, 'ref' => 'ACH-987441'],
            ['id' => 'PAY-0035', 'date' => 'May 2, 2025', 'method' => 'Stripe Connect', 'amount' => 3108.50, 'status' => 'PAID', 'orders' => 4, 'ref' => 'SC-774321'],
            ['id' => 'PAY-0032', 'date' => 'Apr 25, 2025', 'method' => 'ACH · Chase', 'amount' => 1029.00, 'status' => 'PROCESSING', 'orders' => 1, 'ref' => 'ACH-983120'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function deliveryIntegrations(): array
    {
        return [
            ['carrier' => 'FedEx', 'status' => 'Connected', 'tracking_url' => 'https://fedex.com/tracking', 'api_key' => 'FX-XXXXX', 'color' => '#4D148C'],
            ['carrier' => 'UPS', 'status' => 'Connected', 'tracking_url' => 'https://ups.com/track', 'api_key' => 'UPS-XXXXX', 'color' => '#FFB500'],
            ['carrier' => 'DHL', 'status' => 'Pending', 'tracking_url' => 'https://dhl.com/tracking', 'api_key' => '', 'color' => '#D40511'],
            ['carrier' => 'Freight/LTL', 'status' => 'Connected', 'tracking_url' => '', 'api_key' => 'LTL-XXXXX', 'color' => '#0EA5E9'],
            ['carrier' => 'Local CRS', 'status' => 'Connected', 'tracking_url' => 'https://competitiverelocation.com', 'api_key' => 'CRS-XXXXX', 'color' => '#16A34A'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function demoAccounts(): array
    {
        return [
            'dist@acme.com' => [
                'pass' => 'Dist2025!',
                'company' => 'ACME Furniture Co.',
                'role' => 'Distribution Partner',
                'initials' => 'AF',
                'tier' => 'Enterprise',
            ],
            'admin@furniture.com' => [
                'pass' => 'Admin2025!',
                'company' => 'Premier Furniture Supply',
                'role' => 'Wholesale Distributor',
                'initials' => 'PF',
                'tier' => 'Pro',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function crsPricing(): array
    {
        return [
            'crs_base' => [
                'UNDER_25' => 75,
                'UNDER_50' => 120,
                'UNDER_100' => 190,
                'UNDER_200' => 290,
                'OVER_200' => 390,
            ],
            'fees' => [
                'FREE_STAIR_FLIGHTS' => 3,
                'STAIRS_OVER_FREE' => 75,
                'FREE_ELEV_FLOORS' => 3,
                'ELEV_OVER_FREE' => 75,
                'LONG_CARRY_FEE' => 100,
                'PROCESSING' => 0.03,
            ],
        ];
    }
}
