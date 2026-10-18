<?php

namespace App\Models\Member;

use App\Models\Concerns\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberAddress extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'line1',
        'line2',
        'city',
        'state',
        'zip',
        'country',
        'is_default',
        'is_pickup',
        'is_return',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_pickup' => 'boolean',
            'is_return' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
