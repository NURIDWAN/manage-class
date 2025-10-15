<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Jadwal Kuliah | {{ \App\Support\Settings::appName() }}</title>
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

            <x-site-navbar
                title="Jadwal Kuliah"
                background-class="bg-midnight-900/70"
                :cta-label="__('&larr; Dashboard')"
                :cta-href="route('dashboard')"
            />

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

            <main id="jadwal" class="px-6 pb-16 sm:px-10">
                <section class="mx-auto flex w-full max-w-6xl flex-col gap-8">
                    @if ($dayOptions->sum('count') === 0)
                        <div class="rounded-3xl border border-white/10 bg-white/5 px-8 py-12 text-center shadow-xl">
                            <p class="text-xl font-semibold text-slate-200">Belum ada jadwal yang terdaftar.</p>
                            <p class="mt-2 text-sm text-slate-300">Pengurus akan segera menambahkan jadwal. Silakan cek kembali nanti.</p>
                        </div>
                    @else
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="text-2xl font-semibold text-white">Jadwal {{ $selectedLabel }}</h2>
                                    <p class="text-sm text-slate-300">Pilih hari lain bila ingin melihat jadwal berbeda.</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($dayOptions as $option)
                                        <a
                                            href="{{ route('schedule', ['day' => $option['key']]) }}"
                                            @class([
                                                'rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-wide transition',
                                                'bg-sky-500/25 text-white border border-sky-400/50' => $option['key'] === $selectedDay,
                                                'bg-white/5 text-slate-200 border border-white/10 hover:bg-white/10' => $option['key'] !== $selectedDay,
                                            ])
                                        >
                                            {{ $option['label'] }}
                                            <span class="ml-1 text-[10px] text-slate-300">({{ $option['count'] }})</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-6 overflow-hidden rounded-2xl border border-white/10">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-200">
                                        <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                                            <tr>
                                                <th class="px-4 py-3">Mata Kuliah</th>
                                                <th class="px-4 py-3">Waktu</th>
                                                <th class="px-4 py-3">Ruang</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/5">
                                            @forelse ($selectedSchedules as $item)
                                                <tr class="bg-white/5">
                                                    <td class="px-4 py-3 text-sm font-semibold text-white">
                                                        {{ $item['course'] }}
                                                    </td>
                                                    <td class="px-4 py-3 text-xs font-medium text-sky-200">
                                                        {{ $item['time'] }}
                                                    </td>
                                                    <td class="px-4 py-3 text-xs text-slate-200">
                                                        {{ $item['room'] ?? 'TBA' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-400">
                                                        Belum ada jadwal untuk hari {{ strtolower($selectedLabel) }}.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-2">
                            @foreach ($weeklySchedules as $dayKey => $schedule)
                                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl backdrop-blur">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-white">{{ $schedule['label'] }}</h3>
                                        <span class="text-xs uppercase tracking-wide text-slate-300">{{ count($schedule['items']) }} mata kuliah</span>
                                    </div>

                                    <ul class="mt-4 space-y-3">
                                        @forelse ($schedule['items'] as $item)
                                            <li @class([
                                                'rounded-2xl border border-white/10 bg-white/10 p-4 transition',
                                                'ring-1 ring-sky-400/40' => $dayKey === $selectedDay,
                                            ])>
                                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                    <div>
                                                        <p class="text-sm font-semibold text-white">{{ $item['course'] }}</p>
                                                        <p class="text-xs text-slate-300">{{ $item['room'] ?? 'TBA' }}</p>
                                                    </div>
                                                    <span class="inline-flex items-center rounded-full border border-sky-500/40 bg-sky-500/15 px-3 py-1 text-xs font-medium text-sky-200">
                                                        {{ $item['time'] }}
                                                    </span>
                                                </div>
                                            </li>
                                        @empty
                                            <li class="text-xs text-slate-300">Tidak ada jadwal.</li>
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
                &copy; {{ now()->year }} {{ $appName }}. Jadwal kuliah diperbarui oleh pengurus.
            </p>
        </footer>

        @stack('scripts')
    </body>
</html>
