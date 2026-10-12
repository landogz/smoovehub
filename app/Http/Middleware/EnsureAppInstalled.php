<?php

namespace App\Http\Middleware;

use App\Services\Install\InstallService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAppInstalled
{
    public function __construct(
        private readonly InstallService $installService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->installService->isInstalled()) {
            return $next($request);
        }

        if ($this->isInstallRoute($request)) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'status' => false,
                'message' => 'Application is not installed. Open /install to continue.',
                'errors' => [],
            ], 503);
        }

        return redirect('/install');
    }

    private function isInstallRoute(Request $request): bool
    {
        return $request->is('up')
            || $request->is('install')
            || $request->is('install/*')
            || $request->is('api/v1/install')
            || $request->is('api/v1/install/*');
    }
}
