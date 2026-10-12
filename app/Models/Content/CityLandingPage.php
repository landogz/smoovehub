<?php

namespace App\Models\Content;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class CityLandingPage extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'state',
        'hero_content',
        'seo_meta',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hero_content' => 'array',
            'seo_meta' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
