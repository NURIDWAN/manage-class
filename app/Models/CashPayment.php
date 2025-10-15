<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'date',
        'status',
        'payment_method',
        'proof_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        $refresh = function (): void {
            ClassFund::refreshTotals();
        };

        static::saved(function () use ($refresh): void {
            $refresh();
        });

        static::deleted(function () use ($refresh): void {
            $refresh();
        });
    }
}
