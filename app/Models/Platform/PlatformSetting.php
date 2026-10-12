<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'is_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
            'is_encrypted' => 'boolean',
        ];
    }
}
