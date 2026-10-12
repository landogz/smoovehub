<?php

namespace App\Models\Order;

use App\Models\Catalog\Product;
use App\Models\Concerns\HasUuid;
use App\Models\Delivery\DeliveryTimelineEvent;
use App\Models\Distribution\DistributionInventory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'order_id',
        'product_id',
        'distribution_inventory_id',
        'sku',
        'name',
        'condition',
        'seller_name',
        'quantity',
        'unit_price',
        'line_total',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function distributionInventory(): BelongsTo
    {
        return $this->belongsTo(DistributionInventory::class);
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(DeliveryTimelineEvent::class);
    }
}
