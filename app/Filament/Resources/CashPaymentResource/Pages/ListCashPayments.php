<?php

namespace App\Filament\Resources\CashPaymentResource\Pages;

use App\Filament\Resources\CashPaymentResource;
use App\Filament\Resources\CashPaymentResource\Widgets\MonthlyOutstandingPayments;
use App\Models\CashPayment;
use App\Models\User;
use App\Support\Settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListCashPayments extends ListRecords
{
    protected static string $resource = CashPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadReport')
                ->label('Unduh Laporan PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $today = Carbon::today();
                    $currentMonthKey = $today->format('Y-m');
                    $monthLabel = $today->translatedFormat('F Y');

                    $payments = CashPayment::query()
                        ->with('user')
                        ->where('status', 'confirmed')
                        ->whereNotNull('date')
                        ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$currentMonthKey])
                        ->orderBy('date')
                        ->get();

                    $targetPerMember = Settings::weeklyCashAmount() * 4;

                    $paymentsByUser = CashPayment::query()
                        ->selectRaw('user_id, SUM(amount) as total_paid')
                        ->where('status', 'confirmed')
                        ->whereNotNull('date')
                        ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$currentMonthKey])
                        ->groupBy('user_id')
                        ->pluck('total_paid', 'user_id');

                    $members = User::query()
                        ->orderBy('name')
                        ->get()
                        ->map(function (User $member) use ($paymentsByUser, $targetPerMember): array {
                            $paid = (int) ($paymentsByUser[$member->id] ?? 0);

                            return [
                                'name' => $member->name,
                                'nim' => $member->nim,
                                'kelas' => $member->kelas,
                                'paid' => $paid,
                                'remaining' => max(0, $targetPerMember - $paid),
                            ];
                        });

                    $outstandingMembers = $members->filter(fn (array $member): bool => $member['remaining'] > 0)->values();

                    $pdf = Pdf::loadView('pdf.cash-payments-report', [
                        'monthLabel' => $monthLabel,
                        'generatedAt' => Carbon::now(),
                        'payments' => $payments,
                        'totalCollected' => $payments->sum('amount'),
                        'targetPerMember' => $targetPerMember,
                        'outstandingMembers' => $outstandingMembers,
                    ])->setPaper('a4', 'portrait');

                    $filename = sprintf('laporan-kas-%s.pdf', $today->format('Y-m'));

                    return response()->streamDownload(function () use ($pdf): void {
                        echo $pdf->output();
                    }, $filename);
                }),
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MonthlyOutstandingPayments::class,
        ];
    }
}
