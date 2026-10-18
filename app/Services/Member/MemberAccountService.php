<?php

namespace App\Services\Member;

use App\Models\User;
use App\Repositories\Member\MemberAccountRepository;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MemberAccountService
{
    public function __construct(
        private readonly MemberAccountRepository $repository,
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function overview(User $user): array
    {
        return $this->repository->overview($user);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateProfile(User $user, array $payload, Request $request): array
    {
        $profile = $this->repository->updateProfile($user, $payload);
        $this->activityLogger->log($user, 'member.profile.updated', 'user', $user->id, [
            'username' => $profile['username'],
        ], $request);

        return $profile;
    }

    /**
     * @return array<string, mixed>
     */
    public function updatePassword(User $user, array $payload, Request $request): array
    {
        if (! Hash::check((string) $payload['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $this->repository->updatePassword($user, (string) $payload['password']);
        $this->activityLogger->log($user, 'member.password.updated', 'user', $user->id, [], $request);

        return [];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateSettings(User $user, array $payload, Request $request): array
    {
        $settings = $this->repository->updateSettings($user, $payload);
        $this->activityLogger->log($user, 'member.settings.updated', 'user', $user->id, [], $request);

        return $settings;
    }

    /**
     * @return array<string, mixed>
     */
    public function storeAvatar(User $user, UploadedFile $file, Request $request): array
    {
        $path = $file->store('avatars', 'public');
        $url = $this->repository->storeAvatar($user, $path);
        $this->activityLogger->log($user, 'member.avatar.updated', 'user', $user->id, [], $request);

        return [
            'avatar_url' => $url,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function addresses(User $user): array
    {
        return $this->repository->addresses($user);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function storeAddress(User $user, array $payload, Request $request): array
    {
        $address = $this->repository->storeAddress($user, $payload);
        $this->activityLogger->log($user, 'member.address.created', 'member_address', null, [
            'id' => $address['id'],
        ], $request);

        return $address;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateAddress(User $user, string $uuid, array $payload, Request $request): array
    {
        $address = $this->repository->updateAddress($user, $uuid, $payload);
        $this->activityLogger->log($user, 'member.address.updated', 'member_address', null, [
            'id' => $uuid,
        ], $request);

        return $address;
    }

    public function deleteAddress(User $user, string $uuid, Request $request): void
    {
        $this->repository->deleteAddress($user, $uuid);
        $this->activityLogger->log($user, 'member.address.deleted', 'member_address', null, [
            'id' => $uuid,
        ], $request);
    }

    /**
     * @return array<string, mixed>
     */
    public function setDefaultAddress(User $user, string $uuid, Request $request): array
    {
        $address = $this->repository->setDefaultAddress($user, $uuid);
        $this->activityLogger->log($user, 'member.address.defaulted', 'member_address', null, [
            'id' => $uuid,
        ], $request);

        return $address;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function paymentMethods(User $user): array
    {
        return $this->repository->paymentMethods($user);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function storePaymentMethod(User $user, array $payload, Request $request): array
    {
        $this->rejectSensitivePaymentFields($payload);
        $method = $this->repository->storePaymentMethod($user, $payload);
        $this->activityLogger->log($user, 'member.payment.created', 'member_payment_method', null, [
            'type' => $method['type'],
            'last4' => $method['last4'],
        ], $request);

        return $method;
    }

    public function deletePaymentMethod(User $user, string $uuid, Request $request): void
    {
        $this->repository->deletePaymentMethod($user, $uuid);
        $this->activityLogger->log($user, 'member.payment.deleted', 'member_payment_method', null, [
            'id' => $uuid,
        ], $request);
    }

    /**
     * @return array<string, mixed>
     */
    public function setDefaultPaymentMethod(User $user, string $uuid, Request $request): array
    {
        $method = $this->repository->setDefaultPaymentMethod($user, $uuid);
        $this->activityLogger->log($user, 'member.payment.defaulted', 'member_payment_method', null, [
            'id' => $uuid,
        ], $request);

        return $method;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function orders(User $user, string $tab, string $query): array
    {
        return $this->repository->orders($user, $tab, $query);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function notifications(User $user): array
    {
        return $this->repository->notifications($user);
    }

    public function markNotificationRead(User $user, string $id): void
    {
        $this->repository->markNotificationRead($user, $id);
    }

    public function markAllNotificationsRead(User $user): void
    {
        $this->repository->markAllNotificationsRead($user);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function vouchers(User $user): array
    {
        return $this->repository->vouchers($user);
    }

    /**
     * @return array<string, mixed>
     */
    public function redeemVoucher(User $user, string $code, Request $request): array
    {
        $voucher = $this->repository->redeemVoucher($user, $code);
        $this->activityLogger->log($user, 'member.voucher.redeemed', 'voucher', null, [
            'code' => $voucher['code'],
        ], $request);

        return $voucher;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function rejectSensitivePaymentFields(array $payload): void
    {
        foreach (['card_number', 'cvv', 'cvc', 'account_number', 'routing_number', 'iban'] as $field) {
            if (filled($payload[$field] ?? null)) {
                throw ValidationException::withMessages([
                    $field => ['Full card or bank numbers are not stored. Enter brand and last 4 digits only.'],
                ]);
            }
        }
    }
}
