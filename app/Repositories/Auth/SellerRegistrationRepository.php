<?php

namespace App\Repositories\Auth;

use App\Models\Seller\SellerProfile;
use App\Models\User;
use Illuminate\Support\Str;

class SellerRegistrationRepository
{
    public function __construct(
        private readonly AuthRepository $authRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createSeller(array $data): User
    {
        $name = trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? ''));

        $user = $this->authRepository->createUser([
            'name' => $name,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
        ]);

        $user->assignRole('seller');

        $baseSlug = Str::slug($data['store_name']);
        $slug = $this->uniqueStoreSlug($baseSlug !== '' ? $baseSlug : 'store');

        SellerProfile::query()->create([
            'user_id' => $user->id,
            'store_name' => $data['store_name'],
            'store_slug' => $slug,
            'description' => $data['description'] ?? null,
            'business_type' => $data['business_type'] ?? null,
            'verification_status' => 'pending',
            'member_since' => now(),
            'seo_meta' => [
                'category' => $data['category'] ?? null,
                'catalog_size' => $data['catalog_size'] ?? null,
            ],
        ]);

        return $user->fresh(['roles', 'sellerProfile']);
    }

    private function uniqueStoreSlug(string $base): string
    {
        $slug = $base;
        $counter = 1;

        while (SellerProfile::query()->where('store_slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
