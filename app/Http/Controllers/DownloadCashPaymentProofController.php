<?php

namespace App\Http\Controllers;

use App\Models\CashPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadCashPaymentProofController extends Controller
{
    public function __invoke(Request $request, CashPayment $payment): StreamedResponse
    {
        $user = $request->user();

        $isOwner = $payment->user_id === $user?->id;
        $isManager = in_array($user?->role, ['admin', 'super_admin'], true);

        if (! $isOwner && ! $isManager) {
            abort(403);
        }

        if (! $payment->proof_path) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($payment->proof_path)) {
            abort(404);
        }

        $extension = pathinfo($payment->proof_path, PATHINFO_EXTENSION);
        $datePart = $payment->date ? (string) str_replace('-', '', $payment->date) : now()->format('Ymd');
        $fileName = Str::slug('bukti-pembayaran-' . $datePart . '-' . ($payment->user->name ?? '')) . '.' . $extension;

        return $disk->download($payment->proof_path, $fileName);
    }
}
