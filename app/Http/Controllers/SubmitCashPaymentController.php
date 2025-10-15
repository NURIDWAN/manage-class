<?php

namespace App\Http\Controllers;

use App\Models\CashPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SubmitCashPaymentController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user?->nim) {
            return redirect()
                ->route('profile.complete')
                ->with('status', 'profile-required');
        }

        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1000'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', Rule::in(['cash', 'transfer'])],
            'proof' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'],
        ], [
            'proof.required' => 'Bukti pembayaran wajib diunggah.',
            'proof.mimes' => 'Bukti pembayaran harus berupa JPG, JPEG, PNG, atau PDF.',
            'proof.max' => 'Ukuran bukti pembayaran maksimal 5MB.',
        ]);

        $proofPath = $request->file('proof')->store('cash-payment-proofs', 'public');

        CashPayment::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'proof_path' => $proofPath,
        ]);

        return redirect()
            ->route('dashboard.cash')
            ->with('status', 'cash-payment-submitted');
    }
}
