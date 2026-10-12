<?php

namespace App\Services\Seller;

use App\Models\User;
use App\Repositories\Seller\SellerProductRepository;

class SellerProductService
{
    public function __construct(
        private readonly SellerProductRepository $repository,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function store(User $user, array $payload): array
    {
        if (! $user->hasRole('seller')) {
            abort(403, 'Only sellers can list products.');
        }

        return $this->repository->createForUser($user, $payload);
    }
}
