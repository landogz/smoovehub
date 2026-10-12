<?php

namespace App\Models\Distribution;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistributionApiKey extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'distribution_company_id',
        'name',
        'key_prefix',
        'key_hash',
        'abilities',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'abilities' => 'array',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(DistributionCompany::class, 'distribution_company_id');
    }
}
