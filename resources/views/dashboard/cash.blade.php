@extends('layouts.dashboard')

@section('content')
    @php
        $summary = $cashSummary['currentUserSummary'];
        $progress = $cashSummary['progress'];
        $cashPayments = $cashSummary['cashPayments'];
        $remainingPayments = $cashSummary['remainingPayments'];
        $showsClassSummary = $cashSummary['showsClassSummary'];
        $posterUrl = $paymentPosterUrl ?? null;
        $defaultAmount = old('amount', $cashSummary['weeklyTargetAmount']);
        $defaultDate = old('date', now()->toDateString());
        $selectedMethod = old('payment_method', 'transfer');
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
                <div class="flex items-center gap-3">
                    <span class="rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200">
                        {{ $cashSummary['monthLabel'] }}
                    </span>
                    <a
                        href="{{ route('dashboard.cash.archive') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-sky-400/40 bg-sky-500/15 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-sky-100 hover:bg-sky-500/25 transition"
                    >
                        Arsip Bukti
                    </a>
                </div>
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

        @if (session('status') === 'cash-payment-submitted')
            <div class="rounded-3xl border border-emerald-400/40 bg-emerald-400/10 px-6 py-4 text-sm text-emerald-100 shadow-xl">
                <p class="font-semibold">Bukti pembayaran berhasil dikirim.</p>
                <p class="mt-1 text-emerald-100/80">Pembayaran akan dihitung setelah admin mengonfirmasi bukti tersebut.</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-3xl border border-rose-400/40 bg-rose-500/10 px-6 py-4 text-sm text-rose-100 shadow-xl">
                <p class="font-semibold">Tidak dapat mengirim bukti pembayaran.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-rose-100/80">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="grid gap-6 {{ $posterUrl ? 'lg:grid-cols-2' : 'lg:grid-cols-1' }}">
            @if ($posterUrl)
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-white">Poster Pembayaran Kas</h2>
                            <p class="text-xs text-slate-300">Unduh poster berikut untuk melihat informasi rekening atau QR pembayaran.</p>
                        </div>
                        <a
                            href="{{ $posterUrl }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-2 rounded-full border border-sky-400/40 bg-sky-500/20 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-sky-100 hover:bg-sky-500/30 transition"
                        >
                            Unduh Poster
                        </a>
                    </div>

                    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-black/40">
                        <img src="{{ $posterUrl }}" alt="Poster pembayaran kas" class="h-full w-full object-contain" loading="lazy">
                    </div>
                </article>
            @else
                <article class="rounded-3xl border border-dashed border-white/20 bg-white/5 p-6 text-sm text-slate-300 shadow-xl">
                    <h2 class="text-lg font-semibold text-white">Poster Pembayaran</h2>
                    <p class="mt-2 leading-relaxed">
                        Poster pembayaran belum diatur. Minta admin membuka menu pengaturan dan mengunggah poster pembayaran kas agar tampil di sini.
                    </p>
                </article>
            @endif

            <article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-white">Kirim Bukti Pembayaran</h2>
                <p class="mt-2 text-xs text-slate-300">
                    Unggah bukti pembayaran kas terbaru. Admin akan meninjau dan mengonfirmasi sebelum saldo bertambah.
                </p>

                <form
                    action="{{ route('dashboard.cash.payments.store') }}"
                    method="post"
                    enctype="multipart/form-data"
                    class="mt-5 grid gap-4"
                >
                    @csrf
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label for="amount" class="text-xs font-semibold uppercase tracking-wide text-slate-300">Nominal (Rp)</label>
                            <input
                                type="number"
                                min="1000"
                                step="1000"
                                name="amount"
                                id="amount"
                                value="{{ $defaultAmount }}"
                                class="block w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-2.5 text-sm text-white placeholder:text-slate-400 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300/60"
                                required
                            >
                        </div>
                        <div class="space-y-1">
                            <label for="date" class="text-xs font-semibold uppercase tracking-wide text-slate-300">Tanggal Pembayaran</label>
                            <input
                                type="date"
                                name="date"
                                id="date"
                                value="{{ $defaultDate }}"
                                max="{{ now()->toDateString() }}"
                                class="block w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-2.5 text-sm text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300/60"
                                required
                            >
                        </div>
                    </div>

                    <div class="space-y-2">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-300">Metode Pembayaran</span>
                        <div class="flex flex-wrap gap-2">
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="transfer"
                                    class="peer sr-only"
                                    {{ $selectedMethod === 'transfer' ? 'checked' : '' }}
                                >
                                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 transition peer-checked:border-sky-400/50 peer-checked:bg-sky-500/20 peer-checked:text-sky-100 hover:bg-white/15">
                                    Transfer
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="cash"
                                    class="peer sr-only"
                                    {{ $selectedMethod === 'cash' ? 'checked' : '' }}
                                >
                                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 transition peer-checked:border-sky-400/50 peer-checked:bg-sky-500/20 peer-checked:text-sky-100 hover:bg-white/15">
                                    Tunai
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="proof" class="text-xs font-semibold uppercase tracking-wide text-slate-300">Unggah Bukti</label>
                        <input
                            type="file"
                            name="proof"
                            id="proof"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="block w-full cursor-pointer rounded-2xl border border-dashed border-white/15 bg-white/5 px-4 py-8 text-sm text-slate-200 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-500 file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-wide file:text-emerald-950 hover:border-emerald-400/50 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300/60"
                            required
                        >
                        <p class="text-xs text-slate-400">Format JPG, PNG, atau PDF dengan ukuran maksimal 5MB.</p>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-400 px-5 py-3 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:ring-offset-2 focus:ring-offset-slate-900/80"
                    >
                        Kirim Bukti Pembayaran
                    </button>
                </form>
            </article>
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
