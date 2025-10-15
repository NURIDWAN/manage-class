@props([
    'title' => 'Dashboard',
    'ctaLabel' => null,
    'ctaHref' => null,
])

@php
    $appName = \App\Support\Settings::appName();
    $initials = collect(preg_split('/\s+/', $appName, -1, PREG_SPLIT_NO_EMPTY))
        ->map(fn (string $part) => mb_substr($part, 0, 1))
        ->join('');
    $initials = mb_strtoupper(mb_substr($initials, 0, 2));

    $links = collect([
        ['label' => 'Ringkasan', 'route' => 'dashboard'],
        ['label' => 'Uang Kas', 'route' => 'dashboard.cash', 'auth' => true],
        ['label' => 'Dokumen', 'route' => 'dashboard.documents', 'auth' => true],
        ['label' => 'Profil', 'route' => 'dashboard.profile', 'auth' => true],
        ['label' => 'Laporan', 'route' => 'dashboard.reports', 'auth' => true],
        ['label' => 'Jadwal Kuliah', 'route' => 'schedule'],
    ])->filter(function (array $link): bool {
        if (! empty($link['auth']) && ! auth()->check()) {
            return false;
        }

        return true;
    })->values();

    $menuId = 'site-nav-' . \Illuminate\Support\Str::random(8);
@endphp

@php
    $baseBackground = $attributes->get('backgroundClass', 'bg-slate-900/70');
@endphp

<nav {{ $attributes->class(["sticky top-0 z-20 w-full border-b border-white/10 backdrop-blur", $baseBackground]) }}>
    <div class="mx-auto flex w-full flex-col gap-4 px-4 py-4 sm:px-8 lg:max-w-6xl">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-400/20 text-amber-300 font-semibold">
                    {{ $initials }}
                </span>
                <div class="leading-tight">
                    <p class="text-xs uppercase tracking-[0.3em] text-amber-300/80">{{ $appName }}</p>
                    <p class="text-sm font-medium text-white">{{ $title }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 md:hidden">
                @if ($ctaLabel && $ctaHref)
                    <a href="{{ $ctaHref }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition">
                        {{ $ctaLabel }}
                    </a>
                @endif
                <button
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/10 text-slate-200 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-amber-300/70"
                    data-nav-toggle="{{ $menuId }}"
                    aria-controls="{{ $menuId }}"
                    aria-expanded="false"
                >
                    <span class="sr-only">Toggle navigation</span>
                    <svg class="h-5 w-5" data-nav-icon="open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="hidden h-5 w-5" data-nav-icon="close" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div
            id="{{ $menuId }}"
            data-nav-menu
            class="hidden flex-col gap-4 border-t border-white/10 pt-4 text-sm font-medium text-slate-200 md:flex md:flex-row md:items-center md:justify-between md:border-0 md:pt-0"
        >
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:gap-3">
                @foreach ($links as $link)
                    @php
                        $href = route($link['route']);
                        $isActive = request()->routeIs($link['route']);
                    @endphp
                    <a
                        href="{{ $href }}"
                        @class([
                            'rounded-full px-3 py-2 transition hover:text-white hover:bg-white/10',
                            'bg-white/15 text-white font-semibold' => $isActive,
                        ])
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-col gap-2 md:flex-row md:items-center md:gap-3">
                @if ($ctaLabel && $ctaHref)
                    <a href="{{ $ctaHref }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition">
                        {{ $ctaLabel }}
                    </a>
                @endif
                @auth
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-nav-toggle]').forEach((button) => {
                    const targetId = button.getAttribute('data-nav-toggle');
                    const menu = document.getElementById(targetId);

                    if (!menu) {
                        return;
                    }

                    button.addEventListener('click', () => {
                        const expanded = button.getAttribute('aria-expanded') === 'true';
                        button.setAttribute('aria-expanded', String(!expanded));
                        menu.classList.toggle('hidden', expanded);

                        const openIcon = button.querySelector('[data-nav-icon="open"]');
                        const closeIcon = button.querySelector('[data-nav-icon="close"]');

                        if (openIcon && closeIcon) {
                            openIcon.classList.toggle('hidden', !expanded);
                            closeIcon.classList.toggle('hidden', expanded);
                        }
                    });
                });

                document.querySelectorAll('[data-announcement-marquee]').forEach((marquee) => {
                    const pause = () => marquee.classList.add('paused');
                    const resume = () => marquee.classList.remove('paused');

                    marquee.addEventListener('mouseenter', pause);
                    marquee.addEventListener('mouseleave', resume);
                    marquee.addEventListener('touchstart', pause, { passive: true });
                    marquee.addEventListener('touchend', resume);
                    marquee.addEventListener('touchcancel', resume);
                    marquee.addEventListener('focusin', pause);
                    marquee.addEventListener('focusout', resume);
                });
            });
        </script>
    @endpush
@endonce
