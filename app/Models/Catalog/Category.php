<?php

namespace App\Models\Catalog;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'parent_id',
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
        'is_active',
        'seo_meta',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'seo_meta' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
