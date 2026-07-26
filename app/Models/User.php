<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'google_id',
    'subscription_plan',
    'subscription_status',
    'stripe_customer_id',
    'stripe_subscription_id',
    'premium_started_at',
    'premium_ends_at',
    'is_admin',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const DEMO_EMAIL = 'user@shinji.work';

    public const PLAN_FREE = 'free';

    public const PLAN_PREMIUM = 'premium';

    public const FREE_AUCTION_ITEM_LIMIT = 30;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'premium_started_at' => 'datetime',
            'premium_ends_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }

    public function isPremium(): bool
    {
        if ($this->subscription_plan !== self::PLAN_PREMIUM) {
            return false;
        }

        if (in_array($this->subscription_status, ['active', 'trialing'], true)) {
            return true;
        }

        return $this->premium_ends_at !== null && $this->premium_ends_at->isFuture();
    }

    public function freeAuctionItemLimit(): int
    {
        return self::FREE_AUCTION_ITEM_LIMIT;
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /** @return HasMany<AuctionItem, User> */
    public function auctionItems(): HasMany
    {
        return $this->hasMany(AuctionItem::class);
    }
}
