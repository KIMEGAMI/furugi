<?php

namespace App\Models;

use Database\Factories\UserFactory;
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
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const SUBSCRIPTION_ACTIVE = 'active';

    public const SUBSCRIPTION_INACTIVE = 'inactive';

    public const LEGACY_SUBSCRIPTION_PREMIUM = 'premium';

    public const FREE_AUCTION_ITEM_LIMIT = 50;

    public const FREE_CATEGORY_LIMIT = 5;

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

    public function hasActiveSubscription(): bool
    {
        if (! in_array($this->subscription_plan, [self::SUBSCRIPTION_ACTIVE, self::LEGACY_SUBSCRIPTION_PREMIUM], true)) {
            return false;
        }

        if (in_array($this->subscription_status, ['active', 'trialing'], true)) {
            return true;
        }

        return $this->premium_ends_at !== null && $this->premium_ends_at->isFuture();
    }

    public function isPremium(): bool
    {
        return $this->hasActiveSubscription();
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
