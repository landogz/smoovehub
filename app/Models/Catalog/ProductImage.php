<?php

namespace App\Models\Catalog;

use App\Models\Concerns\HasUuid;
use App\Models\Distribution\DistributionInventory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'product_id',
        'distribution_inventory_id',
        'disk',
        'path',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function distributionInventory(): BelongsTo
    {
        return $this->belongsTo(DistributionInventory::class);
    }
}
