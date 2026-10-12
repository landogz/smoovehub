<?php

namespace App\Models\Delivery;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverPayoutMethod extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'driver_id',
        'method',
        'account_details',
        'is_default',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
