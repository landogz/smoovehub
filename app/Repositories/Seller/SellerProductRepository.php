<?php

namespace App\Repositories\Seller;

use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use App\Services\Platform\MarkupCalculationService;
use Illuminate\Support\Str;

class SellerProductRepository
{
    public function __construct(
        private readonly MarkupCalculationService $markup,
    ) {}

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

        $basePrice = (float) $data['base_price'];
        $listingPrice = $this->markup->listingPrice('seller', (string) $data['condition'], $basePrice);
        $slug = $this->uniqueSlug(Str::slug($data['name']));
        $categorySlug = strtolower((string) $data['category']);
        $categoryId = Category::query()->where('slug', $categorySlug)->value('id');

        $icons = [
            'COMMERCIAL' => '🏢',
            'RESIDENTIAL' => '🛋️',
            'APPLIANCES' => '🔌',
        ];

        $product = Product::query()->create([
            'seller_profile_id' => $profile->id,
            'category_id' => $categoryId,
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'condition' => $data['condition'],
            'internal_price' => $basePrice,
            'listing_price' => $listingPrice,
            'compare_at_price' => $listingPrice,
            'quantity' => $data['quantity'],
            'same_day_delivery' => (bool) ($data['same_day_delivery'] ?? false),
            'status' => 'active',
            'ownership_type' => 'seller',
            'seo_meta' => ['icon' => $icons[$data['category']] ?? '🛋️'],
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
