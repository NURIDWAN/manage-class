<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'recorded_by',
        'description',
        'amount',
        'date',
        'status',
    ];

    protected static function booted(): void
    {
        $refresh = static function (): void {
            ClassFund::refreshTotals();
        };

        static::saved(function () use ($refresh): void {
            $refresh();
        });

        static::deleted(function () use ($refresh): void {
            $refresh();
        });
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}

