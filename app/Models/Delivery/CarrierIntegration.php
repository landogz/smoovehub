<?php

namespace App\Models\Delivery;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class CarrierIntegration extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'carrier',
        'display_name',
        'config',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
