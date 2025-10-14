<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Jadwal Kuliah | Kelas Kompak</title>
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
                        colors: {
                            midnight: {
                                900: '#0f172a',
                                800: '#1e293b',
                                700: '#334155',
                            },
                        },
                    },
                },
            };
        </script>
    </head>
    <body class="bg-midnight-900 text-slate-100 min-h-screen">
        <div class="relative isolate overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-40 bg-gradient-to-br from-sky-500/30 via-midnight-900 to-indigo-900 blur-3xl"></div>

            <x-site-navbar title="Jadwal Kuliah" background-class="bg-midnight-900/70" :cta-label="'&larr; Dashboard'" :cta-href="route('dashboard')" />

            <header class="px-6 sm:px-10 py-10">
                <div class="max-w-6xl mx-auto flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-sky-300/80">Kelas Kompak</p>
                        <h1 class="mt-2 text-3xl sm:text-4xl font-semibold text-white">Jadwal Kuliah Minggu Ini</h1>
                        <p class="mt-2 text-slate-300 max-w-2xl">
                            Rencanakan kegiatan belajar dengan melihat jadwal lengkap setiap hari. Jam mulai, jam selesai, dan lokasi ruangan sudah dirangkum untukmu.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-5 py-2.5 text-sm font-medium text-slate-200 shadow-lg hover:bg-white/20 transition">
                            <span>&larr;</span> <span>Kembali ke Dashboard</span>
                        </a>
                        <a href="#jadwal" class="inline-flex items-center gap-2 rounded-full border border-sky-500/40 bg-sky-500/20 px-5 py-2.5 text-sm font-medium text-sky-200 hover:bg-sky-500/30 transition">
                            Lihat Jadwal
                        </a>
                    </div>
                </div>
            </header>

            <main id="jadwal" class="px-6 sm:px-10 pb-16">
                <section class="max-w-6xl mx-auto">
                    @if ($schedules->isEmpty())
                        <div class="rounded-3xl border border-white/10 bg-white/5 px-8 py-12 text-center shadow-xl">
                            <p class="text-xl font-semibold text-slate-200">Belum ada jadwal yang terdaftar.</p>
                            <p class="mt-2 text-sm text-slate-300">Pengurus akan segera menambahkan jadwal. Silakan cek kembali nanti.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach ($schedules as $schedule)
                                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl backdrop-blur">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-xl font-semibold text-white">{{ $schedule['label'] }}</h2>
                                        <span class="text-xs uppercase tracking-wide text-slate-300">Hari aktif</span>
                                    </div>

                                    <ul class="mt-5 space-y-4">
                                        @forelse ($schedule['items'] as $item)
                                            <li class="rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/10 transition">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <p class="text-sm uppercase tracking-wide text-sky-300">{{ $item['time'] }}</p>
                                                        <p class="mt-1 text-lg font-medium text-white">{{ $item['course'] }}</p>
                                                    </div>
                                                    <span class="rounded-full border border-sky-500/40 bg-sky-500/15 px-3 py-1 text-xs font-medium text-sky-200">
                                                        {{ $item['room'] ?? 'TBA' }}
                                                    </span>
                                                </div>
                                            </li>
                                        @empty
                                            <li class="text-sm text-slate-300">Tidak ada jadwal untuk hari ini.</li>
                                        @endforelse
                                    </ul>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            </main>
        </div>

        <footer class="py-10">
            <p class="text-center text-xs text-slate-400">
                &copy; {{ now()->year }} Kelas Kompak. Jadwal kuliah diperbarui oleh pengurus.
            </p>
        </footer>
    </body>
</html>
