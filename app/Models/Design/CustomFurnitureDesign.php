<?php

namespace App\Models\Design;

use App\Models\Concerns\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomFurnitureDesign extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'furniture_type',
        'material',
        'color',
        'dimensions',
        'features',
        'sketch_path',
        'reference_image_path',
        'estimated_price',
        'build_timeline_days',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'dimensions' => 'array',
            'features' => 'array',
            'estimated_price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
