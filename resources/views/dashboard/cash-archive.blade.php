@extends('layouts.dashboard')

@section('content')
    @php
        $payments = $paymentArchive;
    @endphp

    <section class="mx-auto flex w-full max-w-6xl flex-col gap-8">
        <header class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-sky-300/80">Arsip</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white sm:text-4xl">Arsip Bukti Pembayaran</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300 sm:text-base">
                        Riwayat lengkap bukti pembayaran kas yang pernah Anda unggah. Unduh ulang jika memerlukan salinan.
                    </p>
                </div>
                <a
                    href="{{ route('dashboard.cash') }}"
                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition"
                >
                    &larr; Kembali ke Ringkasan
                </a>
            </div>
        </header>

        <article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Daftar Bukti Pembayaran</h2>
                    <p class="text-xs text-slate-300">Menampilkan hingga 60 entri terbaru sesuai urutan waktu.</p>
                </div>
                <span class="text-xs uppercase tracking-wide text-slate-300">Total entri: {{ $payments->count() }}</span>
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
                <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-200">
                    <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Metode</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($payments as $payment)
                            <tr class="bg-white/5">
                                <td class="px-4 py-3 text-sm font-medium text-white">
                                    {{ $payment->date ? \Illuminate\Support\Carbon::parse($payment->date)->translatedFormat('d F Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-amber-200">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-xs uppercase tracking-wide text-slate-300">
                                    {{ $payment->payment_method === 'cash' ? 'Tunai' : 'Transfer' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide',
                                        'bg-emerald-500/10 text-emerald-200 border border-emerald-400/40' => $payment->status === 'confirmed',
                                        'bg-amber-500/10 text-amber-200 border border-amber-400/40' => $payment->status !== 'confirmed',
                                    ])>
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($payment->proof_path)
                                        <a
                                            href="{{ route('dashboard.cash.payments.download', $payment) }}"
                                            class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition"
                                        >
                                            Unduh Bukti
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">Tidak ada berkas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-400">
                                    Belum ada arsip pembayaran yang bisa ditampilkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
