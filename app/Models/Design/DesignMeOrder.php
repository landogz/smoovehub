<?php

namespace App\Models\Design;

use App\Models\Concerns\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignMeOrder extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'furniture_type',
        'material',
        'color',
        'color_name',
        'finish',
        'dimensions',
        'features',
        'timeline',
        'delivery_method',
        'contact_name',
        'contact_email',
        'contact_phone',
        'contact_address',
        'source',
        'notes',
        'sketch_path',
        'reference_images',
        'estimated_price',
        'deposit_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'dimensions' => 'array',
            'features' => 'array',
            'reference_images' => 'array',
            'estimated_price' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
