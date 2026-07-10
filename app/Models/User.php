<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'google_id', 'subscription_plan', 'subscribed_at', 'subscription_cancelled_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const PLAN_FREE = 'free';

    public const PLAN_PREMIUM = 'premium';

    public const PLANS = [
        self::PLAN_FREE,
        self::PLAN_PREMIUM,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'subscribed_at' => 'datetime',
            'subscription_cancelled_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isPremium(): bool
    {
        return $this->subscription_plan === self::PLAN_PREMIUM
            && $this->subscription_cancelled_at === null;
    }

    public function subscribeToPremium(): void
    {
        $this->forceFill([
            'subscription_plan' => self::PLAN_PREMIUM,
            'subscribed_at' => $this->subscribed_at ?? now(),
            'subscription_cancelled_at' => null,
        ])->save();
    }

    public function cancelSubscription(): void
    {
        $this->forceFill([
            'subscription_plan' => self::PLAN_FREE,
            'subscription_cancelled_at' => now(),
        ])->save();
    }
}
