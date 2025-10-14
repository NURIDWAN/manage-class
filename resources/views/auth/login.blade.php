<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Masuk Dashboard Kelas</title>
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
            <div class="absolute inset-0 -z-10 bg-gradient-to-br from-amber-400/25 via-slate-900 to-indigo-900 opacity-80 blur-3xl"></div>

            <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">
                <div class="flex flex-col items-center gap-4 text-center">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-amber-400/20 text-lg font-semibold text-amber-200">
                        KK
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-amber-300/80">Dashboard Kelas</p>
                        <h1 class="mt-3 text-3xl font-semibold text-white">Masuk ke Akun Anda</h1>
                        <p class="mt-2 text-sm text-slate-300">
                            Gunakan email mahasiswa yang terdaftar untuk mengakses informasi kelas.
                        </p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
                        <p class="font-semibold">Gagal masuk.</p>
                        <p class="mt-1">Periksa kembali NIM Anda.</p>
                    </div>
                @endif

                <form action="{{ route('login.store') }}" method="post" class="mt-8 space-y-8">
                    @csrf
                    <div class="space-y-2">
                        <label for="nim" class="text-sm font-medium text-slate-200">Nomor Induk Mahasiswa</label>
                        <input
                            type="text"
                            id="nim"
                            name="nim"
                            value="{{ old('nim') }}"
                            required
                            autofocus
                            maxlength="50"
                            class="block w-full rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/60"
                            placeholder="Contoh: 1234567890"
                        >
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-400 px-4 py-3 text-sm font-semibold text-slate-900 transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-200 focus:ring-offset-2 focus:ring-offset-slate-950"
                    >
                        Masuk
                    </button>
                </form>

                <p class="mt-6 text-center text-xs text-slate-400">
                    Butuh akses? Hubungi admin kelas untuk mendapatkan akun.
                </p>
            </div>
        </div>
    </body>
</html>
