<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ trim(($pageTitle ?? 'Dashboard') . ' | ' . config('app.name', 'Aplikasi')) }}</title>
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
        <style>
            .marquee {
                display: inline-block;
                min-width: 100%;
                animation: marquee-scroll 18s linear infinite;
                will-change: transform;
            }

            .marquee.paused {
                animation-play-state: paused;
            }

            @keyframes marquee-scroll {
                0% {
                    transform: translateX(0%);
                }

                100% {
                    transform: translateX(-100%);
                }
            }
        </style>
    </head>
    <body class="bg-slate-950 text-slate-100 min-h-screen">
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-60 bg-gradient-to-br from-amber-400/25 via-slate-900 to-indigo-900 blur-3xl"></div>

            <x-site-navbar
                :title="$navbarTitle ?? $pageTitle ?? 'Dashboard'"
                :cta-label="$ctaLabel ?? null"
                :cta-href="$ctaHref ?? null"
                class="mb-0"
            />

            @isset($announcementBanner)
                <div class="border-b border-sky-400/30 bg-sky-500/15 text-sky-100 overflow-hidden">
                    <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-2 text-xs sm:px-8 md:text-sm">
                        <span class="rounded-full border border-sky-400/60 bg-sky-400/20 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.3em]">
                            Pengumuman
                        </span>
                        <div class="relative flex-1 overflow-hidden">
                            <div class="marquee whitespace-nowrap" data-announcement-marquee tabindex="0">
                                <span class="inline-block px-2 font-semibold text-sky-50">
                                    {{ $announcementBanner['title'] }}
                                </span>
                                @if (! empty($announcementBanner['summary']))
                                    <span class="inline-block px-2 text-sky-200/80">
                                        — {{ $announcementBanner['summary'] }}
                                    </span>
                                @endif
                                <span class="inline-block px-2 text-sky-300/70">
                                    Diperbarui: {{ $announcementBanner['published_at'] }}
                                </span>
                            </div>
                        </div>
                        @if (! empty($announcementBanner['url']))
                            <a
                                href="{{ $announcementBanner['url'] }}"
                                class="hidden rounded-full border border-sky-400/60 bg-sky-400/20 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-sky-100 transition hover:bg-sky-400/30 sm:inline-flex"
                            >
                                Lihat
                            </a>
                        @endif
                    </div>
                </div>
            @endisset

            <main class="px-4 pb-12 pt-6 sm:px-8 lg:px-10">
                @yield('content')
            </main>

            <footer class="mt-auto border-t border-white/10 bg-white/5/40 px-4 py-6 text-xs text-slate-300 sm:px-8">
                @php
                    $githubUrl = \App\Support\Settings::githubUrl();
                @endphp
                <div class="mx-auto flex w-full max-w-6xl flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white">{{ \App\Support\Settings::appName() }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ \App\Support\Settings::footerText() }}</p>
                    </div>
                    @if ($githubUrl)
                        <a href="{{ $githubUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 transition hover:bg-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                <path d="M12 .5C5.648.5.5 5.648.5 12c0 5.089 3.292 9.396 7.865 10.915.575.105.79-.249.79-.557 0-.275-.01-1.002-.015-1.967-3.201.696-3.877-1.543-3.877-1.543-.524-1.332-1.28-1.688-1.28-1.688-1.046-.715.08-.701.08-.701 1.156.081 1.765 1.188 1.765 1.188 1.028 1.763 2.698 1.254 3.355.958.104-.745.403-1.254.732-1.543-2.555-.291-5.238-1.278-5.238-5.686 0-1.255.45-2.281 1.187-3.084-.119-.29-.515-1.468.112-3.06 0 0 .967-.31 3.168 1.178a11.08 11.08 0 0 1 2.884-.388c.978.004 1.963.132 2.884.388 2.2-1.488 3.166-1.178 3.166-1.178.629 1.592.233 2.77.114 3.06.74.803 1.186 1.829 1.186 3.084 0 4.42-2.688 5.392-5.253 5.677.414.357.783 1.063.783 2.143 0 1.548-.014 2.796-.014 3.178 0 .312.21.67.798.556C20.71 21.39 24 17.082 24 12c0-6.352-5.148-11.5-11.5-11.5Z" />
                            </svg>
                            GitHub
                        </a>
                    @endif
                </div>
            </footer>
        </div>
        @stack('scripts')
    </body>
</html>
