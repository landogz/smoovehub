<?php

namespace App\Repositories\Site;

use App\Models\Delivery\Driver;
use App\Models\Delivery\DriverPayoutMethod;

class DriverApplicationRepository
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Driver
    {
        $mcDot = collect([
            'mc' => $data['mc_number'] ?? null,
            'dot' => $data['dot_number'] ?? null,
        ])->filter(fn ($value) => filled($value))->all();


        $driver = Driver::query()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'vehicle_type' => $data['vehicle_type'],
            'mc_dot_number' => $mcDot !== [] ? json_encode($mcDot) : null,
            'verification_status' => 'pending',
        ]);

        DriverPayoutMethod::query()->create([
            'driver_id' => $driver->id,
            'method' => $data['payout_method'],
            'account_details' => encrypt(json_encode($this->payoutDetails($data))),
            'is_default' => true,
            'is_verified' => false,
        ]);

        return $driver->fresh(['payoutMethods']);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payoutDetails(array $data): array
    {
        return match ($data['payout_method']) {
            'ach' => [
                'account_holder' => $data['account_holder'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'routing_number' => $data['routing_number'] ?? null,
                'account_number' => $data['account_number'] ?? null,
                'account_type' => $data['account_type'] ?? 'checking',
            ],
            'stripe' => [
                'stripe_email' => $data['stripe_email'] ?? null,
            ],
            'zelle' => [
                'zelle_contact' => $data['zelle_contact'] ?? null,
            ],
            'venmo' => [
                'venmo_handle' => $data['venmo_handle'] ?? null,
            ],
            default => [],
        };
    }
}
