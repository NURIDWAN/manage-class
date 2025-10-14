@props([
    'title' => 'Dashboard',
    'ctaLabel' => null,
    'ctaHref' => null,
])

@php
    $links = [
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Jadwal Kuliah', 'href' => route('schedule')],
    ];
@endphp

@php
    $baseBackground = $attributes->get('backgroundClass', 'bg-slate-900/70');
@endphp

<nav {{ $attributes->class(["sticky top-0 z-20 w-full border-b border-white/10 backdrop-blur", $baseBackground]) }}>
    <div class="max-w-6xl mx-auto flex items-center justify-between px-6 sm:px-10 py-4">
        <div class="flex items-center gap-2">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-400/20 text-amber-300 font-semibold">
                KK
            </span>
            <div class="leading-tight">
                <p class="text-xs uppercase tracking-[0.3em] text-amber-300/80">Kelas Kompak</p>
                <p class="text-sm font-medium text-white">{{ $title }}</p>
            </div>
        </div>
        <div class="hidden sm:flex items-center gap-6 text-sm font-medium text-slate-200">
            @foreach ($links as $link)
                <a
                    href="{{ $link['href'] }}"
                    @class([
                        'hover:text-white transition',
                        'text-white font-semibold' => url()->current() === $link['href'],
                    ])
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
        <div class="flex items-center gap-3">
            @if ($ctaLabel && $ctaHref)
                <a href="{{ $ctaHref }}" class="hidden sm:inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition">
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
</nav>
