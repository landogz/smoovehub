<?php

namespace Database\Seeders;

use App\Models\Member\MemberAddress;
use App\Models\Member\MemberPaymentMethod;
use App\Models\Member\MemberVoucher;
use App\Models\Member\Voucher;
use App\Models\User;
use Illuminate\Database\Seeder;

class MemberAccountSeeder extends Seeder
{
    public function run(): void
    {
        $welcome = Voucher::query()->firstOrCreate(
            ['code' => 'WELCOME25'],
            [
                'title' => '$25 off first furniture order',
                'type' => 'amount',
                'value' => 25,
                'min_spend' => 0,
                'expires_at' => now()->addMonths(3),
                'status' => 'active',
            ]
        );

        Voucher::query()->firstOrCreate(
            ['code' => 'SAVE10'],
            [
                'title' => '10% off max $200',
                'type' => 'percent',
                'value' => 10,
                'min_spend' => 400,
                'expires_at' => now()->addDays(14),
                'status' => 'active',
            ]
        );

        Voucher::query()->firstOrCreate(
            ['code' => 'FREESHIP'],
            [
                'title' => '$50 off delivery coordination',
                'type' => 'amount',
                'value' => 50,
                'min_spend' => 200,
                'expires_at' => now()->addMonth(),
                'status' => 'active',
            ]
        );

        $member = User::query()->where('email', 'member@smoovehub.com')->first();

        if (! $member) {
            return;
        }

        if (! $member->username) {
            $member->update(['username' => 'demo_member']);
        }

        MemberAddress::query()->firstOrCreate(
            [
                'user_id' => $member->id,
                'line1' => '123 Main St',
            ],
            [
                'label' => 'Home',
                'recipient_name' => $member->name,
                'phone' => '2145550101',
                'city' => 'Dallas',
                'state' => 'TX',
                'zip' => '75201',
                'country' => 'US',
                'is_default' => true,
            ]
        );

        MemberPaymentMethod::query()->firstOrCreate(
            [
                'user_id' => $member->id,
                'last4' => '4242',
                'type' => 'card',
            ],
            [
                'brand' => 'Visa',
                'holder_name' => $member->name,
                'exp_month' => 12,
                'exp_year' => 2028,
                'is_default' => true,
            ]
        );

        MemberVoucher::query()->firstOrCreate(
            [
                'user_id' => $member->id,
                'voucher_id' => $welcome->id,
            ]
        );
    }
}
