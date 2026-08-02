<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\MaintenanceModeService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MaintenanceMode
{
    public function __construct(
        private readonly MaintenanceModeService $maintenanceMode
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->maintenanceMode->enabled()) {
            return $next($request);
        }

        if ($this->isAdmin($request)) {
            return $next($request);
        }

        if ($this->isAuthenticationPath($request) || $this->isExceptedPath($request)) {
            return $next($request);
        }

        $retryAfter = max(0, (int) config('maintenance.retry_after', 1800));

        return response()
            ->view('maintenance', [
                'title' => config('maintenance.title'),
                'message' => config('maintenance.message'),
                'retryAfter' => $retryAfter,
            ], 503)
            ->header('Retry-After', (string) $retryAfter);
    }

    private function isExceptedPath(Request $request): bool
    {
        foreach ((array) config('maintenance.except_paths', []) as $path) {
            if (is_string($path) && $path !== '' && $request->is($path)) {
                return true;
            }
        }

        return false;
    }

    private function isAuthenticationPath(Request $request): bool
    {
        return $request->is('login') || $request->is('login/*') || $request->is('maintenance-login');
    }

    private function isAdmin(Request $request): bool
    {
        $user = $request->user() ?? Auth::guard('web')->user();

        if ($user instanceof User) {
            return $user->isAdmin();
        }

        try {
            $userId = Auth::guard('web')->id();

            if (! is_int($userId) && ! is_string($userId)) {
                return false;
            }

            return User::query()
                ->whereKey($userId)
                ->where('is_admin', true)
                ->exists();
        } catch (Throwable) {
            return false;
        }
    }
}
