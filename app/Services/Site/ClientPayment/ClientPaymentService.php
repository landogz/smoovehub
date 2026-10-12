<?php

namespace App\Services\Site\ClientPayment;

use App\Repositories\Site\ClientPayment\ClientPaymentRepository;

class ClientPaymentService
{
    public function __construct(
        private readonly ClientPaymentRepository $repository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function page(): array
    {
        return $this->repository->page();
    }
}
