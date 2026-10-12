<?php

namespace App\Services\Site\ProjectProgress;

use App\Repositories\Site\ProjectProgress\ProjectProgressRepository;

class ProjectProgressService
{
    public function __construct(
        private readonly ProjectProgressRepository $repository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function page(): array
    {
        $page = $this->repository->page();
        $phases = $page['phases'] ?? [];

        $scores = [];
        $done = 0;
        $inProgress = 0;
        $upcoming = 0;

        foreach ($phases as $index => $phase) {
            $status = $phase['status'] ?? 'upcoming';
            $percent = match ($status) {
                'done' => 100,
                'in_progress' => 50,
                default => 0,
            };

            $phases[$index]['percent'] = $percent;
            $scores[] = $percent;

            match ($status) {
                'done' => $done++,
                'in_progress' => $inProgress++,
                default => $upcoming++,
            };
        }

        $overall = count($scores) > 0
            ? (int) round(array_sum($scores) / count($scores))
            : 0;

        $moduleTotal = 0;
        $modulePrototype = 0;
        $moduleProduction = 0;
        $moduleUpcoming = 0;

        foreach ($page['portals'] ?? [] as $portalIndex => $portal) {
            $modules = $portal['modules'] ?? [];
            $page['portals'][$portalIndex]['module_count'] = count($modules);

            foreach ($modules as $module) {
                $moduleTotal++;
                match ($module['status'] ?? 'upcoming') {
                    'production' => $moduleProduction++,
                    'prototype' => $modulePrototype++,
                    default => $moduleUpcoming++,
                };
            }
        }

        $surfaceTotal = count($page['surfaces'] ?? []);

        $page['phases'] = $phases;
        $page['overall_percent'] = $overall;
        $page['counts'] = [
            'done' => $done,
            'in_progress' => $inProgress,
            'upcoming' => $upcoming,
            'total' => count($phases),
            'modules' => $moduleTotal,
            'modules_prototype' => $modulePrototype,
            'modules_production' => $moduleProduction,
            'modules_upcoming' => $moduleUpcoming,
            'surfaces' => $surfaceTotal,
        ];

        return $page;
    }
}
