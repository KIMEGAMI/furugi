<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'reason',
    'detail',
    'subscription_status',
])]
class SubscriptionCancellationFeedback extends Model
{
    use HasFactory;

    protected $table = 'subscription_cancellation_feedback';

    public const REASON_TOO_EXPENSIVE = 'too_expensive';

    public const REASON_MISSING_FEATURE = 'missing_feature';

    public const REASON_HARD_TO_USE = 'hard_to_use';

    public const REASON_NOT_USING = 'not_using';

    public const REASON_SWITCHED_TOOL = 'switched_tool';

    public const REASON_OTHER = 'other';

    public const REASONS = [
        self::REASON_TOO_EXPENSIVE => '料金が合わない',
        self::REASON_MISSING_FEATURE => '必要な機能が足りない',
        self::REASON_HARD_TO_USE => '使い方が分かりにくい',
        self::REASON_NOT_USING => '使う頻度が少ない',
        self::REASON_SWITCHED_TOOL => '別の管理方法に変えた',
        self::REASON_OTHER => 'その他',
    ];

    /** @return BelongsTo<User, SubscriptionCancellationFeedback> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
