<?php

namespace App\Models\Seller;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerPayoutAccount extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'seller_profile_id',
        'bank_name',
        'holder_name',
        'account_type',
        'last4',
        'is_default',
        'encrypted_meta',
    ];

    protected $hidden = [
        'encrypted_meta',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }
}
