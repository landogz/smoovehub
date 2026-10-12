<?php

namespace App\Http\Controllers\Web\Site\ProjectProgress;

use App\Http\Controllers\Controller;
use App\Services\Site\ProjectProgress\ProjectProgressService;
use Illuminate\View\View;

class ProjectProgressPageController extends Controller
{
    public function __construct(
        private readonly ProjectProgressService $projectProgressService,
    ) {}

    public function index(): View
    {
        return view('site.project-progress.index', [
            'progress' => $this->projectProgressService->page(),
        ]);
    }
}
