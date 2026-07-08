<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionItem extends Model
{
    public const PLATFORM_YAHOO = 'ヤフオク';

    public const PLATFORM_MERCARI = 'メルカリ';

    public const PLATFORM_RAKUMA = 'ラクマ';

    public const PLATFORM_PAYPAY = 'PayPayフリマ';

    public const PLATFORM_OTHER = 'その他';

    public const STATUS_SELLING = 'selling';

    public const STATUS_SOLD = 'sold';

    public const STATUS_DRAFT = 'draft';

    public const PLATFORMS = [
        self::PLATFORM_YAHOO,
        self::PLATFORM_MERCARI,
        self::PLATFORM_RAKUMA,
        self::PLATFORM_PAYPAY,
        self::PLATFORM_OTHER,
    ];

    public const SALES_FEE_RATES = [
        self::PLATFORM_YAHOO => 10.0,
        self::PLATFORM_MERCARI => 10.0,
        self::PLATFORM_RAKUMA => 10.0,
        self::PLATFORM_PAYPAY => 5.0,
        self::PLATFORM_OTHER => 0.0,
    ];

    public const STATUSES = [
        self::STATUS_SELLING,
        self::STATUS_SOLD,
        self::STATUS_DRAFT,
    ];

    protected $fillable = [
        'user_id',
        'management_id',
        'title',
        'comment',
        'platform',
        'category_id',
        'image_path',
        'sold_image_path',
        'status',
        'purchase_price',
        'sold_price',
        'sales_fee_rate',
        'sales_fee',
        'shipping_fee',
        'profit',
        'sold_at',
    ];

    protected $casts = [
        'purchase_price' => 'integer',
        'category_id' => 'integer',
        'sold_price' => 'integer',
        'sales_fee_rate' => 'decimal:2',
        'sales_fee' => 'integer',
        'shipping_fee' => 'integer',
        'profit' => 'integer',
        'sold_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isSold(): bool
    {
        return $this->status === 'sold';
    }
}
