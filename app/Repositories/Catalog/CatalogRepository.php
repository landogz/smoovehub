<?php

namespace App\Repositories\Catalog;

use App\Repositories\Site\SitePageRepository;

class CatalogRepository
{
    public function __construct(
        private readonly SitePageRepository $sitePages,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function products(): array
    {
        return $this->sitePages->products();
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function conditionLabels(): array
    {
        return $this->sitePages->conditionLabels();
    }
}
