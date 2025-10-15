<?php

namespace App\Http\Controllers;

use App\Support\DashboardDataBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardReportController extends Controller
{
    public function index(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if (! $user?->nim) {
            return redirect()
                ->route('profile.complete')
                ->with('status', 'profile-required');
        }

        $reportDate = $this->resolveMonth($request->query('month'));

        $builder = new DashboardDataBuilder($user, $reportDate);
        $stats = $builder->stats();
        $cashSummary = $builder->cashSummary();
        $charts = $builder->chartData();
        $announcements = $builder->announcementsAndEvents();
        $reportData = $builder->cashReportData();

        return view('dashboard.reports', [
            'user' => $user,
            'stats' => $stats,
            'cashSummary' => $cashSummary,
            'charts' => $charts,
            'reportData' => $reportData,
            'monthOptions' => $builder->monthOptions(),
            'monthValue' => $reportData['monthValue'],
            'upcomingEvents' => $announcements['upcomingEvents'],
            'recentAnnouncements' => $announcements['recentAnnouncements'],
            'announcementBanner' => $builder->latestAnnouncementBanner(),
            'pageTitle' => 'Laporan Kas',
        ]);
    }

    public function download(Request $request)
    {
        $user = $request->user();

        if (! $user?->nim) {
            return redirect()
                ->route('profile.complete')
                ->with('status', 'profile-required');
        }

        $reportDate = $this->resolveMonth($request->query('month'));
        $builder = new DashboardDataBuilder($user, $reportDate);

        $reportData = $builder->cashReportData();
        $cashSummary = $builder->cashSummary();

        $outstandingMembers = collect($cashSummary['remainingPayments'])
            ->filter(fn (array $payment): bool => ($payment['remaining'] ?? 0) > 0)
            ->values();

        $targetPerMember = $cashSummary['weeklyTargetAmount'] * $cashSummary['weeksInMonth'];

        $pdf = Pdf::loadView('pdf.cash-payments-report', [
            'monthLabel' => $reportData['monthLabel'],
            'generatedAt' => Carbon::now(),
            'payments' => $reportData['payments'],
            'expenses' => $reportData['expenses'],
            'totalCollected' => $reportData['totalIn'],
            'totalExpenses' => $reportData['totalOut'],
            'netBalance' => $reportData['net'],
            'targetPerMember' => $targetPerMember,
            'outstandingMembers' => $outstandingMembers,
        ])->setPaper('a4', 'portrait');

        $filename = sprintf('laporan-kas-%s.pdf', $reportData['monthValue']);

        return response()->streamDownload(static function () use ($pdf): void {
            echo $pdf->output();
        }, $filename);
    }

    protected function resolveMonth(?string $value): Carbon
    {
        if (! $value) {
            return Carbon::today()->startOfMonth();
        }

        try {
            return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
        } catch (\Throwable $th) {
            return Carbon::today()->startOfMonth();
        }
    }
}
