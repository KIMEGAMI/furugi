<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionItem extends Model
{
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
