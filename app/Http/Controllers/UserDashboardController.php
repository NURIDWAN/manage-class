<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\CashPayment;
use App\Models\ClassFund;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if (! $user?->nim) {
            return redirect()
                ->route('profile.complete')
                ->with('status', 'profile-required');
        }

        $today = Carbon::today();
        $currentMonth = $today->format('Y-m');

        $stats = [
            'announcements' => Announcement::count(),
            'events' => Event::count(),
            'cash_total' => CashPayment::sum('amount'),
            'fund_balance' => ClassFund::sum('total_balance'),
        ];

        $monthlyChart = $this->buildMonthlyCashChart($today);
        $weeklyChart = $this->buildWeeklyCashChart($today);
        $monthLabel = $today->translatedFormat('F Y');
        $weeksInMonth = 4;
        $weeklyTargetAmount = 10000;
        $monthlyTargetAmount = $weeklyTargetAmount * $weeksInMonth;

        $cashPayments = CashPayment::query()
            ->where('user_id', $user->id)
            ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$currentMonth])
            ->orderBy('date', 'desc')
            ->get();

        $paymentsByUser = CashPayment::query()
            ->selectRaw('user_id, SUM(amount) as total_paid')
            ->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$currentMonth])
            ->groupBy('user_id')
            ->pluck('total_paid', 'user_id');

        $allUsers = $user->role === 'super_admin'
            ? User::query()->orderBy('name')->get()
            : collect([$user]);

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

        $currentUserSummary = $remainingPayments->firstWhere('user_id', $user->id) ?? null;

        $upcomingEvents = Event::query()
            ->whereDate('date', '>=', $today)
            ->orderBy('date')
            ->limit(4)
            ->get();

        $recentAnnouncements = Announcement::query()
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
            'monthlyChart' => $monthlyChart,
            'weeklyChart' => $weeklyChart,
            'cashPayments' => $cashPayments,
            'remainingPayments' => $remainingPayments,
            'monthLabel' => $monthLabel,
            'currentUserSummary' => $currentUserSummary,
            'showsClassSummary' => $user->role === 'super_admin',
            'weeklyTargetAmount' => $weeklyTargetAmount,
            'weeksInMonth' => $weeksInMonth,
            'upcomingEvents' => $upcomingEvents,
            'recentAnnouncements' => $recentAnnouncements,
        ]);
    }

    protected function buildMonthlyCashChart(Carbon $referenceDate): array
    {
        $endDate = $referenceDate->copy()->endOfMonth();
        $startDate = $endDate->copy()->subMonths(5)->startOfMonth();

        $rawData = CashPayment::query()
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as period, SUM(amount) as total')
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('date')
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

    protected function buildWeeklyCashChart(Carbon $referenceDate): array
    {
        $endDate = $referenceDate->copy()->endOfWeek();
        $startDate = $endDate->copy()->subWeeks(7)->startOfWeek();

        $rawData = CashPayment::query()
            ->selectRaw('DATE_FORMAT(date, "%x-%v") as period, SUM(amount) as total')
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('date')
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
}
