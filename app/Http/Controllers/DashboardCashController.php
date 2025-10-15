<?php

namespace App\Http\Controllers;

use App\Support\DashboardDataBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardCashController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if (! $user?->nim) {
            return redirect()
                ->route('profile.complete')
                ->with('status', 'profile-required');
        }

        $builder = new DashboardDataBuilder($user);

        return view('dashboard.cash', [
            'user' => $user,
            'stats' => $builder->stats(),
            'cashSummary' => $builder->cashSummary(),
            'announcementBanner' => $builder->latestAnnouncementBanner(),
            'pageTitle' => 'Uang Kas',
        ]);
    }
}
