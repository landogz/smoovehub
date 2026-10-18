<?php

namespace App\Services\Seller;

use App\Models\User;
use App\Repositories\Seller\SellerProductRepository;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\Request;

class SellerProductService
{
    public function __construct(
        private readonly SellerProductRepository $repository,
        private readonly ActivityLogger $activityLogger,
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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function index(User $user): array
    {
        $this->assertSeller($user);

        return $this->repository->listForUser($user);
    }

    /**
     * @return array<string, mixed>
     */
    public function destroy(User $user, string $uuid, Request $request): array
    {
        $this->assertSeller($user);
        $product = $this->repository->destroyForUser($user, $uuid);

        $this->activityLogger->log($user, 'seller.product.deleted', 'product', $product['numeric_id'] ?? null, [
            'product_uuid' => $uuid,
            'name' => $product['name'] ?? null,
        ], $request);

        return $product;
    }

    private function assertSeller(User $user): void
    {
        if (! $user->hasRole('seller')) {
            abort(403, 'Only sellers can list products.');
        }
    }
}
