<?php

namespace App\Models\Payout;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutSplit extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'payout_id',
        'recipient_type',
        'recipient_id',
        'amount',
        'percentage',
        'stripe_account_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'percentage' => 'decimal:2',
        ];
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }
}
