<?php

namespace App\Http\Middleware;

use App\Services\Site\Maintenance\MaintenanceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteNotInMaintenance
{
    public function __construct(
        private readonly MaintenanceService $maintenanceService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $bypassToken = $this->maintenanceService->incomingBypassToken($request);

        if ($bypassToken !== null) {
            $response = $next($request);
            $response->headers->setCookie(cookie(
                MaintenanceService::BYPASS_COOKIE,
                $bypassToken,
                60 * 12,
                '/',
                null,
                $request->secure(),
                true,
                false,
                'lax',
            ));

            return $response;
        }

        if (! $this->maintenanceService->isEnabled()) {
            return $next($request);
        }

        if ($this->maintenanceService->isExempt($request) || $this->maintenanceService->hasValidBypass($request)) {
            return $next($request);
        }

        $page = $this->maintenanceService->page();
        $retryAfter = (string) $page['retry_after'];

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'status' => false,
                'message' => $page['message'],
                'errors' => [],
            ], 503)->header('Retry-After', $retryAfter);
        }

        return response()
            ->view('site.maintenance.index', [
                'maintenance' => $page,
            ], 503)
            ->header('Retry-After', $retryAfter);
    }
}
