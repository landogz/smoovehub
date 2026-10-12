<?php

namespace App\Http\Controllers\Web\Install;

use App\Http\Controllers\Controller;
use App\Services\Install\InstallService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstallPageController extends Controller
{
    public function __construct(
        private readonly InstallService $installService,
    ) {}

    public function index(): View|RedirectResponse
    {
        if ($this->installService->isInstalled()) {
            return redirect('/');
        }

        return view('install.index', [
            'status' => $this->installService->status(),
            'defaultAppUrl' => url('/'),
        ]);
    }
}
