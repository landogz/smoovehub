<?php

namespace App\Repositories\Catalog;

use App\Models\Catalog\Product;
use Illuminate\Support\Collection;

class CatalogRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function storefront(): array
    {
        return $this->activeProducts()
            ->map(fn (Product $product) => $this->toStorefrontCard($product))
            ->values()
            ->all();
    }

    public function findActiveByUuid(string $uuid): ?Product
    {
        return Product::query()
            ->with(['category', 'sellerProfile'])
            ->where('uuid', $uuid)
            ->where('status', 'active')
            ->first();
    }

    /**
     * @return Collection<int, Product>
     */
    public function activeProducts(): Collection
    {
        return Product::query()
            ->with(['category', 'sellerProfile'])
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->orderByDesc('is_featured')
            ->orderByDesc('is_trending')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function conditionLabels(): array
    {
        return [
            'BRAND_NEW' => ['label' => 'New', 'class' => 'sh-badge-blue'],
            'THRIFT' => ['label' => 'Thrift', 'class' => 'sh-badge-amber'],
            'FLOOR_MODEL' => ['label' => 'Floor', 'class' => 'sh-badge-slate'],
            'REFURBISHED' => ['label' => 'Refurb', 'class' => 'sh-badge-green'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toStorefrontCard(Product $product): array
    {
        $categorySlug = strtoupper(str_replace('-', '_', (string) $product->category?->slug));
        $filterCategory = match ($product->category?->slug) {
            'commercial' => 'COMMERCIAL',
            'residential' => 'RESIDENTIAL',
            'appliances' => 'APPLIANCES',
            default => $categorySlug !== '' ? $categorySlug : 'RESIDENTIAL',
        };

        $seo = is_array($product->seo_meta) ? $product->seo_meta : [];

        return [
            'id' => $product->uuid,
            'name' => $product->name,
            'price' => (float) $product->listing_price,
            'original' => (float) ($product->compare_at_price ?? 0),
            'seller' => $product->sellerProfile?->store_name ?? 'SmooveHub',
            'condition' => $product->condition,
            'category' => $filterCategory,
            'icon' => $seo['icon'] ?? ($product->category?->icon ?? '🛋️'),
            'trending' => (bool) $product->is_trending,
            'same_day' => (bool) $product->same_day_delivery,
            'sku' => $product->sku,
            'quantity' => (int) $product->quantity,
        ];
    }
}
