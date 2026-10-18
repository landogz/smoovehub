<?php

namespace App\Services\Member;

use App\Models\User;
use App\Repositories\Member\MemberAccountRepository;

class MemberAccountService
{
    public function __construct(
        private readonly MemberAccountRepository $repository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function overview(User $user): array
    {
        return $this->repository->overview($user);
    }
}
