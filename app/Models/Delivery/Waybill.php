<?php

namespace App\Models\Delivery;

use App\Models\Concerns\HasUuid;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Waybill extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'order_id',
        'delivery_id',
        'waybill_number',
        'barcode_data',
        'html_content',
        'pdf_path',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
}
