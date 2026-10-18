<?php

namespace App\Repositories\Seller;

use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use App\Services\Platform\MarkupCalculationService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SellerProductRepository
{
    public function __construct(
        private readonly MarkupCalculationService $markup,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listForUser(User $user): array
    {
        $profile = $this->requireProfile($user);

        return Product::query()
            ->with('category')
            ->where('seller_profile_id', $profile->id)
            ->latest()
            ->get()
            ->map(fn (Product $product) => $this->toCard($product, $profile))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createForUser(User $user, array $data): array
    {
        $profile = $this->requireProfile($user);

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

        return $this->toCard($product, $profile);
    }

    /**
     * @return array<string, mixed>
     */
    public function destroyForUser(User $user, string $uuid): array
    {
        $profile = $this->requireProfile($user);
        $product = Product::query()
            ->where('uuid', $uuid)
            ->where('seller_profile_id', $profile->id)
            ->first();

        if (! $product) {
            throw ValidationException::withMessages([
                'product_id' => ['Product was not found.'],
            ]);
        }

        $card = $this->toCard($product, $profile);
        $product->delete();
        $profile->decrement('product_count');

        return $card;
    }

    public function requireProfile(User $user): SellerProfile
    {
        $profile = $user->sellerProfile;

        if (! $profile) {
            abort(403, 'Seller profile not found.');
        }

        return $profile;
    }

    /**
     * @return array<string, mixed>
     */
    public function toCard(Product $product, SellerProfile $profile): array
    {
        $filterCategory = match ($product->category?->slug) {
            'commercial' => 'COMMERCIAL',
            'appliances' => 'APPLIANCES',
            default => 'RESIDENTIAL',
        };
        $seo = is_array($product->seo_meta) ? $product->seo_meta : [];

        return [
            'id' => $product->uuid,
            'numeric_id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->listing_price,
            'original' => (float) ($product->internal_price ?? 0),
            'seller' => $profile->store_name,
            'condition' => $product->condition,
            'category' => $filterCategory,
            'icon' => $seo['icon'] ?? '🛋️',
            'trending' => (bool) $product->is_trending,
            'same_day' => (bool) $product->same_day_delivery,
            'quantity' => (int) $product->quantity,
            'status' => $product->status,
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
