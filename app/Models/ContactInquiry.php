<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name',
    'email',
    'subject',
    'message',
    'status',
    'handled_at',
    'handled_by',
])]
class ContactInquiry extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_HANDLED = 'handled';

    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, ContactInquiry> */
    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
