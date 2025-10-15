<?php

namespace Tests\Unit\Models;

use App\Models\CashExpense;
use App\Models\CashPayment;
use App\Models\ClassFund;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ClassFundTest extends TestCase
{
    use RefreshDatabase;

    public function test_refresh_totals_aggregates_confirmed_transactions(): void
    {
        Carbon::setTestNow('2024-11-15');

        $user = User::factory()->create();

        CashPayment::factory()->confirmed()->create([
            'user_id' => $user->id,
            'amount' => 15000,
            'date' => Carbon::today()->toDateString(),
        ]);

        CashPayment::factory()->pending()->create([
            'user_id' => $user->id,
            'amount' => 5000,
            'date' => Carbon::today()->toDateString(),
        ]);

        CashExpense::factory()->create([
            'amount' => 4000,
            'date' => Carbon::today()->toDateString(),
            'status' => 'confirmed',
        ]);

        CashExpense::factory()->pending()->create([
            'amount' => 3000,
            'date' => Carbon::today()->toDateString(),
        ]);

        ClassFund::refreshTotals();

        $fund = ClassFund::first();

        $this->assertSame(15000, $fund->cash_in_total);
        $this->assertSame(4000, $fund->cash_out_total);
        $this->assertSame(11000, $fund->total_balance);
        $this->assertSame(Carbon::today()->toDateString(), $fund->last_update->toDateString());

        Carbon::setTestNow();
    }

    public function test_cash_payment_events_trigger_refresh(): void
    {
        $user = User::factory()->create();

        $payment = CashPayment::factory()->pending()->create([
            'user_id' => $user->id,
            'amount' => 8000,
            'date' => now()->toDateString(),
        ]);

        $fund = ClassFund::first();
        $this->assertSame(0, $fund->cash_in_total);

        $payment->update(['status' => 'confirmed']);

        $fund->refresh();

        $this->assertSame(8000, $fund->cash_in_total);

        $payment->delete();

        $fund->refresh();
        $this->assertSame(0, $fund->cash_in_total);
    }

    public function test_cash_expense_events_trigger_refresh(): void
    {
        $expense = CashExpense::factory()->pending()->create([
            'amount' => 5000,
            'date' => now()->toDateString(),
        ]);

        $fund = ClassFund::first();
        $this->assertSame(0, $fund->cash_out_total);

        $expense->update(['status' => 'confirmed']);

        $fund->refresh();
        $this->assertSame(5000, $fund->cash_out_total);

        $expense->delete();
        $fund->refresh();
        $this->assertSame(0, $fund->cash_out_total);
    }
}
