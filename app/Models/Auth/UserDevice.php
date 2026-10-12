<?php

namespace App\Models\Auth;

use App\Models\Concerns\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'device_name',
        'device_fingerprint',
        'platform',
        'browser',
        'ip_address',
        'last_active_at',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'last_active_at' => 'datetime',
            'is_current' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
