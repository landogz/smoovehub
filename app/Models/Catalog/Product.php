<?php

namespace App\Models\Catalog;

use App\Models\Concerns\HasUuid;
use App\Models\Distribution\DistributionCatalogImport;
use App\Models\Distribution\DistributionInventory;
use App\Models\Order\OrderItem;
use App\Models\Seller\SellerProfile;
use App\Models\Tracking\InventoryQrCode;
use App\Models\Tracking\OnsiteVisitAppointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'seller_profile_id',
        'distribution_inventory_id',
        'category_id',
        'sku',
        'upc',
        'name',
        'slug',
        'description',
        'condition',
        'brand',
        'material',
        'color',
        'dimensions',
        'weight_lbs',
        'supplier_price',
        'internal_price',
        'listing_price',
        'compare_at_price',
        'quantity',
        'warehouse_location',
        'assembly_required',
        'warranty',
        'same_day_delivery',
        'is_trending',
        'is_featured',
        'ownership_type',
        'status',
        'seo_meta',
    ];

    protected function casts(): array
    {
        return [
            'weight_lbs' => 'decimal:2',
            'internal_price' => 'decimal:2',
            'listing_price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'assembly_required' => 'boolean',
            'same_day_delivery' => 'boolean',
            'is_trending' => 'boolean',
            'is_featured' => 'boolean',
            'seo_meta' => 'array',
        ];
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function distributionInventory(): BelongsTo
    {
        return $this->belongsTo(DistributionInventory::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(InventoryQrCode::class);
    }

    public function onsiteAppointments(): HasMany
    {
        return $this->hasMany(OnsiteVisitAppointment::class);
    }

    public function catalogImports(): HasMany
    {
        return $this->hasMany(DistributionCatalogImport::class);
    }
}
