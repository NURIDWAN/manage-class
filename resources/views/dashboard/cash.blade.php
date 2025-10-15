@extends('layouts.dashboard')

@section('content')
    @php
        $summary = $cashSummary['currentUserSummary'];
        $progress = $cashSummary['progress'];
        $cashPayments = $cashSummary['cashPayments'];
        $remainingPayments = $cashSummary['remainingPayments'];
        $showsClassSummary = $cashSummary['showsClassSummary'];
    @endphp

    <section class="mx-auto flex w-full max-w-6xl flex-col gap-8">
        <header class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-emerald-300/80">Keuangan</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white sm:text-4xl">Ringkasan Uang Kas</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300 sm:text-base">
                        Detail pembayaran kas, progres bulanan, dan daftar anggota yang masih memiliki tagihan.
                    </p>
                </div>
                <span class="rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200">
                    {{ $cashSummary['monthLabel'] }}
                </span>
            </div>
        </header>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Target Bulanan</p>
                <p class="mt-2 text-3xl font-semibold text-white">
                    Rp {{ number_format($summary['target'], 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">{{ $cashSummary['weeksInMonth'] }} pertemuan • Rp {{ number_format($cashSummary['weeklyTargetAmount'], 0, ',', '.') }}/minggu</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Total Dibayar</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-300">
                    Rp {{ number_format($summary['paid'], 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Pembayaran yang sudah tercatat</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Sisa Tagihan</p>
                <p class="mt-2 text-3xl font-semibold {{ $summary['remaining'] > 0 ? 'text-rose-300' : 'text-emerald-300' }}">
                    Rp {{ number_format($summary['remaining'], 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Nominal yang masih harus dibayar</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Progres Bulanan</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $progress }}%</p>
                <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-800/80">
                    <div class="h-full rounded-full bg-emerald-400 transition-all" style="width: {{ $progress }}%;"></div>
                </div>
                <p class="mt-3 text-xs text-slate-400">Target diikuti otomatis saat pembayaran masuk</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.5fr,1fr]">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-semibold text-white">Riwayat Pembayaran Bulan Ini</h2>
                    <span class="text-xs uppercase tracking-wide text-slate-300">{{ $cashSummary['monthLabel'] }}</span>
                </div>

                <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-200">
                        <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Jumlah</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($cashPayments as $payment)
                                <tr class="bg-white/5">
                                    <td class="px-4 py-3 text-sm font-medium text-white">
                                        {{ $payment->date ? \Illuminate\Support\Carbon::parse($payment->date)->translatedFormat('d F Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-amber-200">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-400">
                                        Belum ada pembayaran kas pada bulan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-white">Sisa Tagihan</h2>
                    <span class="text-xs uppercase tracking-wide text-slate-300">{{ $cashSummary['monthLabel'] }}</span>
                </div>
                <p class="mt-2 text-xs text-slate-300">
                    {{ $showsClassSummary ? 'Daftar anggota dengan sisa tagihan bulan ini.' : 'Sisa tagihan pribadi Anda.' }}
                </p>

                <div class="mt-4 max-h-72 overflow-auto rounded-2xl border border-white/10">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-200">
                        <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">NIM</th>
                                <th class="px-4 py-3">Sisa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($remainingPayments as $payment)
                                <tr @class([
                                    'bg-white/5',
                                    'bg-rose-500/10' => $payment['remaining'] > 0,
                                    'ring-1 ring-emerald-400/40' => $payment['user_id'] === $user->id && $payment['remaining'] === 0,
                                ])>
                                    <td class="px-4 py-3 text-sm font-medium text-white">
                                        {{ $payment['name'] ?? 'Tidak diketahui' }}
                                        @if (! empty($payment['kelas']))
                                            <span class="ml-2 text-xs text-slate-400">({{ $payment['kelas'] }})</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-300">{{ $payment['nim'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold {{ $payment['remaining'] > 0 ? 'text-rose-200' : 'text-emerald-200' }}">
                                        Rp {{ number_format($payment['remaining'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-400">
                                        Data kas belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </section>
@endsection

