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
            'products' => $this->repository->products(),
            'condition_labels' => $this->repository->conditionLabels(),
        ];
    }
}
