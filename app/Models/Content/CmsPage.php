<?php

namespace App\Models\Content;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'slug',
        'title',
        'content',
        'page_type',
        'meta_description',
        'meta_keywords',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
