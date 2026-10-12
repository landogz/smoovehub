<?php

namespace App\Models\Tracking;

use App\Models\Catalog\Product;
use App\Models\Concerns\HasUuid;
use App\Models\Order\OrderItem;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnsiteVisitAppointment extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'product_id',
        'order_item_id',
        'customer_id',
        'seller_profile_id',
        'scheduled_at',
        'status',
        'customer_notes',
        'seller_notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }
}
