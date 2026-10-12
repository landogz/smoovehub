<?php

namespace App\Models\Order;

use App\Models\Concerns\HasUuid;
use App\Models\Delivery\Delivery;
use App\Models\Distribution\DistributionCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryQuote extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'distribution_company_id',
        'quote_number',
        'contact_name',
        'contact_phone',
        'contact_email',
        'pickup_address',
        'dropoff_address',
        'items_description',
        'access_details',
        'distance_miles',
        'distance_tier',
        'crs_quote_id',
        'estimated_fee',
        'stairs_surcharge',
        'elevator_surcharge',
        'long_carry_surcharge',
        'waiting_time_surcharge',
        'status',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'pickup_address' => 'array',
            'dropoff_address' => 'array',
            'items_description' => 'array',
            'access_details' => 'array',
            'distance_miles' => 'decimal:2',
            'estimated_fee' => 'decimal:2',
            'stairs_surcharge' => 'decimal:2',
            'elevator_surcharge' => 'decimal:2',
            'long_carry_surcharge' => 'decimal:2',
            'waiting_time_surcharge' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function distributionCompany(): BelongsTo
    {
        return $this->belongsTo(DistributionCompany::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
