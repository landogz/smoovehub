<?php

namespace App\Http\Middleware;

use App\Services\Install\InstallService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInstalled
{
    public function __construct(
        private readonly InstallService $installService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->installService->isInstalled()) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'SmooveHub is already installed.',
                    'errors' => [],
                ], 403);
            }

            return redirect('/');
        }

        return $next($request);
    }
}
