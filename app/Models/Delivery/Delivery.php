<?php

namespace App\Models\Delivery;

use App\Models\Concerns\HasUuid;
use App\Models\Order\DeliveryQuote;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Delivery extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'order_id',
        'delivery_quote_id',
        'driver_id',
        'carrier',
        'external_job_id',
        'tracking_number',
        'status',
        'estimated_delivery_at',
        'picked_up_at',
        'delivered_at',
        'proof_url',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'estimated_delivery_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'delivered_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryQuote(): BelongsTo
    {
        return $this->belongsTo(DeliveryQuote::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(DeliveryTimelineEvent::class);
    }

    public function waybill(): HasOne
    {
        return $this->hasOne(Waybill::class);
    }
}
