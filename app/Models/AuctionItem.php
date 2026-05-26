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
        'image_path',
        'sold_image_path',
        'status',
        'purchase_price',
        'sold_price',
        'profit',
        'sold_at',
    ];

    protected $casts = [
        'purchase_price' => 'integer',
        'sold_price' => 'integer',
        'profit' => 'integer',
        'sold_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSold(): bool
    {
        return $this->status === 'sold';
    }
}