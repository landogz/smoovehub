<?php

namespace App\Services\Catalog;

use App\Repositories\Catalog\CatalogRepository;

class CatalogService
{
    public function __construct(
        private readonly CatalogRepository $repository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function catalog(): array
    {
        return [
            'products' => $this->repository->storefront(),
            'condition_labels' => $this->repository->conditionLabels(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function storefrontProducts(): array
    {
        return $this->repository->storefront();
    }
}
