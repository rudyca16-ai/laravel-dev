<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Always return null for API routes, even if expectsJson() is false
        if ($request->expectsJson() || $request->is('api/*'))
            return null;

        return route('login');
    }

    /**
     * Handle an incoming request.
     * Overrides the parent to always return JSON for unauthenticated requests.
     */
    public function handle($request, Closure $next, ...$guards): mixed
    {
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        return $next($request);
    }
}
