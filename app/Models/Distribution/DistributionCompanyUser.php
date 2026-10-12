<?php

namespace App\Models\Distribution;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistributionCompanyUser extends Model
{
    protected $fillable = [
        'distribution_company_id',
        'user_id',
        'distribution_sub_account_id',
        'portal_role',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(DistributionCompany::class, 'distribution_company_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subAccount(): BelongsTo
    {
        return $this->belongsTo(DistributionSubAccount::class, 'distribution_sub_account_id');
    }
}
