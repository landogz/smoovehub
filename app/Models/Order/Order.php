<?php

namespace App\Models\Order;

use App\Models\Concerns\HasUuid;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\Waybill;
use App\Models\Distribution\DistributionCompany;
use App\Models\Payout\Payout;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'order_number',
        'buyer_id',
        'seller_profile_id',
        'distribution_company_id',
        'delivery_quote_id',
        'order_type',
        'fulfillment_source',
        'subtotal',
        'delivery_fee',
        'processing_fee',
        'platform_fee',
        'total',
        'currency',
        'status',
        'payment_status',
        'stripe_payment_intent_id',
        'notes',
        'paid_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'processing_fee' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function distributionCompany(): BelongsTo
    {
        return $this->belongsTo(DistributionCompany::class);
    }

    public function deliveryQuote(): BelongsTo
    {
        return $this->belongsTo(DeliveryQuote::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function waybill(): HasOne
    {
        return $this->hasOne(Waybill::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }
}
