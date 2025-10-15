<?php

namespace App\Filament\Resources\CashPaymentResource\Widgets;

use App\Models\User;
use App\Support\Settings;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class MonthlyOutstandingPayments extends TableWidget
{
    protected static ?string $heading = 'Belum Lunas Bulan Ini';

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        $currentMonth = Carbon::today()->format('Y-m');

        return User::query()
            ->select('users.*')
            ->withSum([
                'cashPayments as paid_this_month' => function (Builder $query) use ($currentMonth): void {
                    $query->where('status', 'confirmed')
                        ->whereNotNull('date')
                        ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$currentMonth]);
                },
            ], 'amount')
            ->havingRaw('COALESCE(paid_this_month, 0) < ?', [$this->getMonthlyTarget()])
            ->orderBy('name');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Nama')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('nim')
                ->label('NIM')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('paid_this_month')
                ->label('Sudah Bayar')
                ->state(fn (User $record): int => (int) ($record->paid_this_month ?? 0))
                ->money('IDR', true)
                ->sortable(),
            Tables\Columns\TextColumn::make('remaining')
                ->label('Sisa Tagihan')
                ->state(fn (User $record): int => $this->calculateRemaining((int) ($record->paid_this_month ?? 0)))
                ->money('IDR', true)
                ->color(fn (User $record): string => $this->calculateRemaining((int) ($record->paid_this_month ?? 0)) > 0 ? 'danger' : 'success')
                ->sortable(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Semua anggota sudah lunas bulan ini.';
    }

    protected function getMonthlyTarget(): int
    {
        return Settings::weeklyCashAmount() * 4;
    }

    protected function calculateRemaining(int $paid): int
    {
        return max(0, $this->getMonthlyTarget() - $paid);
    }
}
