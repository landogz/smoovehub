<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $repository,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function login(array $payload, Request $request): array
    {
        $user = $this->repository->findByEmail($payload['email']);

        if (! $user || ! $this->repository->verifyPassword($user, $payload['password'])) {
            $this->repository->logActivity(
                null,
                'auth.login_failed',
                ['email' => $payload['email']],
                $request->ip(),
                (string) $request->userAgent(),
            );

            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw new HttpException(403, 'Your account is not active. Please contact support.');
        }

        return $this->authenticate($user, $payload, $request, 'auth.login');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function register(array $payload, Request $request): array
    {
        return DB::transaction(function () use ($payload, $request) {
            $user = $this->repository->createUser($payload);
            $user->assignRole('customer');

            $this->repository->logActivity(
                $user,
                'auth.register',
                ['email' => $user->email],
                $request->ip(),
                (string) $request->userAgent(),
            );

            return $this->authenticate($user, $payload, $request, 'auth.register');
        });
    }

    public function logout(User $user, Request $request): void
    {
        $this->repository->revokeCurrentToken($user);

        if ($request->hasSession()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $this->repository->logActivity(
            $user,
            'auth.logout',
            [],
            $request->ip(),
            (string) $request->userAgent(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function logoutOtherDevices(User $user, Request $request): array
    {
        $revoked = $this->repository->revokeOtherTokens($user);

        $this->repository->logActivity(
            $user,
            'auth.logout_other_devices',
            ['revoked_count' => $revoked],
            $request->ip(),
            (string) $request->userAgent(),
        );

        return [
            'revoked_count' => $revoked,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function me(User $user): array
    {
        return $this->repository->formatUser($user);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function authenticate(User $user, array $payload, Request $request, string $activity): array
    {
        $meta = $this->deviceMeta($payload, $request);
        $issued = $this->repository->issueToken($user, $meta);

        $this->repository->syncDevice($user, $meta);
        $this->repository->touchLastLogin($user);

        $this->repository->logActivity(
            $user,
            $activity,
            ['device_name' => $meta['device_name'] ?? 'Web Browser'],
            $request->ip(),
            (string) $request->userAgent(),
        );

        if ($request->hasSession()) {
            Auth::guard('web')->login($user);
            $request->session()->regenerate();
        }

        return [
            'token' => $issued['token'],
            'token_type' => 'Bearer',
            'user' => $this->repository->formatUser($user->fresh()),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function deviceMeta(array $payload, Request $request): array
    {
        return [
            'device_name' => $payload['device_name'] ?? $this->defaultDeviceName($request),
            'device_fingerprint' => $payload['device_fingerprint'] ?? null,
            'platform' => $payload['platform'] ?? null,
            'browser' => $payload['browser'] ?? null,
            'ip_address' => $request->ip(),
        ];
    }

    private function defaultDeviceName(Request $request): string
    {
        $agent = (string) $request->userAgent();

        if (str_contains($agent, 'iPhone') || str_contains($agent, 'iPad')) {
            return 'Apple Device';
        }

        if (str_contains($agent, 'Android')) {
            return 'Android Device';
        }

        return 'Web Browser';
    }
}
