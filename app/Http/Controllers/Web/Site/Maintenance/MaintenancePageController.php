<?php

namespace App\Http\Controllers\Web\Site\Maintenance;

use App\Http\Controllers\Controller;
use App\Services\Site\Maintenance\MaintenanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class MaintenancePageController extends Controller
{
    public function __construct(
        private readonly MaintenanceService $maintenanceService,
    ) {}

    public function index(): Response|RedirectResponse
    {
        if (! $this->maintenanceService->isEnabled()) {
            return redirect('/');
        }

        $page = $this->maintenanceService->page();

        return response()
            ->view('site.maintenance.index', [
                'maintenance' => $page,
            ], 503)
            ->header('Retry-After', (string) $page['retry_after']);
    }
}
