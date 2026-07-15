<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    public const KEY_MAINTENANCE_ENABLED = 'maintenance_enabled';

    protected $fillable = [
        'key',
        'value',
    ];
}
