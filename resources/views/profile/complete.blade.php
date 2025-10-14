<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lengkapi Data Mahasiswa</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
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
    </head>
    <body class="bg-slate-950 text-slate-100 min-h-screen">
        <div class="relative isolate flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="absolute inset-0 -z-10 bg-gradient-to-br from-amber-400/20 via-slate-900 to-indigo-900 opacity-80 blur-3xl"></div>

            <div class="w-full max-w-xl rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.35em] text-amber-300/80">Verifikasi Kelas</p>
                        <h1 class="mt-3 text-3xl font-semibold">Lengkapi Data Anda</h1>
                        <p class="mt-2 text-sm text-slate-300">
                            Mohon isi nomor induk mahasiswa dan detail kelas agar bisa mengakses dashboard kelas.
                        </p>
                    </div>
                    <span class="rounded-full bg-amber-400/20 px-4 py-2 text-xs font-semibold text-amber-200">
                        Data wajib
                    </span>
                </div>

                @if (session('status') === 'profile-required')
                    <div class="mt-6 rounded-2xl border border-amber-500/40 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                        Masukkan data berikut untuk memastikan Anda bagian dari kelas yang benar.
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
                        <p class="font-semibold">Periksa kembali isian Anda:</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.complete.store') }}" method="post" class="mt-8 space-y-6">
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
                                class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/60"
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
                                    class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/60"
                                    placeholder="Contoh: 03TPLP006"
                                >
                            </div>

                            <div class="space-y-2">
                                <label for="no_hp" class="text-sm font-medium text-slate-200">No. HP (opsional)</label>
                                <input
                                    type="tel"
                                    id="no_hp"
                                    name="no_hp"
                                    value="{{ old('no_hp', $user->no_hp) }}"
                                    maxlength="50"
                                    class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/60"
                                    placeholder="Contoh: 08123456789"
                                >
                            </div>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-400 px-4 py-3 text-sm font-semibold text-slate-900 transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-200 focus:ring-offset-2 focus:ring-offset-slate-950"
                    >
                        Simpan &amp; Masuk ke Dashboard
                    </button>
                </form>

                <p class="mt-8 text-center text-xs text-slate-400">
                    Data Anda akan digunakan untuk memastikan akses hanya bagi anggota kelas yang sah.
                </p>
            </div>
        </div>
    </body>
</html>
