<?php

namespace App\Http\Controllers\Web\LandingPage;

use App\Http\Controllers\Controller;
use App\Services\LandingPage\LandingPageService;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __construct(
        private readonly LandingPageService $landingPageService,
    ) {}

    public function index(): View
    {
        return view('landing.index', [
            'landing' => $this->landingPageService->getLandingData(),
        ]);
    }
}
