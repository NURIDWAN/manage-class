@props([
    'title',
    'value',
    'icon' => 'megaphone',
])

@php
    $icons = [
        'megaphone' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.25 9.75v-4.5m0 4.5 5.712-2.856a.818.818 0 0 0 0-1.464l-5.712-2.856a1.5 1.5 0 0 0-2.088.672L6.48 12H4.125a1.125 1.125 0 0 0-1.08 1.434l1.905 6.35A1.125 1.125 0 0 0 6 20.25h1.372m6.878-10.5v9.375a1.125 1.125 0 0 1-1.839.846L8.25 16.098m0 0-.664 1.855A1.125 1.125 0 0 0 8.625 19.5H9.75"/></svg>',
        'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25M3 18.75A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75M3 18.75v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5M8.25 12.75h.008v.008H8.25v-.008Zm0 3h.008v.008H8.25v-.008Zm3 0h.008v.008H11.25v-.008Zm3 0h.008v.008H14.25v-.008Zm0-3h.008v.008H14.25v-.008Zm-3-3h.008v.008H11.25v-.008Z"/></svg>',
        'banknotes' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h15a2.25 2.25 0 0 1 2.25 2.25v7.5a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 0 18V10.5a2.25 2.25 0 0 1 2.25-2.25Zm0 0V6A2.25 2.25 0 0 1 4.5 3.75h15A2.25 2.25 0 0 1 21.75 6v2.25M6 18h.008v.008H6V18Zm0-9h.008v.008H6V9Zm6 0h.008v.008H12V9Zm0 9h.008v.008H12V18Zm6 0h.008v.008H18V18Zm0-9h.008v.008H18V9ZM9 15.75A2.25 2.25 0 1 0 9 11.25a2.25 2.25 0 0 0 0 4.5Z"/></svg>',
        'shield' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M12 3 3.75 6v6c0 4.213 2.658 8.215 8.25 9 5.592-.785 8.25-4.787 8.25-9V6L12 3Z"/></svg>',
    ];

    $iconSvg = $icons[$icon] ?? $icons['megaphone'];
@endphp

<div {{ $attributes->class('rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl flex flex-col gap-3') }}>
    <div class="flex items-center justify-between">
        <div class="rounded-full bg-white/10 p-3 text-amber-300">
            {!! $iconSvg !!}
        </div>
        <span class="text-xs uppercase tracking-wide text-slate-300">Update realtime</span>
    </div>
    <div class="flex flex-col gap-1">
        <p class="text-sm text-slate-300">{{ $title }}</p>
        <p class="text-2xl font-semibold text-white">{{ $value }}</p>
    </div>
</div>
