<?php

namespace App\Models\Content;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'email',
        'city',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected function casts(): array
    {
        return [
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }
}
