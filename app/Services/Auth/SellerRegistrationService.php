<?php

namespace App\Services\Auth;

use App\Repositories\Auth\AuthRepository;
use App\Repositories\Auth\SellerRegistrationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerRegistrationService
{
    public function __construct(
        private readonly SellerRegistrationRepository $repository,
        private readonly AuthRepository $authRepository,
        private readonly AuthService $authService,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function register(array $payload, Request $request): array
    {
        return DB::transaction(function () use ($payload, $request) {
            $user = $this->repository->createSeller($payload);

            $this->authRepository->logActivity(
                $user,
                'auth.seller_register',
                ['email' => $user->email, 'store' => $payload['store_name']],
                $request->ip(),
                (string) $request->userAgent(),
            );

            return $this->authService->login([
                'email' => $payload['email'],
                'password' => $payload['password'],
                ...$payload,
            ], $request);
        });
    }
}
