<?php

namespace App\Models\Auth;

use App\Models\Concerns\HasUuid;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'token',
        'abilities',
        'expires_at',
        'device_name',
        'device_fingerprint',
        'ip_address',
        'tokenable_type',
        'tokenable_id',
    ];
}
