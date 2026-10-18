<?php

namespace App\Models\Member;

use App\Models\Concerns\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPaymentMethod extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'type',
        'brand',
        'bank_name',
        'last4',
        'exp_month',
        'exp_year',
        'holder_name',
        'is_default',
        'encrypted_meta',
    ];

    protected $hidden = [
        'encrypted_meta',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'exp_month' => 'integer',
            'exp_year' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
