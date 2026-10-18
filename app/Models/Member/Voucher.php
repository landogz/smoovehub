<?php

namespace App\Models\Member;

use App\Models\Concerns\HasUuid;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Voucher extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'seller_profile_id',
        'code',
        'title',
        'type',
        'value',
        'min_spend',
        'expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_spend' => 'decimal:2',
            'expires_at' => 'datetime',
        ];
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'member_vouchers')
            ->withPivot(['uuid', 'redeemed_at'])
            ->withTimestamps();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
