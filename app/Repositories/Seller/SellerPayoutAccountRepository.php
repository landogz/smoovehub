<?php

namespace App\Repositories\Seller;

use App\Models\Seller\SellerPayoutAccount;
use App\Models\Seller\SellerProfile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class SellerPayoutAccountRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function list(SellerProfile $profile): array
    {
        return SellerPayoutAccount::query()
            ->where('seller_profile_id', $profile->id)
            ->orderByDesc('is_default')
            ->latest()
            ->get()
            ->map(fn (SellerPayoutAccount $account) => $this->payload($account))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function store(SellerProfile $profile, array $data): array
    {
        $makeDefault = (bool) ($data['is_default'] ?? false)
            || ! SellerPayoutAccount::query()->where('seller_profile_id', $profile->id)->exists();

        if ($makeDefault) {
            SellerPayoutAccount::query()
                ->where('seller_profile_id', $profile->id)
                ->update(['is_default' => false]);
        }

        $account = SellerPayoutAccount::query()->create([
            'seller_profile_id' => $profile->id,
            'bank_name' => $data['bank_name'],
            'holder_name' => $data['holder_name'],
            'account_type' => $data['account_type'],
            'last4' => $data['last4'],
            'is_default' => $makeDefault,
            'encrypted_meta' => Crypt::encryptString(json_encode([
                'last4' => $data['last4'],
                'account_type' => $data['account_type'],
            ], JSON_THROW_ON_ERROR)),
        ]);

        return $this->payload($account);
    }

    /**
     * @return array<string, mixed>
     */
    public function destroy(SellerProfile $profile, string $uuid): array
    {
        $account = $this->findOwned($profile, $uuid);
        $payload = $this->payload($account);
        $wasDefault = $account->is_default;
        $account->delete();

        if ($wasDefault) {
            $next = SellerPayoutAccount::query()
                ->where('seller_profile_id', $profile->id)
                ->latest()
                ->first();

            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function setDefault(SellerProfile $profile, string $uuid): array
    {
        $account = $this->findOwned($profile, $uuid);

        SellerPayoutAccount::query()
            ->where('seller_profile_id', $profile->id)
            ->update(['is_default' => false]);

        $account->update(['is_default' => true]);

        return $this->payload($account->fresh());
    }

    public function findOwned(SellerProfile $profile, string $uuid): SellerPayoutAccount
    {
        $account = SellerPayoutAccount::query()
            ->where('uuid', $uuid)
            ->where('seller_profile_id', $profile->id)
            ->first();

        if (! $account) {
            throw ValidationException::withMessages([
                'bank' => ['Bank account was not found.'],
            ]);
        }

        return $account;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(SellerPayoutAccount $account): array
    {
        return [
            'id' => $account->uuid,
            'bank_name' => $account->bank_name,
            'holder_name' => $account->holder_name,
            'account_type' => $account->account_type,
            'last4' => $account->last4,
            'is_default' => (bool) $account->is_default,
        ];
    }
}
