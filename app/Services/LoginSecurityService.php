<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SuspiciousLoginNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class LoginSecurityService
{
    public function recordSuccessfulLogin(User $user, Request $request, string $loginMethod): void
    {
        if (! $this->hasLoginSecurityColumns()) {
            return;
        }

        $ipAddress = $request->ip();
        $userAgentHash = $this->userAgentHash($request);
        $isSuspicious = $this->isSuspiciousLogin($user, $ipAddress, $userAgentHash);

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
            'last_login_user_agent_hash' => $userAgentHash,
            'suspicious_login_detected_at' => $isSuspicious ? now() : $user->suspicious_login_detected_at,
        ])->save();

        if ($isSuspicious && (bool) config('auth_security.suspicious_login_notifications', true)) {
            $this->notifySuspiciousLogin($user, $loginMethod, $ipAddress);
        }
    }

    private function isSuspiciousLogin(User $user, ?string $ipAddress, string $userAgentHash): bool
    {
        if (! is_string($user->last_login_ip) || $user->last_login_ip === '') {
            return false;
        }

        if (! is_string($user->last_login_user_agent_hash) || $user->last_login_user_agent_hash === '') {
            return false;
        }

        return $user->last_login_ip !== $ipAddress
            || ! hash_equals($user->last_login_user_agent_hash, $userAgentHash);
    }

    private function notifySuspiciousLogin(User $user, string $loginMethod, ?string $ipAddress): void
    {
        try {
            $user->notify(new SuspiciousLoginNotification(
                $loginMethod,
                $ipAddress ?: '不明',
                now()->timezone(config('app.timezone'))->format('Y/m/d H:i')
            ));
        } catch (Throwable $exception) {
            Log::warning('Failed to send suspicious login notification.', [
                'user_id' => $user->id,
                'error_class' => $exception::class,
            ]);
        }
    }

    private function userAgentHash(Request $request): string
    {
        return hash('sha256', (string) $request->userAgent());
    }

    private function hasLoginSecurityColumns(): bool
    {
        return Schema::hasColumn('users', 'last_login_at')
            && Schema::hasColumn('users', 'last_login_ip')
            && Schema::hasColumn('users', 'last_login_user_agent_hash')
            && Schema::hasColumn('users', 'suspicious_login_detected_at');
    }
}
