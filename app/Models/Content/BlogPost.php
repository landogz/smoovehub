<?php

namespace App\Models\Content;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'slug',
        'title',
        'tag',
        'excerpt',
        'content',
        'icon',
        'author',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
