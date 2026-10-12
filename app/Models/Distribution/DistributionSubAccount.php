<?php

namespace App\Models\Distribution;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistributionSubAccount extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'distribution_company_id',
        'name',
        'code',
        'warehouse_location',
        'payout_stripe_account',
        'is_payout_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_payout_enabled' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(DistributionCompany::class, 'distribution_company_id');
    }
}
