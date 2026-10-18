<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user('sanctum') ?? $request->user();

        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication required.',
                    'errors' => [],
                ], 401);
            }

            if ($request->routeIs('site.dashboard')) {
                return redirect()->route('site.home');
            }

            // Admin and distribute still load a login overlay for guests.
            $request->attributes->set('portal_guest', true);

            return $next($request);
        }

        $allowed = collect($roles)
            ->flatMap(fn (string $role) => explode(',', $role))
            ->map(fn (string $role) => trim($role))
            ->filter()
            ->values()
            ->all();

        $hasRole = collect($allowed)->contains(fn (string $role) => $user->hasRole($role));

        if (! $hasRole) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have access to this portal.',
                    'errors' => [],
                ], 403);
            }

            if ($request->routeIs('site.dashboard')) {
                return redirect()->route('site.home');
            }

            $request->attributes->set('portal_forbidden', true);
        }

        return $next($request);
    }
}
