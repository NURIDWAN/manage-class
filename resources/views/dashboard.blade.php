<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard Kelas</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        },
                    },
                },
            };
        </script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    </head>
    <body class="bg-slate-950 text-slate-100 min-h-screen">
        <div class="relative isolate overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-50 bg-gradient-to-br from-amber-400/25 via-slate-900 to-indigo-900 blur-3xl"></div>

            <x-site-navbar title="Manajemen Kelas" :cta-label="'Jadwal Minggu Ini'" :cta-href="route('schedule')" />

            <main class="px-6 pb-16 pt-10 sm:px-10">
                <section class="mx-auto grid max-w-6xl gap-8 rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur lg:grid-cols-[1.6fr,1fr]">
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-amber-400/20 px-3 py-1 text-xs font-semibold text-amber-200">Dashboard Anggota</span>
                            <span class="text-xs uppercase tracking-[0.35em] text-slate-300/80">Kelas Kompak</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-semibold text-white sm:text-4xl">
                                Halo, {{ $user->name }} 👋
                            </h1>
                            <p class="mt-3 text-sm leading-relaxed text-slate-300 sm:text-base">
                                Semua informasi penting kelas ada di sini. Cek perkembangan kas, agenda terdekat, dan pengumuman terbaru
                                agar tidak ketinggalan kabar.
                            </p>
                            @if (session('status') === 'profile-completed')
                                <div class="mt-4 rounded-2xl border border-emerald-400/40 bg-emerald-400/15 px-4 py-3 text-xs text-emerald-100">
                                    Data profil berhasil diperbarui. Selamat menggunakan dashboard!
                                </div>
                            @endif
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                                <p class="text-xs uppercase tracking-wide text-slate-300">Saldo Dana Kelas</p>
                                <p class="mt-2 text-3xl font-semibold text-amber-300">
                                    Rp {{ number_format($stats['fund_balance'] ?? 0, 0, ',', '.') }}
                                </p>
                                <p class="mt-3 text-xs text-slate-400">Total saldo per hari ini</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                                <p class="text-xs uppercase tracking-wide text-slate-300">Total Kas Masuk</p>
                                <p class="mt-2 text-3xl font-semibold text-emerald-300">
                                    Rp {{ number_format($stats['cash_total'] ?? 0, 0, ',', '.') }}
                                </p>
                                <p class="mt-3 text-xs text-slate-400">Akumulasi dari semua pembayaran kas</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex h-full flex-col justify-between gap-6 rounded-2xl border border-white/10 bg-slate-950/40 p-6">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Identitas Mahasiswa</p>
                            <div class="mt-4 space-y-2 text-sm">
                                <div class="flex items-center justify-between rounded-xl border border-white/5 bg-white/5 px-4 py-3">
                                    <span class="text-slate-300">NIM</span>
                                    <span class="font-semibold text-white">{{ $user->nim }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl border border-white/5 bg-white/5 px-4 py-3">
                                    <span class="text-slate-300">Kelas</span>
                                    <span class="font-semibold text-white">{{ $user->kelas }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl border border-white/5 bg-white/5 px-4 py-3">
                                    <span class="text-slate-300">No. HP</span>
                                    <span class="font-semibold text-white">
                                        {{ $user->no_hp ?: 'Belum diisi' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <a
                            href="{{ route('profile.complete') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-amber-400/40 bg-amber-400/20 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-amber-200 transition hover:bg-amber-400/30"
                        >
                            Perbarui Data Profil
                        </a>
                    </div>
                </section>

                @php
                    $defaultTarget = $weeklyTargetAmount * $weeksInMonth;
                    $summary = $currentUserSummary ?? [
                        'target' => $defaultTarget,
                        'paid' => 0,
                        'remaining' => $defaultTarget,
                    ];
                    $progress = $summary['target'] > 0 ? min(100, round(($summary['paid'] / $summary['target']) * 100)) : 0;
                @endphp

                <section class="mx-auto mt-10 grid max-w-6xl gap-6 lg:grid-cols-[1.6fr,1fr]">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-white">Riwayat Kas Bulan {{ $monthLabel }}</h2>
                                <p class="mt-1 text-xs text-slate-300">
                                    Iuran kas mingguan Rp {{ number_format($weeklyTargetAmount, 0, ',', '.') }} • {{ $weeksInMonth }} pertemuan bulan ini
                                </p>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs uppercase tracking-wide text-slate-200">
                                Data terbaru
                            </span>
                        </div>

                        <div class="mt-6 overflow-hidden rounded-2xl border border-white/5">
                            <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-200">
                                <thead class="bg-slate-950/40 text-xs uppercase tracking-wide text-slate-400">
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

                    <div class="grid gap-6">
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-white">Status Kas Anda</h2>
                                <span class="text-xs uppercase tracking-wide text-slate-300">{{ $monthLabel }}</span>
                            </div>
                            <dl class="mt-5 space-y-3 text-sm text-slate-300">
                                <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 px-4 py-3">
                                    <dt>Target Bulanan</dt>
                                    <dd class="font-semibold text-white">Rp {{ number_format($summary['target'], 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 px-4 py-3">
                                    <dt>Total Dibayar</dt>
                                    <dd class="font-semibold text-emerald-200">Rp {{ number_format($summary['paid'], 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 px-4 py-3">
                                    <dt>Sisa Tagihan</dt>
                                    <dd class="font-semibold {{ $summary['remaining'] > 0 ? 'text-rose-200' : 'text-emerald-200' }}">
                                        Rp {{ number_format($summary['remaining'], 0, ',', '.') }}
                                    </dd>
                                </div>
                            </dl>
                            <div class="mt-6">
                                <p class="text-xs uppercase tracking-wide text-slate-400">Progres Pembayaran</p>
                                <div class="mt-3 h-3 w-full overflow-hidden rounded-full bg-slate-800/80">
                                    <div
                                        class="h-full rounded-full bg-emerald-400 transition-all"
                                        style="width: {{ $progress }}%;"
                                    ></div>
                                </div>
                                <p class="mt-2 text-xs text-slate-400">{{ $progress }}% terpenuhi</p>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-white">Sisa Tagihan Kelas</h2>
                                <span class="text-xs uppercase tracking-wide text-slate-300">{{ $monthLabel }}</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-300">
                                {{ $showsClassSummary ? 'Daftar anggota dengan sisa tagihan kas bulan ini. Member yang lunas ditampilkan di bagian paling bawah.' : 'Sisa tagihan kas pribadi Anda pada bulan ini.' }}
                            </p>
                            <div class="mt-4 max-h-64 overflow-auto rounded-2xl border border-white/5">
                                <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-200">
                                    <thead class="bg-slate-950/40 text-xs uppercase tracking-wide text-slate-400">
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
                    </div>
                </section>

                <section class="mx-auto mt-10 grid max-w-6xl gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    <x-dashboard-stat-card icon="megaphone" title="Pengumuman" :value="$stats['announcements'] ?? 0" />
                    <x-dashboard-stat-card icon="calendar" title="Kegiatan" :value="$stats['events'] ?? 0" />
                    <x-dashboard-stat-card icon="banknotes" title="Kas Masuk" :value="'Rp ' . number_format($stats['cash_total'] ?? 0, 0, ',', '.')" />
                    <x-dashboard-stat-card icon="shield" title="Status Kelas" value="Aktif" />
                </section>

                <section class="mx-auto mt-10 grid max-w-6xl gap-6 xl:grid-cols-[1.4fr,1fr]">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-white">Performa Kas</h2>
                                <p class="mt-1 text-xs text-slate-300">Lihat pergerakan kas bulanan dan mingguan</p>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs uppercase tracking-wide text-slate-200">Realtime</span>
                        </div>
                        <div class="mt-6 grid gap-6 lg:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-white">Tren Kas Bulanan</h3>
                                    <span class="text-[10px] uppercase tracking-wide text-slate-400">6 bulan</span>
                                </div>
                                <canvas id="monthlyChart" class="mt-4 h-52"></canvas>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-white">Rekap Kas Mingguan</h3>
                                    <span class="text-[10px] uppercase tracking-wide text-slate-400">8 minggu</span>
                                </div>
                                <canvas id="weeklyChart" class="mt-4 h-52"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6">
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-white">Agenda Terdekat</h2>
                                <a href="{{ route('schedule') }}" class="text-xs font-semibold uppercase tracking-wide text-amber-200 hover:text-amber-100">
                                    Lihat Jadwal
                                </a>
                            </div>
                            <ul class="mt-6 space-y-4">
                                @forelse ($upcomingEvents as $event)
                                    <li class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                        <p class="text-sm font-semibold text-white">{{ $event->title }}</p>
                                        <p class="mt-1 text-xs uppercase tracking-wide text-slate-400">
                                            {{ \Illuminate\Support\Carbon::parse($event->date)->translatedFormat('l, d F Y') }}
                                        </p>
                                        @if ($event->location)
                                            <p class="mt-2 text-sm text-slate-300">Lokasi: {{ $event->location }}</p>
                                        @endif
                                    </li>
                                @empty
                                    <li class="rounded-2xl border border-dashed border-white/20 bg-white/5 p-4 text-center text-sm text-slate-400">
                                        Belum ada agenda terdekat.
                                    </li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-white">Pengumuman Terbaru</h2>
                                <span class="text-xs uppercase tracking-wide text-slate-400">4 terakhir</span>
                            </div>
                            <ul class="mt-6 space-y-4">
                                @forelse ($recentAnnouncements as $announcement)
                                    <li class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                        <p class="text-sm font-semibold text-white">{{ $announcement->title }}</p>
                                        <p class="mt-1 text-xs uppercase tracking-wide text-slate-400">
                                            {{ $announcement->created_at->translatedFormat('d F Y • H:i') }}
                                        </p>
                                        <p class="mt-2 max-h-20 overflow-hidden text-sm text-slate-300 text-ellipsis">{{ $announcement->content }}</p>
                                    </li>
                                @empty
                                    <li class="rounded-2xl border border-dashed border-white/20 bg-white/5 p-4 text-center text-sm text-slate-400">
                                        Belum ada pengumuman baru.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </section>
            </main>
        </div>

        <script>
            const monthlyCtx = document.getElementById('monthlyChart');
            const monthlyData = @json($monthlyChart);
            const weeklyCtx = document.getElementById('weeklyChart');
            const weeklyData = @json($weeklyChart);

            if (monthlyCtx) {
                new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyData.labels,
                        datasets: [{
                            label: 'Total Kas Masuk',
                            data: monthlyData.data,
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.25)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#fbbf24',
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                ticks: {
                                    callback: value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value),
                                    color: '#cbd5f5',
                                },
                                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            },
                            x: {
                                ticks: { color: '#cbd5f5' },
                                grid: { display: false },
                            }
                        },
                        plugins: {
                            legend: { labels: { color: '#e2e8f0' } },
                        },
                    },
                });
            }

            if (weeklyCtx) {
                new Chart(weeklyCtx, {
                    type: 'bar',
                    data: {
                        labels: weeklyData.labels,
                        datasets: [{
                            label: 'Total Kas Masuk',
                            data: weeklyData.data,
                            backgroundColor: 'rgba(59, 130, 246, 0.55)',
                            borderColor: '#3b82f6',
                            borderWidth: 1.5,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                ticks: {
                                    callback: value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value),
                                    color: '#cbd5f5',
                                },
                                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                            },
                            x: {
                                ticks: { color: '#cbd5f5' },
                                grid: { display: false },
                            },
                        },
                        plugins: {
                            legend: { labels: { color: '#e2e8f0' } },
                        },
                    },
                });
            }
        </script>
    </body>
</html>
