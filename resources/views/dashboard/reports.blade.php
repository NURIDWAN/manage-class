@extends('layouts.dashboard')

@section('content')
    @php
        $monthly = $charts['monthly'] ?? ['labels' => [], 'data' => []];
        $weekly = $charts['weekly'] ?? ['labels' => [], 'data' => []];
        $summary = $cashSummary['currentUserSummary'];
        $payments = $reportData['payments'] ?? collect();
        $expenses = $reportData['expenses'] ?? collect();
        $totalIn = $reportData['totalIn'] ?? 0;
        $totalOut = $reportData['totalOut'] ?? 0;
        $netBalance = $reportData['net'] ?? 0;
        $selectedMonthValue = $monthValue ?? $cashSummary['monthValue'] ?? now()->format('Y-m');
    @endphp

    <section class="mx-auto flex w-full max-w-6xl flex-col gap-8">
        <header class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-sky-300/80">Laporan</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white sm:text-4xl">Analitik Uang Kas</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300 sm:text-base">
                        Pantau tren kas masuk, pengeluaran kas, progres pembayaran pribadi, dan unduh laporan PDF untuk arsip keuangan kelas.
                    </p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <form method="GET" class="flex w-full flex-wrap items-center gap-2 rounded-2xl border border-white/10 bg-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 sm:w-auto">
                        <label class="flex items-center gap-2">
                            <span class="text-[10px] tracking-[0.3em] text-slate-300">Periode</span>
                            <select name="month" class="rounded-xl border border-white/20 bg-slate-900/60 px-3 py-2 text-xs font-semibold text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/60">
                                @foreach ($monthOptions as $option)
                                    <option value="{{ $option['value'] }}" @selected($option['value'] === $selectedMonthValue)>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button type="submit" class="inline-flex items-center rounded-full bg-sky-500/20 px-3 py-2 text-[11px] font-semibold text-sky-100 transition hover:bg-sky-500/30">
                            Terapkan
                        </button>
                    </form>
                    <div class="flex flex-wrap gap-2 sm:justify-end">
                        <a
                            href="{{ route('dashboard.reports.download', ['month' => $selectedMonthValue]) }}"
                            class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition"
                        >
                            Unduh PDF
                        </a>
                        <a
                            href="{{ route('dashboard.cash') }}"
                            class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition"
                        >
                            Kelola Uang Kas
                        </a>
                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition"
                        >
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Saldo Dana</p>
                <p class="mt-2 text-3xl font-semibold text-amber-300">
                    Rp {{ number_format($stats['fund_balance'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Per {{ $cashSummary['monthLabel'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Kas Masuk</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-300">
                    Rp {{ number_format($stats['cash_total'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Terkonfirmasi</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Kas Keluar</p>
                <p class="mt-2 text-3xl font-semibold text-rose-300">
                    Rp {{ number_format($stats['cash_out_total'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Telah terealisasi</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Progres Pribadi</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $cashSummary['progress'] }}%</p>
                <p class="mt-3 text-xs text-slate-400">Target Rp {{ number_format($summary['target'], 0, ',', '.') }}</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Daftar Kas Masuk</h2>
                    <span class="text-xs uppercase tracking-wide text-slate-300">{{ $cashSummary['monthLabel'] }}</span>
                </div>
                <div class="mt-5 max-h-96 overflow-auto rounded-2xl border border-white/10">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-200">
                        <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Metode</th>
                                <th class="px-4 py-3 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($payments as $payment)
                                <tr class="bg-white/5">
                                    <td class="px-4 py-3 text-xs font-semibold text-emerald-200">
                                        {{ $payment->date ? \Illuminate\Support\Carbon::parse($payment->date)->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-white">
                                        {{ $payment->user?->name ?? 'Tidak diketahui' }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-300">
                                        {{ $payment->payment_method === 'transfer' ? 'Transfer' : 'Tunai' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right text-emerald-200">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-400">
                                        Belum ada kas masuk pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Daftar Kas Keluar</h2>
                    <span class="text-xs uppercase tracking-wide text-slate-300">{{ $cashSummary['monthLabel'] }}</span>
                </div>
                <div class="mt-5 max-h-96 overflow-auto rounded-2xl border border-white/10">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-200">
                        <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Keterangan</th>
                                <th class="px-4 py-3 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($expenses as $expense)
                                <tr class="bg-white/5">
                                    <td class="px-4 py-3 text-xs font-semibold text-rose-200">
                                        {{ $expense->date ? \Illuminate\Support\Carbon::parse($expense->date)->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-white">
                                        {{ $expense->description }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right text-rose-200">
                                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-400">
                                        Belum ada kas keluar pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Kas Masuk Bulan Ini</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-300">
                    Rp {{ number_format($totalIn, 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Transaksi terkonfirmasi periode {{ $cashSummary['monthLabel'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Kas Keluar Bulan Ini</p>
                <p class="mt-2 text-2xl font-semibold text-rose-300">
                    Rp {{ number_format($totalOut, 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Pengeluaran yang sudah disahkan</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Saldo Bersih Bulan Ini</p>
                <p class="mt-2 text-2xl font-semibold {{ $netBalance >= 0 ? 'text-amber-200' : 'text-rose-200' }}">
                    Rp {{ number_format($netBalance, 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Selisih kas masuk dikurangi kas keluar</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.4fr,1fr]">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-semibold text-white">Tren Kas Bulanan</h2>
                    <span class="text-xs uppercase tracking-wide text-slate-300">6 bulan terakhir</span>
                </div>
                <div class="mt-6">
                    <canvas id="monthly-cash-chart" height="220"></canvas>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-semibold text-white">Kas Mingguan</h2>
                    <span class="text-xs uppercase tracking-wide text-slate-300">8 minggu terakhir</span>
                </div>
                <div class="mt-6">
                    <canvas id="weekly-cash-chart" height="220"></canvas>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr,1fr]">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-white">Ringkasan Pembayaran Pribadi</h2>
                <ul class="mt-5 grid gap-4 sm:grid-cols-2">
                    <li class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-300">Total Dibayar Bulan Ini</p>
                        <p class="mt-2 text-2xl font-semibold text-emerald-300">Rp {{ number_format($summary['paid'], 0, ',', '.') }}</p>
                    </li>
                    <li class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-300">Sisa Tagihan</p>
                        <p class="mt-2 text-2xl font-semibold {{ $summary['remaining'] > 0 ? 'text-rose-300' : 'text-emerald-300' }}">
                            Rp {{ number_format($summary['remaining'], 0, ',', '.') }}
                        </p>
                    </li>
                </ul>
                <p class="mt-5 text-xs text-slate-400">
                    Target bulanan dihitung dari {{ $cashSummary['weeksInMonth'] }} kali pertemuan dengan nominal Rp {{ number_format($cashSummary['weeklyTargetAmount'], 0, ',', '.') }} per minggu.
                </p>
            </div>

            <div class="grid gap-6">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                    <h2 class="text-lg font-semibold text-white">Agenda Terdekat</h2>
                    <ul class="mt-4 space-y-3">
                        @forelse ($upcomingEvents as $event)
                            <li class="rounded-2xl border border-white/10 bg-white/10 p-4">
                                <p class="text-sm font-semibold text-white">{{ $event->title }}</p>
                                <p class="text-xs text-slate-300">{{ $event->location ?? 'Lokasi menyusul' }}</p>
                                <p class="mt-2 text-[11px] uppercase tracking-wide text-slate-400">
                                    {{ $event->date ? \Illuminate\Support\Carbon::parse($event->date)->translatedFormat('d M Y') : '-' }}
                                </p>
                            </li>
                        @empty
                            <li class="rounded-2xl border border-dashed border-white/20 px-4 py-6 text-center text-sm text-slate-400">
                                Belum ada agenda yang tercatat.
                            </li>
                        @endforelse
                    </ul>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                    <h2 class="text-lg font-semibold text-white">Pengumuman Terbaru</h2>
                    <ul class="mt-4 space-y-3">
                        @forelse ($recentAnnouncements as $announcement)
                            @php
                                $excerpt = $announcement->excerpt
                                    ?? \Illuminate\Support\Str::limit(strip_tags($announcement->content ?? $announcement->description ?? ''), 120);
                            @endphp
                            <li class="rounded-2xl border border-white/10 bg-white/10 p-4">
                                <p class="text-sm font-semibold text-white">{{ $announcement->title }}</p>
                                <p class="mt-1 text-xs text-slate-300 leading-relaxed overflow-hidden">{{ $excerpt }}</p>
                                <p class="mt-2 text-[11px] uppercase tracking-wide text-slate-400">
                                    {{ $announcement->created_at?->translatedFormat('d M Y H:i') }}
                                </p>
                            </li>
                        @empty
                            <li class="rounded-2xl border border-dashed border-white/20 px-4 py-6 text-center text-sm text-slate-400">
                                Belum ada pengumuman.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>
    </section>
@endsection

@push('scripts')
    <script src=\"https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js\"></script>
    <script>
        const monthlyCtx = document.getElementById('monthly-cash-chart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: @json($monthly['labels']),
                    datasets: [
                        {
                            label: 'Total Kas Masuk',
                            data: @json($monthly['data']),
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.25)',
                            tension: 0.3,
                            fill: true,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            ticks: {
                                callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value),
                            },
                        },
                    },
                },
            });
        }

        const weeklyCtx = document.getElementById('weekly-cash-chart');
        if (weeklyCtx) {
            new Chart(weeklyCtx, {
                type: 'bar',
                data: {
                    labels: @json($weekly['labels']),
                    datasets: [
                        {
                            label: 'Total Kas Masuk',
                            data: @json($weekly['data']),
                            backgroundColor: 'rgba(59, 130, 246, 0.55)',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            ticks: {
                                callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value),
                            },
                        },
                    },
                },
            });
        }
    </script>
@endpush

