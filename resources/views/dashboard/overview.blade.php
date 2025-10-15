@extends('layouts.dashboard')

@section('content')
    @php
        $appName = App\Support\Settings::appName();
        $summary = $cashSummary['currentUserSummary'];
        $progress = $cashSummary['progress'];
    @endphp

    <section class="mx-auto flex w-full max-w-6xl flex-col gap-8">
        <header class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.35em] text-amber-300/80">{{ $appName }}</p>
                    <h1 class="text-3xl font-semibold text-white sm:text-4xl">Halo, {{ $user->name }} 👋</h1>
                    <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                        Lihat ringkasan terbaru aktivitas kelas dan status pembayaranmu. Gunakan menu cepat di samping untuk berpindah ke halaman kas atau laporan detail.
                    </p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a
                        href="{{ route('dashboard.cash') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-emerald-400/40 bg-emerald-400/15 px-5 py-3 text-sm font-semibold text-emerald-100 transition hover:bg-emerald-400/25"
                    >
                        Kelola Uang Kas
                    </a>
                    <a
                        href="{{ route('dashboard.reports') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-sky-400/40 bg-sky-400/15 px-5 py-3 text-sm font-semibold text-sky-100 transition hover:bg-sky-400/25"
                    >
                        Buka Laporan
                    </a>
                </div>
            </div>
        </header>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Saldo Dana</p>
                <p class="mt-2 text-3xl font-semibold text-amber-300">
                    Rp {{ number_format($stats['fund_balance'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Total dana kelas saat ini</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Kas Masuk</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-300">
                    Rp {{ number_format($stats['cash_total'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Telah dikonfirmasi</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Kas Keluar</p>
                <p class="mt-2 text-3xl font-semibold text-rose-300">
                    Rp {{ number_format($stats['cash_out_total'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-3 text-xs text-slate-400">Pengeluaran terealisasi</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow">
                <p class="text-xs uppercase tracking-wide text-slate-300">Target Kas Pribadi</p>
                <p class="mt-2 text-3xl font-semibold text-white">
                    {{ $progress }}%
                </p>
                <p class="mt-3 text-xs text-slate-400">
                    Bulan {{ $cashSummary['monthLabel'] }}
                </p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr,1fr]">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Status Kas Anda</h2>
                        <p class="text-xs text-slate-300">
                            Target mingguan Rp {{ number_format($cashSummary['weeklyTargetAmount'], 0, ',', '.') }} • {{ $cashSummary['weeksInMonth'] }} pertemuan
                        </p>
                    </div>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs uppercase tracking-wide text-slate-200">
                        {{ $cashSummary['monthLabel'] }}
                    </span>
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-300">Target Bulanan</dt>
                        <dd class="mt-2 text-lg font-semibold text-white">
                            Rp {{ number_format($summary['target'], 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-300">Total Dibayar</dt>
                        <dd class="mt-2 text-lg font-semibold text-emerald-200">
                            Rp {{ number_format($summary['paid'], 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-300">Sisa Tagihan</dt>
                        <dd class="mt-2 text-lg font-semibold {{ $summary['remaining'] > 0 ? 'text-rose-200' : 'text-emerald-200' }}">
                            Rp {{ number_format($summary['remaining'], 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 space-y-3">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Progres Pembayaran</p>
                    <div class="h-3 w-full overflow-hidden rounded-full bg-slate-800/80">
                        <div class="h-full rounded-full bg-emerald-400 transition-all" style="width: {{ $progress }}%;"></div>
                    </div>
                    <p class="text-xs text-slate-400">{{ $progress }}% terpenuhi bulan ini.</p>
                </div>
            </div>

            <div class="grid gap-6">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                    <h2 class="text-lg font-semibold text-white">Agenda Terdekat</h2>
                    <ul class="mt-4 space-y-3">
                        @forelse ($upcomingEvents as $event)
                            <li class="flex items-start justify-between gap-3 rounded-2xl border border-white/10 bg-white/10 p-4">
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $event->title }}</p>
                                    <p class="text-xs text-slate-300">{{ $event->location ?? 'Lokasi menyusul' }}</p>
                                </div>
                                <span class="text-xs font-medium text-amber-200">
                                    {{ \Illuminate\Support\Carbon::parse($event->date)->translatedFormat('d M Y') }}
                                </span>
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
