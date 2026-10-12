<?php

namespace App\Repositories\Auth;

use App\Models\Auth\PersonalAccessToken;
use App\Models\Auth\UserDevice;
use App\Models\Platform\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->first();
    }

    public function findByUuid(string $uuid): ?User
    {
        return User::query()
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createUser(array $data): User
    {
        return User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    public function verifyPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array{token: string, token_id: int}
     */
    public function issueToken(User $user, array $meta): array
    {
        $deviceName = $meta['device_name'] ?? 'Web Browser';
        $accessToken = $user->createToken($deviceName, ['*']);

        /** @var PersonalAccessToken $tokenModel */
        $tokenModel = $accessToken->accessToken;
        $tokenModel->forceFill([
            'device_name' => $deviceName,
            'device_fingerprint' => $meta['device_fingerprint'] ?? null,
            'ip_address' => $meta['ip_address'] ?? null,
        ])->save();

        return [
            'token' => $accessToken->plainTextToken,
            'token_id' => $tokenModel->id,
        ];
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function syncDevice(User $user, array $meta): UserDevice
    {
        $fingerprint = $meta['device_fingerprint'] ?? null;

        UserDevice::query()
            ->where('user_id', $user->id)
            ->update(['is_current' => false]);

        if ($fingerprint) {
            $device = UserDevice::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'device_fingerprint' => $fingerprint,
                ],
                [
                    'device_name' => $meta['device_name'] ?? 'Web Browser',
                    'platform' => $meta['platform'] ?? null,
                    'browser' => $meta['browser'] ?? null,
                    'ip_address' => $meta['ip_address'] ?? null,
                    'last_active_at' => now(),
                    'is_current' => true,
                ],
            );

            return $device;
        }

        return UserDevice::query()->create([
            'user_id' => $user->id,
            'device_name' => $meta['device_name'] ?? 'Web Browser',
            'device_fingerprint' => null,
            'platform' => $meta['platform'] ?? null,
            'browser' => $meta['browser'] ?? null,
            'ip_address' => $meta['ip_address'] ?? null,
            'last_active_at' => now(),
            'is_current' => true,
        ]);
    }

    public function touchLastLogin(User $user): void
    {
        $user->forceFill(['last_login_at' => now()])->save();
    }

    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function revokeOtherTokens(User $user): int
    {
        $currentTokenId = $user->currentAccessToken()?->id;

        if (! $currentTokenId) {
            return 0;
        }

        return PersonalAccessToken::query()
            ->where('tokenable_id', $user->id)
            ->where('tokenable_type', User::class)
            ->where('id', '!=', $currentTokenId)
            ->delete();
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function logActivity(?User $user, string $action, array $properties = [], ?string $ip = null, ?string $userAgent = null): void
    {
        ActivityLog::query()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'properties' => $properties,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function formatUser(User $user): array
    {
        $user->loadMissing([
            'roles:id,name,slug',
            'sellerProfile:id,user_id,uuid,store_name,store_slug,verification_status',
            'distributionMemberships.company:id,uuid,company_name,slug,tier',
        ]);

        $roles = $user->roles->pluck('slug')->values()->all();
        $primaryRole = $roles[0] ?? 'customer';

        $payload = [
            'uuid' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => $user->status,
            'roles' => $roles,
            'primary_role' => $primaryRole,
            'avatar_initials' => $this->initials($user->name),
            'last_login_at' => $user->last_login_at?->toIso8601String(),
        ];

        if ($user->sellerProfile) {
            $payload['seller_profile'] = [
                'uuid' => $user->sellerProfile->uuid,
                'store_name' => $user->sellerProfile->store_name,
                'store_slug' => $user->sellerProfile->store_slug,
                'verification_status' => $user->sellerProfile->verification_status,
            ];
        }

        if ($user->distributionMemberships->isNotEmpty()) {
            $company = $user->distributionMemberships->first()?->company;

            if ($company) {
                $payload['distribution_company'] = [
                    'uuid' => $company->uuid,
                    'company_name' => $company->company_name,
                    'slug' => $company->slug,
                    'tier' => $company->tier,
                ];
            }
        }

        return $payload;
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        return $initials !== '' ? $initials : 'U';
    }
}
