<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'label'];

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
                $setting = static::where('key', $key)->first();
                return $setting?->value ?? $default;
            });
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }
}
