<?php

namespace App\Models\Platform;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PlatformFile extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'owner_type',
        'owner_id',
        'name',
        'disk',
        'path',
        'mime_type',
        'size',
        'category',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
