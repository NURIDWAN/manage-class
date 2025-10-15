<?php

namespace App\Filament\Widgets;

use App\Models\CashPayment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CashPaymentChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pembayaran Kas';

    protected static ?string $pollingInterval = '60s';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $endDate = Carbon::today()->endOfMonth();
        $startDate = (clone $endDate)->subMonths(5)->startOfMonth();

        $rawData = CashPayment::query()
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as period, SUM(amount) as total')
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('date')
            ->where('status', 'confirmed')
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $labels = [];
        $values = [];

        $cursor = $startDate->copy();
        while ($cursor <= $endDate) {
            $periodKey = $cursor->format('Y-m');
            $labels[] = $cursor->translatedFormat('M Y');
            $values[] = (float) ($rawData[$periodKey] ?? 0);
            $cursor->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Kas Masuk',
                    'data' => $values,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.25)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
