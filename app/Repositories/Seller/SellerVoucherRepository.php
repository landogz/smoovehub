<?php

namespace App\Repositories\Seller;

use App\Models\Member\Voucher;
use App\Models\Seller\SellerProfile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SellerVoucherRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function list(SellerProfile $profile): array
    {
        return Voucher::query()
            ->where('seller_profile_id', $profile->id)
            ->latest()
            ->get()
            ->map(fn (Voucher $voucher) => $this->payload($voucher))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function store(SellerProfile $profile, array $data): array
    {
        $voucher = Voucher::query()->create($this->attributes($profile, $data));

        return $this->payload($voucher);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(SellerProfile $profile, string $uuid, array $data): array
    {
        $voucher = $this->findOwned($profile, $uuid);
        $voucher->update($this->attributes($profile, $data));

        return $this->payload($voucher->fresh());
    }

    /**
     * @return array<string, mixed>
     */
    public function destroy(SellerProfile $profile, string $uuid): array
    {
        $voucher = $this->findOwned($profile, $uuid);
        $payload = $this->payload($voucher);
        $voucher->delete();

        return $payload;
    }

    public function findOwned(SellerProfile $profile, string $uuid): Voucher
    {
        $voucher = Voucher::query()
            ->where('uuid', $uuid)
            ->where('seller_profile_id', $profile->id)
            ->first();

        if (! $voucher) {
            throw ValidationException::withMessages([
                'voucher' => ['Voucher was not found.'],
            ]);
        }

        return $voucher;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(SellerProfile $profile, array $data): array
    {
        return [
            'seller_profile_id' => $profile->id,
            'code' => Str::upper($data['code']),
            'title' => $data['title'],
            'type' => $data['type'],
            'value' => $data['value'],
            'min_spend' => $data['min_spend'] ?? 0,
            'expires_at' => $data['expires_at'] ?? null,
            'status' => $data['status'] ?? 'active',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(Voucher $voucher): array
    {
        return [
            'id' => $voucher->uuid,
            'code' => $voucher->code,
            'title' => $voucher->title,
            'type' => $voucher->type,
            'value' => (float) $voucher->value,
            'min_spend' => (float) $voucher->min_spend,
            'expires_at' => optional($voucher->expires_at)->toDateString(),
            'status' => $voucher->status,
            'expired' => $voucher->isExpired(),
        ];
    }
}
