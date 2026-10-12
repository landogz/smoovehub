<?php

namespace App\Models\Delivery;

use App\Models\Concerns\HasUuid;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryTimelineEvent extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'delivery_id',
        'order_id',
        'order_item_id',
        'step',
        'title',
        'description',
        'occurred_at',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'location' => 'array',
        ];
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
