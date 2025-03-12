<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the value of the setting by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null): mixed
    {
        static $cache = [];

        if (! isset($cache[$key])) {
            $setting = self::where('key', $key)
                ->orderByDesc('start_date')
                ->first();

            $cache[$key] = $setting ? $setting->value : $default;
        }

        return $cache[$key];
    }
}
