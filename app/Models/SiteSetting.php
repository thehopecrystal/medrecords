<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $primaryKey = 'key';
    public    $incrementing = false;
    protected $keyType      = 'string';
    protected $fillable     = ['key', 'value'];

    private static array $cache = [];

    public static function get(string $key, string $default = ''): string
    {
        if (!array_key_exists($key, self::$cache)) {
            self::$cache[$key] = static::where('key', $key)->value('value') ?? $default;
        }
        return self::$cache[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        self::$cache[$key] = $value ?? '';
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::set($key, $value);
        }
    }

    /** Load all settings at once — avoids N queries per page. */
    public static function loadAll(): void
    {
        self::$cache = static::pluck('value', 'key')->toArray();
    }
}
