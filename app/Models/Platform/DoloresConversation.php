<?php

namespace App\Models\Platform;

use App\Models\Concerns\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoloresConversation extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'session_id',
        'messages',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'messages' => 'array',
            'last_message_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
