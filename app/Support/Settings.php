<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    /**
     * Retrieve a setting value with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = "settings." . $key;

        $value = Cache::remember($cacheKey, now()->addHours(2), function () use ($key) {
            return Setting::query()->where('key', $key)->value('value');
        });

        if ($value === null || $value === '') {
            $defaults = config('classmanager.defaults', []);
            return $defaults[$key] ?? $default;
        }

        return $value;
    }

    public static function appName(): string
    {
        return (string) static::get('app_name', config('classmanager.defaults.app_name'));
    }

    public static function weeklyCashAmount(): int
    {
        return (int) static::get('weekly_cash_amount', config('classmanager.defaults.weekly_cash_amount'));
    }

    public static function githubUrl(): ?string
    {
        $url = static::get('github_url', config('classmanager.defaults.github_url'));

        return $url ? (string) $url : null;
    }

    public static function footerText(): string
    {
        return (string) static::get('footer_text', config('classmanager.defaults.footer_text'));
    }

}
