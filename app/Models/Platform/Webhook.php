<?php

namespace App\Models\Platform;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Webhook extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'owner_type',
        'owner_id',
        'url',
        'events',
        'secret',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'events' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WebhookLog::class);
    }
}
