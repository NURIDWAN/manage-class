<?php

namespace App\Http\Controllers;

use App\Support\DashboardDataBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashPaymentArchiveController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user?->nim) {
            return redirect()
                ->route('profile.complete')
                ->with('status', 'profile-required');
        }

        $builder = new DashboardDataBuilder($user);

        return view('dashboard.cash-archive', [
            'user' => $user,
            'paymentArchive' => $builder->paymentArchive(60),
            'pageTitle' => 'Arsip Pembayaran Kas',
        ]);
    }
}
