<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    public const KEY_MAINTENANCE_ENABLED = 'maintenance_enabled';

    public const KEY_DASHBOARD_NOTICE_ENABLED = 'dashboard_notice_enabled';

    public const KEY_DASHBOARD_NOTICE_TITLE = 'dashboard_notice_title';

    public const KEY_DASHBOARD_NOTICE_BODY = 'dashboard_notice_body';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function stringValue(string $key, string $default = ''): string
    {
        $value = self::query()
            ->where('key', $key)
            ->value('value');

        return is_string($value) ? $value : $default;
    }

    public static function booleanValue(string $key, bool $default = false): bool
    {
        $value = self::stringValue($key, $default ? '1' : '0');

        return $value === '1';
    }

    public static function putValue(string $key, ?string $value): void
    {
        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
