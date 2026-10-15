<?php

namespace App\Services\Site\Maintenance;

use App\Repositories\Site\Maintenance\MaintenanceRepository;
use Illuminate\Http\Request;

class MaintenanceService
{
    public const BYPASS_COOKIE = 'smoovehub_maintenance_bypass';

    public function __construct(
        private readonly MaintenanceRepository $repository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function page(): array
    {
        return $this->repository->page();
    }

    public function isEnabled(): bool
    {
        return $this->repository->isEnabled();
    }

    public function isExempt(Request $request): bool
    {
        return $request->is('up')
            || $request->is('install')
            || $request->is('install/*')
            || $request->is('api/v1/install')
            || $request->is('api/v1/install/*')
            || $request->is('client-payment')
            || $request->is('project-progress')
            || $request->is('maintenance')
            || $request->is('build')
            || $request->is('build/*')
            || $request->is('images/*')
            || $request->is('storage/*')
            || $request->is('favicon.ico')
            || $request->is('robots.txt');
    }

    public function hasValidBypass(Request $request): bool
    {
        $token = $this->repository->bypassToken();

        if ($token === '') {
            return false;
        }

        $cookie = (string) $request->cookie(self::BYPASS_COOKIE, '');

        return hash_equals($token, $cookie);
    }

    public function incomingBypassToken(Request $request): ?string
    {
        $expected = $this->repository->bypassToken();

        if ($expected === '') {
            return null;
        }

        $provided = (string) $request->query('bypass', '');

        if ($provided === '' || ! hash_equals($expected, $provided)) {
            return null;
        }

        return $expected;
    }
}
