<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentPoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (PaymentPoster $poster): void {
            if (! $poster->is_active) {
                return;
            }

            $query = static::query();

            if ($poster->exists) {
                $query->whereKeyNot($poster->getKey());
            }

            $query->update(['is_active' => false]);
        });
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        $disk = Storage::disk('public');

        return $disk->exists($this->file_path)
            ? $disk->url($this->file_path)
            : null;
    }
}
