<?php

namespace App\Models\Distribution;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySyncLog extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'distribution_company_id',
        'distribution_inventory_id',
        'source',
        'event',
        'qty_change',
        'payload',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(DistributionCompany::class, 'distribution_company_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(DistributionInventory::class, 'distribution_inventory_id');
    }
}
