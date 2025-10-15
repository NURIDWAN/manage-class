<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected static function booted(): void
    {
        static::saved(function (Setting $setting): void {
            
            cache()->forget('settings.' . $setting->key);
        });

        static::deleted(function (Setting $setting): void {
            cache()->forget('settings.' . $setting->key);
        });
    }
}
