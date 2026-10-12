<?php

namespace App\Repositories\Site\Admin;

use App\Repositories\Site\Distribute\DistributePortalData;

class AdminPortalData
{
    /**
     * @return array<string, string>
     */
    public static function credentials(): array
    {
        return [
            'admin@smoovehub.com' => 'Admin2025!',
            'superadmin@smoovehub.com' => 'Super2025!',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function sellers(): array
    {
        return [
            ['id' => 'SLR-001', 'name' => 'HomeStyle Co.', 'email' => 'seller@homestyleco.com', 'phone' => '(214) 555-0101', 'city' => 'Dallas, TX', 'joined' => 'Jan 12, 2025', 'status' => 'Active', 'products' => 14, 'sales' => 38, 'revenue' => 42180, 'rating' => 4.9, 'stripe' => 'Connected', 'payout' => '$36,874', 'type' => 'Furniture Store'],
            ['id' => 'SLR-002', 'name' => 'OfficeFirst', 'email' => 'admin@officefirst.com', 'phone' => '(713) 555-0202', 'city' => 'Houston, TX', 'joined' => 'Feb 3, 2025', 'status' => 'Active', 'products' => 22, 'sales' => 61, 'revenue' => 58320, 'rating' => 4.8, 'stripe' => 'Connected', 'payout' => '$51,321', 'type' => 'Commercial'],
            ['id' => 'SLR-003', 'name' => 'Thrift Palace', 'email' => 'info@thriftpalace.com', 'phone' => '(404) 555-0303', 'city' => 'Atlanta, GA', 'joined' => 'Mar 7, 2025', 'status' => 'Active', 'products' => 87, 'sales' => 124, 'revenue' => 28400, 'rating' => 4.7, 'stripe' => 'Connected', 'payout' => '$24,992', 'type' => 'Thrift/Consignment'],
            ['id' => 'SLR-004', 'name' => 'LuxLiving', 'email' => 'lux@luxliving.com', 'phone' => '(305) 555-0404', 'city' => 'Miami, FL', 'joined' => 'Jan 28, 2025', 'status' => 'Active', 'products' => 9, 'sales' => 19, 'revenue' => 61200, 'rating' => 4.8, 'stripe' => 'Connected', 'payout' => '$53,856', 'type' => 'Luxury'],
            ['id' => 'SLR-005', 'name' => 'ApplianceHub TX', 'email' => 'sales@appliancetx.com', 'phone' => '(469) 555-0505', 'city' => 'Dallas, TX', 'joined' => 'Apr 1, 2025', 'status' => 'Active', 'products' => 31, 'sales' => 44, 'revenue' => 88400, 'rating' => 4.9, 'stripe' => 'Connected', 'payout' => '$77,792', 'type' => 'Appliances'],
            ['id' => 'SLR-006', 'name' => 'BizFurniture', 'email' => 'biz@bizfurniture.com', 'phone' => '(312) 555-0606', 'city' => 'Chicago, IL', 'joined' => 'Feb 19, 2025', 'status' => 'Pending', 'products' => 5, 'sales' => 0, 'revenue' => 0, 'rating' => 0, 'stripe' => 'Pending', 'payout' => '$0', 'type' => 'Commercial'],
            ['id' => 'SLR-007', 'name' => 'Budget Finds', 'email' => 'hello@budgetfinds.com', 'phone' => '(602) 555-0707', 'city' => 'Phoenix, AZ', 'joined' => 'Mar 22, 2025', 'status' => 'Suspended', 'products' => 0, 'sales' => 2, 'revenue' => 890, 'rating' => 3.1, 'stripe' => 'Connected', 'payout' => '$782', 'type' => 'Thrift'],
            ['id' => 'SLR-008', 'name' => 'NW Warehouse', 'email' => 'nw@nwwarehouse.com', 'phone' => '(206) 555-0808', 'city' => 'Seattle, WA', 'joined' => 'May 2, 2025', 'status' => 'Active', 'products' => 18, 'sales' => 27, 'revenue' => 34100, 'rating' => 4.6, 'stripe' => 'Connected', 'payout' => '$30,008', 'type' => 'Distribution'],
            ['id' => 'SLR-009', 'name' => 'DenverDesigns', 'email' => 'info@denverdesigns.com', 'phone' => '(720) 555-0909', 'city' => 'Denver, CO', 'joined' => 'Apr 14, 2025', 'status' => 'Active', 'products' => 11, 'sales' => 16, 'revenue' => 22400, 'rating' => 4.7, 'stripe' => 'Connected', 'payout' => '$19,712', 'type' => 'Furniture Store'],
            ['id' => 'SLR-010', 'name' => 'SouthCoast Furn.', 'email' => 'sc@southcoast.com', 'phone' => '(858) 555-1010', 'city' => 'San Diego, CA', 'joined' => 'May 8, 2025', 'status' => 'Pending', 'products' => 3, 'sales' => 0, 'revenue' => 0, 'rating' => 0, 'stripe' => 'Pending', 'payout' => '$0', 'type' => 'Residential'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function drivers(): array
    {
        return [
            ['id' => 'DRV-001', 'name' => 'Marcus Johnson', 'email' => 'marcus.j@crs.com', 'phone' => '(214) 555-1101', 'city' => 'Dallas, TX', 'joined' => 'Dec 5, 2024', 'status' => 'Active', 'company' => 'M&J Trucking LLC', 'mc' => 'MC-441821', 'dot' => 'DOT-882341', 'trucks' => 2, 'deliveries' => 187, 'rating' => 4.9, 'earnings' => '$24,180', 'payout' => 'ACH Chase', 'insurance' => 'Active', 'cdl' => true],
            ['id' => 'DRV-002', 'name' => 'Angela Torres', 'email' => 'angela.t@crs.com', 'phone' => '(713) 555-1202', 'city' => 'Houston, TX', 'joined' => 'Jan 14, 2025', 'status' => 'Active', 'company' => 'Torres Transport', 'mc' => 'MC-552930', 'dot' => 'DOT-991204', 'trucks' => 1, 'deliveries' => 143, 'rating' => 4.8, 'earnings' => '$18,420', 'payout' => 'ACH Wells Fargo', 'insurance' => 'Active', 'cdl' => true],
            ['id' => 'DRV-003', 'name' => 'Derek Williams', 'email' => 'd.williams@crs.com', 'phone' => '(404) 555-1303', 'city' => 'Atlanta, GA', 'joined' => 'Feb 2, 2025', 'status' => 'Active', 'company' => 'DW Freight Solutions', 'mc' => 'MC-663041', 'dot' => 'DOT-112398', 'trucks' => 3, 'deliveries' => 98, 'rating' => 4.7, 'earnings' => '$12,960', 'payout' => 'Stripe Connect', 'insurance' => 'Active', 'cdl' => true],
            ['id' => 'DRV-004', 'name' => 'Sofia Ramirez', 'email' => 'sofia.r@crs.com', 'phone' => '(305) 555-1404', 'city' => 'Miami, FL', 'joined' => 'Mar 9, 2025', 'status' => 'Active', 'company' => 'Ramirez Logistics', 'mc' => 'MC-774152', 'dot' => 'DOT-223409', 'trucks' => 1, 'deliveries' => 64, 'rating' => 4.9, 'earnings' => '$8,320', 'payout' => 'ACH Chase', 'insurance' => 'Active', 'cdl' => false],
            ['id' => 'DRV-005', 'name' => 'Brian Mitchell', 'email' => 'b.mitchell@crs.com', 'phone' => '(469) 555-1505', 'city' => 'Plano, TX', 'joined' => 'Jan 30, 2025', 'status' => 'In Transit', 'company' => 'BM Moving & Freight', 'mc' => 'MC-885263', 'dot' => 'DOT-334510', 'trucks' => 2, 'deliveries' => 211, 'rating' => 4.8, 'earnings' => '$27,430', 'payout' => 'Zelle', 'insurance' => 'Active', 'cdl' => true],
            ['id' => 'DRV-006', 'name' => 'Rachel Nguyen', 'email' => 'r.nguyen@crs.com', 'phone' => '(312) 555-1606', 'city' => 'Chicago, IL', 'joined' => 'Apr 4, 2025', 'status' => 'Active', 'company' => 'Nguyen Express', 'mc' => 'MC-996374', 'dot' => 'DOT-445621', 'trucks' => 1, 'deliveries' => 41, 'rating' => 4.6, 'earnings' => '$5,330', 'payout' => 'PayPal', 'insurance' => 'Active', 'cdl' => false],
            ['id' => 'DRV-007', 'name' => 'James Crawford', 'email' => 'j.crawford@crs.com', 'phone' => '(602) 555-1707', 'city' => 'Tempe, AZ', 'joined' => 'Feb 22, 2025', 'status' => 'Suspended', 'company' => 'Crawford Freight', 'mc' => 'MC-107485', 'dot' => 'DOT-556732', 'trucks' => 1, 'deliveries' => 18, 'rating' => 2.9, 'earnings' => '$2,340', 'payout' => 'ACH BofA', 'insurance' => 'Expired', 'cdl' => true],
            ['id' => 'DRV-008', 'name' => 'Keisha Brown', 'email' => 'k.brown@crs.com', 'phone' => '(206) 555-1808', 'city' => 'Tacoma, WA', 'joined' => 'Mar 17, 2025', 'status' => 'Active', 'company' => 'Brown & Co Delivery', 'mc' => 'MC-218596', 'dot' => 'DOT-667843', 'trucks' => 2, 'deliveries' => 76, 'rating' => 4.7, 'earnings' => '$9,880', 'payout' => 'ACH Chase', 'insurance' => 'Active', 'cdl' => true],
            ['id' => 'DRV-009', 'name' => 'Tony Vasquez', 'email' => 't.vasquez@crs.com', 'phone' => '(720) 555-1909', 'city' => 'Aurora, CO', 'joined' => 'Apr 28, 2025', 'status' => 'Pending', 'company' => 'Vasquez Trucking', 'mc' => 'MC-329607', 'dot' => 'DOT-778954', 'trucks' => 1, 'deliveries' => 0, 'rating' => 0, 'earnings' => '$0', 'payout' => 'Pending', 'insurance' => 'Active', 'cdl' => true],
            ['id' => 'DRV-010', 'name' => 'Lisa Park', 'email' => 'l.park@crs.com', 'phone' => '(858) 555-2010', 'city' => 'Chula Vista, CA', 'joined' => 'May 5, 2025', 'status' => 'Active', 'company' => 'Park Logistics Co.', 'mc' => 'MC-440718', 'dot' => 'DOT-889065', 'trucks' => 1, 'deliveries' => 29, 'rating' => 4.8, 'earnings' => '$3,770', 'payout' => 'Venmo', 'insurance' => 'Active', 'cdl' => false],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function customers(): array
    {
        return [
            ['id' => 'CST-001', 'name' => 'Maria Kowalski', 'email' => 'maria.k@email.com', 'phone' => '(214) 555-2101', 'city' => 'Dallas, TX', 'joined' => 'Jan 5, 2025', 'status' => 'Active', 'orders' => 7, 'spent' => '$4,280', 'last_order' => 'May 13, 2025', 'type' => 'Restaurant Owner'],
            ['id' => 'CST-002', 'name' => 'James Thompson', 'email' => 'j.thompson@email.com', 'phone' => '(305) 555-2202', 'city' => 'Miami, FL', 'joined' => 'Feb 10, 2025', 'status' => 'Active', 'orders' => 3, 'spent' => '$2,190', 'last_order' => 'May 10, 2025', 'type' => 'Interior Designer'],
            ['id' => 'CST-003', 'name' => 'Sandra Rivera', 'email' => 's.rivera@email.com', 'phone' => '(312) 555-2303', 'city' => 'Chicago, IL', 'joined' => 'Jan 20, 2025', 'status' => 'Active', 'orders' => 2, 'spent' => '$1,449', 'last_order' => 'May 8, 2025', 'type' => 'Homeowner'],
            ['id' => 'CST-004', 'name' => 'Tom Liu', 'email' => 'tom.l@email.com', 'phone' => '(415) 555-2404', 'city' => 'San Francisco, CA', 'joined' => 'Mar 4, 2025', 'status' => 'Active', 'orders' => 5, 'spent' => '$3,840', 'last_order' => 'May 16, 2025', 'type' => 'Property Manager'],
            ['id' => 'CST-005', 'name' => 'Rachel Adams', 'email' => 'r.adams@email.com', 'phone' => '(469) 555-2505', 'city' => 'Frisco, TX', 'joined' => 'Feb 28, 2025', 'status' => 'Active', 'orders' => 1, 'spent' => '$699', 'last_order' => 'May 14, 2025', 'type' => 'Homeowner'],
            ['id' => 'CST-006', 'name' => 'Carlos Mendez', 'email' => 'c.mendez@email.com', 'phone' => '(713) 555-2606', 'city' => 'Houston, TX', 'joined' => 'Apr 9, 2025', 'status' => 'Active', 'orders' => 4, 'spent' => '$6,120', 'last_order' => 'May 17, 2025', 'type' => 'Restaurant Owner'],
            ['id' => 'CST-007', 'name' => 'Jennifer Walsh', 'email' => 'j.walsh@email.com', 'phone' => '(720) 555-2707', 'city' => 'Denver, CO', 'joined' => 'Mar 22, 2025', 'status' => 'Active', 'orders' => 2, 'spent' => '$1,898', 'last_order' => 'May 5, 2025', 'type' => 'Homeowner'],
            ['id' => 'CST-008', 'name' => 'Alex Kim', 'email' => 'alex.kim@email.com', 'phone' => '(206) 555-2808', 'city' => 'Bellevue, WA', 'joined' => 'Jan 31, 2025', 'status' => 'Active', 'orders' => 6, 'spent' => '$8,340', 'last_order' => 'May 18, 2025', 'type' => 'Interior Designer'],
            ['id' => 'CST-009', 'name' => 'Priya Patel', 'email' => 'p.patel@email.com', 'phone' => '(404) 555-2909', 'city' => 'Atlanta, GA', 'joined' => 'Apr 14, 2025', 'status' => 'Active', 'orders' => 1, 'spent' => '$540', 'last_order' => 'Apr 20, 2025', 'type' => 'Homeowner'],
            ['id' => 'CST-010', 'name' => 'David Chen', 'email' => 'd.chen@email.com', 'phone' => '(602) 555-3010', 'city' => 'Scottsdale, AZ', 'joined' => 'May 1, 2025', 'status' => 'Active', 'orders' => 2, 'spent' => '$2,290', 'last_order' => 'May 15, 2025', 'type' => 'Office Manager'],
            ['id' => 'CST-011', 'name' => 'Laura Bennett', 'email' => 'l.bennett@email.com', 'phone' => '(858) 555-3111', 'city' => 'Carlsbad, CA', 'joined' => 'Feb 15, 2025', 'status' => 'Inactive', 'orders' => 0, 'spent' => '$0', 'last_order' => '—', 'type' => 'Homeowner'],
            ['id' => 'CST-012', 'name' => 'Marcus Freeman', 'email' => 'm.freeman@email.com', 'phone' => '(312) 555-3212', 'city' => 'Evanston, IL', 'joined' => 'Mar 3, 2025', 'status' => 'Active', 'orders' => 3, 'spent' => '$3,102', 'last_order' => 'May 11, 2025', 'type' => 'Restaurant Owner'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function distributors(): array
    {
        return [
            ['id' => 'ACME', 'name' => 'ACME Furniture Co.', 'location' => 'Dallas, TX', 'tier' => 'Enterprise', 'contact_email' => 'dist@acme.com', 'api_connected' => true],
            ['id' => 'PREMIER', 'name' => 'Premier Furniture Supply', 'location' => 'Houston, TX', 'tier' => 'Pro', 'contact_email' => 'admin@furniture.com', 'api_connected' => true],
            ['id' => 'NATIONAL', 'name' => 'National Wholesale Dist.', 'location' => 'Atlanta, GA', 'tier' => 'Pro', 'contact_email' => 'warehouse@nationalwholesale.com', 'api_connected' => false],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function companyBanks(): array
    {
        return [
            'ACME' => ['name' => 'ACME Furniture Co.', 'bank' => 'Chase Bank', 'acct' => 'XXXX8821', 'method' => 'ACH', 'holder' => 'ACME Furniture LLC'],
            'PREMIER' => ['name' => 'Premier Furniture Supply', 'bank' => 'Wells Fargo', 'acct' => 'XXXX4410', 'method' => 'ACH', 'holder' => 'Premier Supply Inc.'],
            'NATIONAL' => ['name' => 'National Wholesale Dist.', 'bank' => 'Bank of America', 'acct' => 'XXXX2299', 'method' => 'Stripe Connect', 'holder' => 'National Wholesale LLC'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function orderLedger(): array
    {
        return [
            [
                'ledger_id' => 'DL-A1B2C3', 'order_id' => 'SO-4K9MX', 'date_display' => 'May 17, 2025',
                'seller_name' => 'HomeStyle Co.', 'product_name' => 'Japandi Solid Oak Dining Table', 'qty' => 2,
                'source_company' => 'ACME Furniture Co.', 'source_id' => 'ACME', 'warehouse' => 'Dallas TX WH-A',
                'wholesale' => 620, 'total_paid' => 1822.80, 'status' => 'FULFILLED', 'payout_status' => 'PAID',
            ],
            [
                'ledger_id' => 'DL-B2C3D4', 'order_id' => 'SO-7M2NP', 'date_display' => 'May 16, 2025',
                'seller_name' => 'LuxLiving', 'product_name' => 'Velvet L-Shape Sectional', 'qty' => 1,
                'source_company' => 'Premier Furniture Supply', 'source_id' => 'PREMIER', 'warehouse' => 'Houston TX WH-B',
                'wholesale' => 980, 'total_paid' => 1029, 'status' => 'IN_TRANSIT', 'payout_status' => 'PROCESSING',
            ],
            [
                'ledger_id' => 'DL-C3D4E5', 'order_id' => 'SO-9K1QR', 'date_display' => 'May 15, 2025',
                'seller_name' => 'OfficeFirst', 'product_name' => 'Executive Standing Desk Pro', 'qty' => 3,
                'source_company' => 'Premier Furniture Supply', 'source_id' => 'PREMIER', 'warehouse' => 'Dallas TX WH-A',
                'wholesale' => 310, 'total_paid' => 975, 'status' => 'PENDING_FULFILLMENT', 'payout_status' => 'PENDING',
            ],
            [
                'ledger_id' => 'DL-D4E5F6', 'order_id' => 'SO-2L8ST', 'date_display' => 'May 14, 2025',
                'seller_name' => 'Thrift Palace', 'product_name' => 'King Bed Frame — Linen', 'qty' => 1,
                'source_company' => 'National Wholesale Dist.', 'source_id' => 'NATIONAL', 'warehouse' => 'Atlanta GA WH-C',
                'wholesale' => 540, 'total_paid' => 567, 'status' => 'FULFILLED', 'payout_status' => 'PAID',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function shopProducts(): array
    {
        return [
            ['id' => 101, 'name' => 'Modern Accent Chair', 'seller' => 'SmooveHub Official', 'category' => 'Living', 'condition' => 'BRAND_NEW', 'price' => 289, 'qty' => 12, 'status' => 'Active'],
            ['id' => 102, 'name' => 'Glass Coffee Table', 'seller' => 'HomeStyle Co.', 'category' => 'Living', 'condition' => 'BRAND_NEW', 'price' => 199, 'qty' => 8, 'status' => 'Active'],
            ['id' => 103, 'name' => 'Ergonomic Office Chair', 'seller' => 'OfficeFirst', 'category' => 'Office', 'condition' => 'BRAND_NEW', 'price' => 349, 'qty' => 24, 'status' => 'Active'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function fileRecords(): array
    {
        return [
            ['id' => 'FM-001', 'type' => 'Delivery Photo', 'order' => 'SH-4921', 'customer' => 'Maria Kowalski', 'date' => 'May 17, 2025', 'size' => '2.4 MB'],
            ['id' => 'FM-002', 'type' => 'Signed BOL', 'order' => 'SH-4918', 'customer' => 'Tom Liu', 'date' => 'May 16, 2025', 'size' => '890 KB'],
            ['id' => 'FM-003', 'type' => 'Customer ID', 'order' => 'SH-4915', 'customer' => 'Alex Kim', 'date' => 'May 15, 2025', 'size' => '1.1 MB'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function orders(): array
    {
        $dist = DistributePortalData::orders();

        return array_merge($dist, [
            ['id' => 'SH-4831', 'product' => 'Japandi Dining Table', 'qty' => 1, 'customer' => 'Maria K.', 'date' => 'May 17, 2025', 'status' => 'DELIVERED', 'total' => 1302, 'payout_status' => 'PAID'],
            ['id' => 'SH-4829', 'product' => 'Velvet Sectional', 'qty' => 1, 'customer' => 'Tom L.', 'date' => 'May 16, 2025', 'status' => 'IN_TRANSIT', 'total' => 1029, 'payout_status' => 'PROCESSING'],
            ['id' => 'SH-4826', 'product' => 'Executive Standing Desk', 'qty' => 2, 'customer' => 'Alex K.', 'date' => 'May 15, 2025', 'status' => 'DELIVERED', 'total' => 698, 'payout_status' => 'PAID'],
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function apiSections(): array
    {
        return [
            [
                'title' => 'SmooveHub Platform API',
                'icon' => '🏪',
                'color' => '#1B3C87',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/api/v1/sellers/list', 'desc' => 'List all sellers'],
                    ['method' => 'POST', 'path' => '/api/v1/orders/create', 'desc' => 'Create marketplace order'],
                    ['method' => 'GET', 'path' => '/api/v1/payout/status/:id', 'desc' => 'Get payout status'],
                ],
            ],
            [
                'title' => 'Distribution API',
                'icon' => '🏭',
                'color' => '#D97706',
                'endpoints' => [
                    ['method' => 'POST', 'path' => '/api/v1/inventory/create', 'desc' => 'Create inventory item'],
                    ['method' => 'GET', 'path' => '/api/v1/inventory/list', 'desc' => 'List distributor inventory'],
                ],
            ],
            [
                'title' => 'CRS / Delivery Webhooks',
                'icon' => '🚛',
                'color' => '#16A34A',
                'endpoints' => [
                    ['method' => 'WEBHOOK', 'path' => '/api/v1/crs/delivery-confirmed', 'desc' => 'Delivery confirmation trigger'],
                    ['method' => 'POST', 'path' => '/api/v1/delivery/update', 'desc' => 'Update delivery status'],
                ],
            ],
        ];
    }
}
