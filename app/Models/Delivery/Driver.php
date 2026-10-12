<?php

namespace App\Models\Delivery;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'vehicle_type',
        'mc_dot_number',
        'insurance_policy',
        'cdl_number',
        'verification_status',
        'crs_crm_linked',
        'delivery_count',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'crs_crm_linked' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function payoutMethods(): HasMany
    {
        return $this->hasMany(DriverPayoutMethod::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
