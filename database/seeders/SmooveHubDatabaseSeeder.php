<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SmooveHubDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full platform access'],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Admin portal access'],
            ['name' => 'Customer', 'slug' => 'customer', 'description' => 'Marketplace buyer'],
            ['name' => 'Seller', 'slug' => 'seller', 'description' => 'Reseller / seller dashboard'],
            ['name' => 'Distribution Partner', 'slug' => 'distribution-partner', 'description' => 'Distribution company portal'],
            ['name' => 'Driver', 'slug' => 'driver', 'description' => 'CRS-linked delivery driver'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'uuid' => (string) Str::uuid(),
                'name' => $role['name'],
                'slug' => $role['slug'],
                'guard_name' => 'web',
                'description' => $role['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $categories = [
            ['name' => 'Commercial', 'slug' => 'commercial', 'icon' => '🏢', 'sort_order' => 1],
            ['name' => 'Residential', 'slug' => 'residential', 'icon' => '🏠', 'sort_order' => 2],
            ['name' => 'Appliances', 'slug' => 'appliances', 'icon' => '🔌', 'sort_order' => 3],
            ['name' => 'Brand New', 'slug' => 'brand-new', 'icon' => '✨', 'sort_order' => 4],
            ['name' => 'Thrift Finds', 'slug' => 'thrift-finds', 'icon' => '💎', 'sort_order' => 5],
            ['name' => 'Same-Day', 'slug' => 'same-day', 'icon' => '⚡', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'uuid' => (string) Str::uuid(),
                'name' => $category['name'],
                'slug' => $category['slug'],
                'icon' => $category['icon'],
                'sort_order' => $category['sort_order'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $markupRules = [
            ['name' => 'SmooveHub Brand New Markup', 'rule_key' => 'sh_brand_new', 'rate' => 0.4000, 'applies_to' => 'smoovehub_brand_new'],
            ['name' => 'SmooveHub Thrift/Floor Markup', 'rule_key' => 'sh_thrift_floor', 'rate' => 0.1000, 'applies_to' => 'smoovehub_thrift_floor'],
            ['name' => 'Distribution Reseller Markup', 'rule_key' => 'dist_reseller', 'rate' => 0.0500, 'applies_to' => 'distribution_catalog'],
            ['name' => 'Third-Party Seller Listing Markup', 'rule_key' => 'seller_listing', 'rate' => 0.1000, 'applies_to' => 'seller_listing'],
            ['name' => 'Stripe Processing Fee', 'rule_key' => 'stripe_processing', 'rate' => 0.0300, 'applies_to' => 'checkout'],
            ['name' => 'Delivery Coordination Fee', 'rule_key' => 'delivery_coordination', 'rate' => 0.0300, 'applies_to' => 'checkout'],
            ['name' => 'CRS Delivery Fee Share', 'rule_key' => 'crs_delivery_share', 'rate' => 0.0300, 'applies_to' => 'crs_payout'],
        ];

        foreach ($markupRules as $rule) {
            DB::table('markup_rules')->insert([
                'uuid' => (string) Str::uuid(),
                'name' => $rule['name'],
                'rule_key' => $rule['rule_key'],
                'rate' => $rule['rate'],
                'applies_to' => $rule['applies_to'],
                'description' => 'Platform Blueprint v1.0 — configurable B2B/B2C engine',
                'is_active' => true,
                'effective_from' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $carriers = [
            ['carrier' => 'crs', 'display_name' => 'Competitive Relocation Service'],
            ['carrier' => 'goshare', 'display_name' => 'GoShare'],
        ];

        foreach ($carriers as $carrier) {
            DB::table('carrier_integrations')->insert([
                'uuid' => (string) Str::uuid(),
                'carrier' => $carrier['carrier'],
                'display_name' => $carrier['display_name'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $cmsPages = [
            ['slug' => 'terms', 'title' => 'Terms of Service', 'page_type' => 'legal'],
            ['slug' => 'privacy', 'title' => 'Privacy Policy', 'page_type' => 'legal'],
            ['slug' => 'about', 'title' => 'About Us', 'page_type' => 'page'],
            ['slug' => 'blueprint', 'title' => 'Platform Blueprint', 'page_type' => 'blueprint'],
            ['slug' => 'contact', 'title' => 'Contact', 'page_type' => 'page'],
        ];

        foreach ($cmsPages as $page) {
            DB::table('cms_pages')->insert([
                'uuid' => (string) Str::uuid(),
                'slug' => $page['slug'],
                'title' => $page['title'],
                'page_type' => $page['page_type'],
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $cities = [
            ['name' => 'Dallas', 'slug' => 'dallas-tx', 'state' => 'TX'],
            ['name' => 'New York', 'slug' => 'new-york-ny', 'state' => 'NY'],
            ['name' => 'Houston', 'slug' => 'houston-tx', 'state' => 'TX'],
            ['name' => 'Miami', 'slug' => 'miami-fl', 'state' => 'FL'],
            ['name' => 'Chicago', 'slug' => 'chicago-il', 'state' => 'IL'],
        ];

        foreach ($cities as $city) {
            DB::table('city_landing_pages')->insert([
                'uuid' => (string) Str::uuid(),
                'name' => $city['name'],
                'slug' => $city['slug'],
                'state' => $city['state'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $settings = [
            ['group' => 'platform', 'key' => 'support_phone', 'value' => json_encode('877-385-2919')],
            ['group' => 'platform', 'key' => 'support_email', 'value' => json_encode('support@smoovehub.com')],
            ['group' => 'platform', 'key' => 'crs_email', 'value' => json_encode('love@competitiverelocation.com')],
            ['group' => 'delivery', 'key' => 'distance_tier_auto_miles', 'value' => json_encode(50)],
            ['group' => 'delivery', 'key' => 'distance_tier_crm_miles', 'value' => json_encode(100)],
            ['group' => 'delivery', 'key' => 'distance_tier_validation_miles', 'value' => json_encode(200)],
        ];

        foreach ($settings as $setting) {
            DB::table('platform_settings')->insert([
                'group' => $setting['group'],
                'key' => $setting['key'],
                'value' => $setting['value'],
                'is_encrypted' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
