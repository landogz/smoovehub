<?php

namespace App\Repositories\Seller;

use App\Models\Catalog\Product;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use Illuminate\Support\Str;

class SellerProductRepository
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createForUser(User $user, array $data): array
    {
        /** @var SellerProfile|null $profile */
        $profile = $user->sellerProfile;

        if (! $profile) {
            abort(403, 'Seller profile not found.');
        }

        $markup = ($data['condition'] ?? '') === 'BRAND_NEW' ? 1.4 : 1.1;
        $listingPrice = (float) round(((float) $data['base_price']) * $markup, 2);
        $slug = $this->uniqueSlug(Str::slug($data['name']));

        $icons = [
            'COMMERCIAL' => '🏢',
            'RESIDENTIAL' => '🛋️',
            'APPLIANCES' => '🔌',
        ];

        $product = Product::query()->create([
            'seller_profile_id' => $profile->id,
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'condition' => $data['condition'],
            'internal_price' => $data['base_price'],
            'listing_price' => $listingPrice,
            'compare_at_price' => $listingPrice,
            'quantity' => $data['quantity'],
            'same_day_delivery' => (bool) ($data['same_day_delivery'] ?? false),
            'status' => 'active',
            'ownership_type' => 'seller',
        ]);

        $profile->increment('product_count');

        return [
            'id' => $product->uuid,
            'numeric_id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->listing_price,
            'original' => (float) $data['base_price'],
            'seller' => $profile->store_name,
            'condition' => $product->condition,
            'category' => $data['category'],
            'icon' => $icons[$data['category']] ?? '🛋️',
            'trending' => false,
            'same_day' => $product->same_day_delivery,
        ];
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base !== '' ? $base : 'product';
        $counter = 1;

        while (Product::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
