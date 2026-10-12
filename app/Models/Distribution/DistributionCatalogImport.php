<?php

namespace App\Models\Distribution;

use App\Models\Catalog\Product;
use App\Models\Concerns\HasUuid;
use App\Models\Seller\SellerProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistributionCatalogImport extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'seller_profile_id',
        'distribution_company_id',
        'distribution_inventory_id',
        'product_id',
        'wholesale_price',
        'resale_price',
        'status',
        'imported_at',
    ];

    protected function casts(): array
    {
        return [
            'wholesale_price' => 'decimal:2',
            'resale_price' => 'decimal:2',
            'imported_at' => 'datetime',
        ];
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(DistributionCompany::class, 'distribution_company_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(DistributionInventory::class, 'distribution_inventory_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
