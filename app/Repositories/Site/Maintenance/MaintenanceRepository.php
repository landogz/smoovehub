<?php

namespace App\Repositories\Site\Maintenance;

class MaintenanceRepository
{
    /**
     * @return array<string, mixed>
     */
    public function page(): array
    {
        return [
            'title' => 'We’ll be back soon',
            'eyebrow' => 'Under maintenance',
            'message' => (string) config('maintenance.message'),
            'retry_after' => (int) config('maintenance.retry_after', 3600),
        ];
    }

    public function isEnabled(): bool
    {
        return (bool) config('maintenance.enabled');
    }

    public function bypassToken(): string
    {
        return (string) config('maintenance.bypass', '');
    }
}
