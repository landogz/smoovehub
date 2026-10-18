<?php

namespace App\Services\Seller;

use App\Models\User;
use App\Repositories\Seller\SellerDashboardRepository;
use App\Repositories\Seller\SellerPayoutAccountRepository;
use App\Repositories\Seller\SellerVoucherRepository;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class SellerDashboardService
{
    public function __construct(
        private readonly SellerDashboardRepository $repository,
        private readonly SellerPayoutAccountRepository $payoutAccounts,
        private readonly SellerVoucherRepository $vouchers,
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
    public function updateOrderStatus(User $user, string $uuid, string $status, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $order = $this->repository->updateOrderStatus($profile, $uuid, $status);

        $this->activityLogger->log($user, 'seller.order.status', 'order', null, [
            'order_uuid' => $uuid,
            'status' => $status,
        ], $request);

        return $order;
    }

    /**
     * @return array<string, mixed>
     */
    public function payouts(User $user): array
    {
        return $this->repository->payouts($this->repository->requireProfile($user));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function storePayoutAccount(User $user, array $payload, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $account = $this->payoutAccounts->store($profile, $payload);

        $this->activityLogger->log($user, 'seller.payout_account.created', 'seller_payout_account', null, [
            'last4' => $account['last4'],
            'bank_name' => $account['bank_name'],
        ], $request);

        return $account;
    }

    /**
     * @return array<string, mixed>
     */
    public function destroyPayoutAccount(User $user, string $uuid, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $account = $this->payoutAccounts->destroy($profile, $uuid);

        $this->activityLogger->log($user, 'seller.payout_account.deleted', 'seller_payout_account', null, [
            'last4' => $account['last4'],
        ], $request);

        return $account;
    }

    /**
     * @return array<string, mixed>
     */
    public function setDefaultPayoutAccount(User $user, string $uuid, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $account = $this->payoutAccounts->setDefault($profile, $uuid);

        $this->activityLogger->log($user, 'seller.payout_account.default', 'seller_payout_account', null, [
            'last4' => $account['last4'],
        ], $request);

        return $account;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function vouchers(User $user): array
    {
        return $this->vouchers->list($this->repository->requireProfile($user));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function storeVoucher(User $user, array $payload, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $voucher = $this->vouchers->store($profile, $payload);

        $this->activityLogger->log($user, 'seller.voucher.created', 'voucher', null, [
            'code' => $voucher['code'],
        ], $request);

        return $voucher;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateVoucher(User $user, string $uuid, array $payload, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $voucher = $this->vouchers->update($profile, $uuid, $payload);

        $this->activityLogger->log($user, 'seller.voucher.updated', 'voucher', null, [
            'code' => $voucher['code'],
        ], $request);

        return $voucher;
    }

    /**
     * @return array<string, mixed>
     */
    public function destroyVoucher(User $user, string $uuid, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $voucher = $this->vouchers->destroy($profile, $uuid);

        $this->activityLogger->log($user, 'seller.voucher.deleted', 'voucher', null, [
            'code' => $voucher['code'],
        ], $request);

        return $voucher;
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
     * @return array<string, mixed>
     */
    public function storeLogo(User $user, UploadedFile $file, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $path = $file->store('store-logos', 'public');
        $settings = $this->repository->storeLogo($profile, $user, $path);

        $this->activityLogger->log($user, 'seller.logo.updated', 'seller_profile', $profile->id, [], $request);

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
     * @return array<string, mixed>
     */
    public function importDistSku(User $user, string $uuid, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $result = $this->repository->importDistSku($profile, $uuid);

        $this->activityLogger->log($user, 'seller.dist.imported', 'distribution_catalog_import', null, [
            'inventory_uuid' => $uuid,
            'sku' => $result['import']['sku'] ?? null,
        ], $request);

        return $result;
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
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateOnsite(User $user, string $uuid, array $payload, Request $request): array
    {
        $profile = $this->repository->requireProfile($user);
        $appt = $this->repository->updateOnsite($profile, $uuid, $payload);

        $this->activityLogger->log($user, 'seller.onsite.updated', 'onsite_visit_appointment', null, [
            'appointment_uuid' => $uuid,
            'status' => $payload['status'],
        ], $request);

        return $appt;
    }

    /**
     * @return array<string, mixed>
     */
    public function openApi(User $user): array
    {
        $this->repository->requireProfile($user);

        return $this->repository->openApi($user);
    }

    /**
     * @return array<string, mixed>
     */
    public function issueOpenApiToken(User $user, Request $request): array
    {
        $this->repository->requireProfile($user);
        $result = $this->repository->issueOpenApiToken($user);

        $this->activityLogger->log($user, 'seller.open_api.token_issued', 'personal_access_token', null, [], $request);

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function revokeOpenApiToken(User $user, Request $request): array
    {
        $this->repository->requireProfile($user);
        $result = $this->repository->revokeOpenApiToken($user);

        $this->activityLogger->log($user, 'seller.open_api.token_revoked', 'personal_access_token', null, [], $request);

        return $result;
    }
}
