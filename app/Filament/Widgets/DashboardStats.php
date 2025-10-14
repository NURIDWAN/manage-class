<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use App\Models\CashPayment;
use App\Models\ClassFund;
use App\Models\Event;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class DashboardStats extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '60s';

    protected function getCards(): array
    {
        return [
            Card::make('Total Pengumuman', Announcement::count())
                ->description('Jumlah pengumuman aktif')
                ->icon('heroicon-o-megaphone'),
            Card::make('Kegiatan Terjadwal', Event::count())
                ->description('Total kegiatan kelas')
                ->icon('heroicon-o-calendar-days'),
            Card::make('Pembayaran Kas', Number::currency(CashPayment::sum('amount'), 'IDR'))
                ->description('Nominal pembayaran kas tercatat')
                ->icon('heroicon-o-banknotes'),
            Card::make('Saldo Dana Kelas', Number::currency(ClassFund::sum('total_balance'), 'IDR'))
                ->description('Total dana kelas terkumpul')
                ->icon('heroicon-o-presentation-chart-line'),
        ];
    }
}
