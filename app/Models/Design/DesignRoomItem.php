<?php

namespace App\Models\Design;

use App\Models\Catalog\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignRoomItem extends Model
{
    protected $fillable = [
        'design_room_id',
        'product_id',
        'position',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'array',
        ];
    }

    public function designRoom(): BelongsTo
    {
        return $this->belongsTo(DesignRoom::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
