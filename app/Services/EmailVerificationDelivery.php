<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailVerificationDelivery
{
    public function send(User $user, string $context): bool
    {
        try {
            $user->sendEmailVerificationNotification();

            return true;
        } catch (Throwable $exception) {
            Log::warning('Email verification notification delivery failed.', [
                'user_id' => $user->id,
                'context' => $context,
                'error_class' => $exception::class,
            ]);

            return false;
        }
    }

    public static function failureStatus(): string
    {
        return 'verification-link-send-failed';
    }

    public static function sentStatus(): string
    {
        return 'verification-link-sent';
    }
}
