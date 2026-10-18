<?php

namespace Database\Seeders;

use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CatalogProductSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = [
            'HomeStyle Co.' => ['slug' => 'homestyle-co', 'email' => 'hello@homestyle.smoovehub.test'],
            'OfficeFirst' => ['slug' => 'officefirst', 'email' => 'sales@officefirst.smoovehub.test'],
            'LuxLiving' => ['slug' => 'luxliving', 'email' => 'studio@luxliving.smoovehub.test'],
            'Thrift Palace' => ['slug' => 'thrift-palace', 'email' => 'hello@thriftpalace.smoovehub.test'],
            'BizFurniture' => ['slug' => 'bizfurniture', 'email' => 'sales@bizfurniture.smoovehub.test'],
            'ApplianceHub TX' => ['slug' => 'appliancehub-tx', 'email' => 'sales@appliancehub.smoovehub.test'],
        ];

        $profiles = [];

        foreach ($sellers as $name => $meta) {
            $user = User::query()->firstOrCreate(
                ['email' => $meta['email']],
                [
                    'name' => $name,
                    'password' => Hash::make('Seller2026!'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            if (method_exists($user, 'assignRole') && ! $user->hasRole('seller')) {
                $user->assignRole('seller');
            }

            $profiles[$name] = SellerProfile::query()->firstOrCreate(
                ['store_slug' => $meta['slug']],
                [
                    'user_id' => $user->id,
                    'store_name' => $name,
                    'description' => $name.' storefront on SmooveHub.',
                    'business_type' => 'Furniture retailer',
                    'city' => 'Dallas',
                    'state' => 'TX',
                    'verification_status' => 'verified',
                    'member_since' => now(),
                ]
            );
        }

        $categories = Category::query()->pluck('id', 'slug');

        $items = [
            ['sku' => 'SH-RES-001', 'name' => 'Japandi Solid Oak Dining Table', 'price' => 1249, 'original' => 1780, 'seller' => 'HomeStyle Co.', 'condition' => 'BRAND_NEW', 'category' => 'residential', 'icon' => '🪑', 'trending' => true, 'same_day' => false],
            ['sku' => 'SH-COM-002', 'name' => 'Industrial Steel Office Desk Set', 'price' => 849, 'original' => 1200, 'seller' => 'OfficeFirst', 'condition' => 'BRAND_NEW', 'category' => 'commercial', 'icon' => '🖥️', 'trending' => false, 'same_day' => true],
            ['sku' => 'SH-RES-003', 'name' => 'Plush Velvet Sectional Sofa', 'price' => 2190, 'original' => 3100, 'seller' => 'LuxLiving', 'condition' => 'FLOOR_MODEL', 'category' => 'residential', 'icon' => '🛋️', 'trending' => true, 'same_day' => false],
            ['sku' => 'SH-RES-004', 'name' => 'Mid-Century Walnut Bookshelf', 'price' => 398, 'original' => 0, 'seller' => 'Thrift Palace', 'condition' => 'THRIFT', 'category' => 'residential', 'icon' => '📚', 'trending' => false, 'same_day' => false],
            ['sku' => 'SH-COM-005', 'name' => 'Commercial Restaurant Booth Set', 'price' => 1640, 'original' => 2200, 'seller' => 'BizFurniture', 'condition' => 'BRAND_NEW', 'category' => 'commercial', 'icon' => '🍽️', 'trending' => false, 'same_day' => true],
            ['sku' => 'SH-APP-006', 'name' => 'Smart Refrigerator French Door', 'price' => 2890, 'original' => 3400, 'seller' => 'ApplianceHub TX', 'condition' => 'BRAND_NEW', 'category' => 'appliances', 'icon' => '🧊', 'trending' => true, 'same_day' => false],
            ['sku' => 'SH-COM-007', 'name' => 'Adjustable Standing Desk Pro', 'price' => 699, 'original' => 900, 'seller' => 'OfficeFirst', 'condition' => 'REFURBISHED', 'category' => 'commercial', 'icon' => '💻', 'trending' => false, 'same_day' => true],
            ['sku' => 'SH-RES-008', 'name' => 'Minimalist Linen King Bed Frame', 'price' => 1099, 'original' => 1500, 'seller' => 'HomeStyle Co.', 'condition' => 'BRAND_NEW', 'category' => 'residential', 'icon' => '🛏️', 'trending' => false, 'same_day' => false],
            ['sku' => 'SH-COM-009', 'name' => 'Italian Leather Executive Chair', 'price' => 599, 'original' => 850, 'seller' => 'BizFurniture', 'condition' => 'FLOOR_MODEL', 'category' => 'commercial', 'icon' => '🪑', 'trending' => true, 'same_day' => true],
            ['sku' => 'SH-RES-010', 'name' => 'Farmhouse Entryway Bench', 'price' => 219, 'original' => 0, 'seller' => 'Thrift Palace', 'condition' => 'THRIFT', 'category' => 'residential', 'icon' => '🪵', 'trending' => false, 'same_day' => false],
            ['sku' => 'SH-APP-011', 'name' => 'Stainless Commercial Dishwasher', 'price' => 1880, 'original' => 2400, 'seller' => 'ApplianceHub TX', 'condition' => 'BRAND_NEW', 'category' => 'appliances', 'icon' => '🍽️', 'trending' => false, 'same_day' => false],
            ['sku' => 'SH-RES-012', 'name' => 'Biophilic Rattan Accent Chair', 'price' => 449, 'original' => 620, 'seller' => 'HomeStyle Co.', 'condition' => 'BRAND_NEW', 'category' => 'residential', 'icon' => '🌿', 'trending' => true, 'same_day' => true],
        ];

        foreach ($items as $item) {
            Product::query()->updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'seller_profile_id' => $profiles[$item['seller']]->id,
                    'category_id' => $categories[$item['category']] ?? null,
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'condition' => $item['condition'],
                    'listing_price' => $item['price'],
                    'compare_at_price' => $item['original'] > 0 ? $item['original'] : null,
                    'quantity' => 12,
                    'same_day_delivery' => $item['same_day'],
                    'is_trending' => $item['trending'],
                    'is_featured' => $item['trending'],
                    'ownership_type' => 'seller',
                    'status' => 'active',
                    'seo_meta' => ['icon' => $item['icon']],
                ]
            );
        }

        foreach ($profiles as $profile) {
            $profile->update([
                'product_count' => Product::query()->where('seller_profile_id', $profile->id)->count(),
            ]);
        }
    }
}
