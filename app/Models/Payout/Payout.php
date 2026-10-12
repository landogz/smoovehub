<?php

namespace App\Models\Payout;

use App\Models\Concerns\HasUuid;
use App\Models\Order\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payout extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'payable_type',
        'payable_id',
        'user_id',
        'order_id',
        'amount',
        'currency',
        'status',
        'method',
        'stripe_transfer_id',
        'triggered_by',
        'scheduled_for',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'scheduled_for' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function splits(): HasMany
    {
        return $this->hasMany(PayoutSplit::class);
    }
}
