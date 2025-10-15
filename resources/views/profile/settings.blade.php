@extends('layouts.dashboard')

@section('content')
    <section class="mx-auto flex w-full max-w-4xl flex-col gap-8">
        <header class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur sm:p-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-amber-300/80">Profil</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white sm:text-4xl">Pengaturan Data Mahasiswa</h1>
                    <p class="mt-3 text-sm text-slate-300 sm:text-base">
                        Perbarui nomor induk mahasiswa, kelas, dan nomor HP agar data dashboard tetap akurat.
                    </p>
                </div>
                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 transition hover:bg-white/20"
                >
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </header>

        @if (session('status') === 'profile-updated')
            <div class="rounded-3xl border border-emerald-400/40 bg-emerald-400/10 px-6 py-4 text-sm text-emerald-100 shadow-xl">
                <p class="font-semibold">Profil berhasil diperbarui.</p>
                <p class="mt-1 text-emerald-100/80">Perubahan data akan digunakan pada laporan dan halaman kas.</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-3xl border border-rose-400/40 bg-rose-500/10 px-6 py-4 text-sm text-rose-100 shadow-xl">
                <p class="font-semibold">Tidak dapat menyimpan data.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dashboard.profile.update') }}" method="post" class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl space-y-6">
            @csrf
            <div class="grid gap-6">
                <div class="space-y-2">
                    <label for="nim" class="text-sm font-medium text-slate-200">Nomor Induk Mahasiswa</label>
                    <input
                        type="text"
                        id="nim"
                        name="nim"
                        value="{{ old('nim', $user->nim) }}"
                        required
                        maxlength="50"
                        class="block w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/60"
                        placeholder="Contoh: 241011400256"
                    >
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="kelas" class="text-sm font-medium text-slate-200">Kelas</label>
                        <input
                            type="text"
                            id="kelas"
                            name="kelas"
                            value="{{ old('kelas', $user->kelas) }}"
                            required
                            maxlength="50"
                            class="block w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/60"
                            placeholder="Contoh: 03TPLP006"
                        >
                    </div>

                    <div class="space-y-2">
                        <label for="no_hp" class="text-sm font-medium text-slate-200">No. HP</label>
                        <input
                            type="tel"
                            id="no_hp"
                            name="no_hp"
                            value="{{ old('no_hp', $user->no_hp) }}"
                            maxlength="50"
                            class="block w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/60"
                            placeholder="Contoh: 081234567890"
                        >
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-400 px-5 py-3 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:ring-offset-2 focus:ring-offset-slate-900/80"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>
@endsection
