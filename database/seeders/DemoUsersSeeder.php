<?php

namespace Database\Seeders;

use App\Models\Distribution\DistributionCompany;
use App\Models\Distribution\DistributionCompanyUser;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Prototype demo credentials — development/staging only.
     */
    public function run(): void
    {
        $this->seedSuperAdmin();
        $this->seedAdmin();
        $this->seedDistributor();
        $this->seedSeller();
        $this->seedMember();
    }

    private function seedSuperAdmin(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'superadmin@smoovehub.com'],
            [
                'name' => 'SmooveHub Super Admin',
                'password' => Hash::make('Super2025!'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('super-admin');
    }

    private function seedAdmin(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'admin@smoovehub.com'],
            [
                'name' => 'SmooveHub Admin',
                'password' => Hash::make('Admin2025!'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('admin');
    }

    private function seedDistributor(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'dist@acme.com'],
            [
                'name' => 'ACME Distribution Partner',
                'password' => Hash::make('Dist2025!'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('distribution-partner');

        $company = DistributionCompany::query()->firstOrCreate(
            ['slug' => 'acme-furniture-co'],
            [
                'owner_user_id' => $user->id,
                'company_name' => 'ACME Furniture Co.',
                'email' => 'dist@acme.com',
                'phone' => '877-385-2919',
                'tier' => 'enterprise',
                'verification_status' => 'verified',
                'reseller_markup_rate' => 0.0500,
            ]
        );

        if ($company->owner_user_id !== $user->id) {
            $company->update(['owner_user_id' => $user->id]);
        }

        DistributionCompanyUser::query()->firstOrCreate(
            [
                'distribution_company_id' => $company->id,
                'user_id' => $user->id,
            ],
            [
                'portal_role' => 'admin',
                'is_primary' => true,
            ]
        );
    }

    private function seedSeller(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'demo@smoovehub.com'],
            [
                'name' => 'Demo Seller',
                'password' => Hash::make('Demo1234!'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('seller');

        SellerProfile::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'store_name' => 'Demo Seller',
                'store_slug' => 'demo-seller',
                'description' => 'Prototype demo seller storefront for SmooveHub development.',
                'business_type' => 'Residential',
                'city' => 'Dallas',
                'state' => 'TX',
                'verification_status' => 'verified',
                'rating_avg' => 4.9,
                'review_count' => 128,
                'accepts_onsite_visits' => true,
                'member_since' => now(),
            ]
        );
    }

    private function seedMember(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'member@smoovehub.com'],
            [
                'name' => 'Demo Member',
                'password' => Hash::make('Member1234!'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('customer');
    }
}
