<?php

namespace App\Services\Seller;

use App\Models\User;
use App\Repositories\Seller\SellerDashboardRepository;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\Request;

class SellerDashboardService
{
    public function __construct(
        private readonly SellerDashboardRepository $repository,
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function overview(User $user): array
    {
        return $this->repository->overview($this->repository->requireProfile($user));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function orders(User $user): array
    {
        return $this->repository->orders($this->repository->requireProfile($user));
    }

    /**
     * @return array<string, mixed>
     */
    public function payouts(User $user): array
    {
        return $this->repository->payouts($this->repository->requireProfile($user));
    }

    /**
     * @return array<string, mixed>
     */
    public function settings(User $user): array
    {
        return $this->repository->settings($this->repository->requireProfile($user), $user);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateSettings(User $user, array $payload, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $settings = $this->repository->updateSettings($profile, $user, $payload);

        $this->activityLogger->log($user, 'seller.settings.updated', 'seller_profile', $profile->id, [
            'store_name' => $settings['store_name'],
        ], $request);

        return $settings;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function distCatalog(User $user): array
    {
        $this->repository->requireProfile($user);

        return $this->repository->distCatalog();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function imports(User $user): array
    {
        return $this->repository->imports($this->repository->requireProfile($user));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function onsiteAppointments(User $user): array
    {
        return $this->repository->onsiteAppointments($this->repository->requireProfile($user));
    }

    /**
     * @return array<string, mixed>
     */
    public function openApi(User $user): array
    {
        $this->repository->requireProfile($user);

        return $this->repository->openApi();
    }
}
