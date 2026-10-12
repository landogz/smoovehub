<?php

namespace App\Models\Design;

use App\Models\Concerns\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DesignRoom extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'room_type',
        'dimensions',
        'canvas_data',
        'photo_path',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'dimensions' => 'array',
            'canvas_data' => 'array',
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DesignRoomItem::class);
    }
}
