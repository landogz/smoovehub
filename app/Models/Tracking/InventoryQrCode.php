<?php

namespace App\Models\Tracking;

use App\Models\Catalog\Product;
use App\Models\Concerns\HasUuid;
use App\Models\Distribution\DistributionInventory;
use App\Models\Order\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryQrCode extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'token',
        'product_id',
        'distribution_inventory_id',
        'order_item_id',
        'qr_image_path',
        'scan_url',
        'scan_count',
        'last_scanned_at',
    ];

    protected function casts(): array
    {
        return [
            'last_scanned_at' => 'datetime',
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

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
