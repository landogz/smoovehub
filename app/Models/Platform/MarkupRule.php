<?php

namespace App\Models\Platform;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class MarkupRule extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'rule_key',
        'rate',
        'applies_to',
        'description',
        'is_active',
        'effective_from',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'is_active' => 'boolean',
            'effective_from' => 'datetime',
        ];
    }
}
