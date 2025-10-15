<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ClassFund extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_balance',
        'cash_in_total',
        'cash_out_total',
        'last_update',
    ];

    public static function refreshTotals(): void
    {
        $totalIn = CashPayment::query()
            ->where('status', 'confirmed')
            ->sum('amount');

        $totalOut = CashExpense::query()
            ->where('status', 'confirmed')
            ->sum('amount');

        $fund = static::query()->orderBy('id')->first() ?? new static();

        $fund->cash_in_total = $totalIn;
        $fund->cash_out_total = $totalOut;
        $fund->total_balance = $totalIn - $totalOut;
        $fund->last_update = Carbon::today();

        $fund->save();
    }
}
