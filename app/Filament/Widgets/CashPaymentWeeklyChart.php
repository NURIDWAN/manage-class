<?php

namespace App\Filament\Widgets;

use App\Models\CashPayment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CashPaymentWeeklyChart extends ChartWidget
{
    protected static ?string $heading = 'Rekap Kas per Minggu';

    protected static ?string $pollingInterval = '60s';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $endDate = Carbon::today()->endOfWeek();
        $startDate = (clone $endDate)->subWeeks(7)->startOfWeek();

        $rawData = CashPayment::query()
            ->selectRaw('DATE_FORMAT(date, "%x-%v") as period, SUM(amount) as total')
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
            $isoYear = $cursor->isoWeekYear;
            $isoWeek = $cursor->isoWeek;
            $periodKey = sprintf('%s-%02d', $isoYear, $isoWeek);

            $labels[] = $cursor->isoFormat('WW [•] D MMM');
            $values[] = (float) ($rawData[$periodKey] ?? 0);

            $cursor->addWeek();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Kas Masuk',
                    'data' => $values,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.55)',
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
