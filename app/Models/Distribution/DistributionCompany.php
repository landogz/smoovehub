<?php

namespace App\Models\Distribution;

use App\Models\Concerns\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistributionCompany extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'owner_user_id',
        'company_name',
        'slug',
        'email',
        'phone',
        'tier',
        'verification_status',
        'stripe_connect_id',
        'reseller_markup_rate',
        'address',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'reseller_markup_rate' => 'decimal:4',
            'address' => 'array',
            'settings' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function subAccounts(): HasMany
    {
        return $this->hasMany(DistributionSubAccount::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(DistributionCompanyUser::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(DistributionApiKey::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(DistributionInventory::class);
    }
}
