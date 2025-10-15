<?php

namespace App\Support;

use App\Models\Announcement;
use App\Models\CashExpense;
use App\Models\CashPayment;
use App\Models\ClassFund;
use App\Models\Event;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DashboardDataBuilder
{
    protected User $user;

    protected Carbon $now;

    protected Carbon $reportDate;

    protected string $currentMonth;

    public function __construct(User $user, ?Carbon $reportDate = null)
    {
        $this->user = $user;
        $this->now = Carbon::today();
        $this->reportDate = $reportDate?->copy()->startOfMonth() ?? $this->now->copy()->startOfMonth();
        $this->currentMonth = $this->reportDate->format('Y-m');
    }

    public function stats(): array
    {
        return [
            'announcements' => Announcement::count(),
            'events' => Event::count(),
            'cash_total' => (int) CashPayment::query()
                ->where('status', 'confirmed')
                ->sum('amount'),
            'cash_out_total' => (int) CashExpense::query()
                ->where('status', 'confirmed')
                ->sum('amount'),
            'fund_balance' => (int) (ClassFund::query()->value('total_balance') ?? 0),
        ];
    }

    public function cashSummary(): array
    {
        $weeksInMonth = 4;
        $weeklyTargetAmount = Settings::weeklyCashAmount();
        $monthlyTargetAmount = $weeklyTargetAmount * $weeksInMonth;
        $monthLabel = $this->reportDate->translatedFormat('F Y');

        $cashPayments = CashPayment::query()
            ->where('user_id', $this->user->id)
            ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$this->currentMonth])
            ->orderBy('date', 'desc')
            ->get();

        $paymentsByUser = CashPayment::query()
            ->selectRaw('user_id, SUM(amount) as total_paid')
            ->where('status', 'confirmed')
            ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$this->currentMonth])
            ->groupBy('user_id')
            ->pluck('total_paid', 'user_id');

        $allUsers = $this->user->role === 'super_admin'
            ? User::query()->orderBy('name')->get()
            : collect([$this->user]);

        $remainingPayments = $allUsers->map(function (User $student) use ($paymentsByUser, $monthlyTargetAmount) {
            $totalPaid = (int) ($paymentsByUser[$student->id] ?? 0);
            $remaining = max(0, $monthlyTargetAmount - $totalPaid);

            return [
                'user_id' => $student->id,
                'name' => $student->name,
                'nim' => $student->nim,
                'kelas' => $student->kelas,
                'paid' => (int) $totalPaid,
                'remaining' => (int) $remaining,
                'target' => $monthlyTargetAmount,
            ];
        })->sortByDesc(fn ($payment) => $payment['remaining'])->values();

        $currentUserSummary = $remainingPayments->firstWhere('user_id', $this->user->id) ?? [
            'target' => $monthlyTargetAmount,
            'paid' => 0,
            'remaining' => $monthlyTargetAmount,
        ];

        $progress = $currentUserSummary['target'] > 0
            ? min(100, (int) round(($currentUserSummary['paid'] / $currentUserSummary['target']) * 100))
            : 0;

        return [
            'weeksInMonth' => $weeksInMonth,
            'weeklyTargetAmount' => $weeklyTargetAmount,
            'cashPayments' => $cashPayments,
            'remainingPayments' => $remainingPayments,
            'currentUserSummary' => $currentUserSummary,
            'showsClassSummary' => $this->user->role === 'super_admin',
            'progress' => $progress,
            'monthLabel' => $monthLabel,
            'monthValue' => $this->currentMonth,
        ];
    }

    public function paymentArchive(int $limit = 30): Collection
    {
        return CashPayment::query()
            ->where('user_id', $this->user->id)
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function chartData(): array
    {
        return [
            'monthly' => $this->buildMonthlyCashChart(),
            'weekly' => $this->buildWeeklyCashChart(),
        ];
    }

    public function announcementsAndEvents(): array
    {
        $upcomingEvents = Event::query()
            ->whereDate('date', '>=', $this->now)
            ->orderBy('date')
            ->limit(4)
            ->get();

        $recentAnnouncements = Announcement::query()
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        return [
            'upcomingEvents' => $upcomingEvents,
            'recentAnnouncements' => $recentAnnouncements,
        ];
    }

    public function cashReportData(): array
    {
        $payments = CashPayment::query()
            ->with('user')
            ->where('status', 'confirmed')
            ->whereNotNull('date')
            ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$this->currentMonth])
            ->orderBy('date')
            ->get();

        $expenses = CashExpense::query()
            ->where('status', 'confirmed')
            ->whereNotNull('date')
            ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$this->currentMonth])
            ->orderBy('date')
            ->get();

        $totalIn = $payments->sum('amount');
        $totalOut = $expenses->sum('amount');

        return [
            'monthLabel' => $this->reportDate->translatedFormat('F Y'),
            'monthValue' => $this->currentMonth,
            'payments' => $payments,
            'expenses' => $expenses,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'net' => $totalIn - $totalOut,
        ];
    }

    public function monthOptions(int $limit = 12): array
    {
        $options = [];
        $cursor = $this->now->copy()->startOfMonth();

        for ($i = 0; $i < $limit; $i++) {
            $options[] = [
                'value' => $cursor->format('Y-m'),
                'label' => $cursor->translatedFormat('F Y'),
            ];

            $cursor->subMonth();
        }

        $hasSelected = collect($options)->contains(fn (array $option) => $option['value'] === $this->currentMonth);

        if (! $hasSelected) {
            $options[] = [
                'value' => $this->currentMonth,
                'label' => $this->reportDate->translatedFormat('F Y'),
            ];
        }

        return $options;
    }

    public function reportMonthValue(): string
    {
        return $this->currentMonth;
    }

    protected function buildMonthlyCashChart(): array
    {
        $endDate = $this->reportDate->copy()->endOfMonth();
        $startDate = $endDate->copy()->subMonths(5)->startOfMonth();

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
            $labels[] = $cursor->translatedFormat('M Y');
            $values[] = (float) ($rawData[$cursor->format('Y-m')] ?? 0);
            $cursor->addMonth();
        }

        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }

    protected function buildWeeklyCashChart(): array
    {
        $endDate = $this->reportDate->copy()->endOfMonth()->endOfWeek();
        $startDate = $endDate->copy()->subWeeks(7)->startOfWeek();

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
            $key = sprintf('%s-%02d', $cursor->isoWeekYear, $cursor->isoWeek);
            $labels[] = $cursor->isoFormat('WW [•] D MMM');
            $values[] = (float) ($rawData[$key] ?? 0);
            $cursor->addWeek();
        }

        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }

    public function latestAnnouncement(): ?Announcement
    {
        return Announcement::query()
            ->orderByDesc('created_at')
            ->first();
    }

    public function latestAnnouncementBanner(): ?array
    {
        $announcement = $this->latestAnnouncement();

        if (! $announcement) {
            return null;
        }

        $summary = $announcement->excerpt
            ?? Str::limit(strip_tags($announcement->content ?? $announcement->description ?? ''), 140);

        return [
            'title' => $announcement->title,
            'summary' => $summary,
            'published_at' => $announcement->created_at?->translatedFormat('d M Y H:i'),
            'url' => null,
        ];
    }
}
