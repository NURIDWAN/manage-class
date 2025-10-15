@extends('layouts.dashboard')

@section('content')
    @php
        $items = $documents;
    @endphp

    <section class="mx-auto flex w-full max-w-6xl flex-col gap-8">
        <header class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-emerald-300/80">Dokumen</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white sm:text-4xl">Arsip Dokumen Kelas</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300 sm:text-base">
                        Unduh dokumen penting kelas, seperti notulen rapat, modul belajar, dan file pendukung lainnya.
                    </p>
                </div>
                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-200 hover:bg-white/20 transition"
                >
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </header>

        <article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Daftar Dokumen</h2>
                    <p class="text-xs text-slate-300">Dokumen diurutkan dari yang terbaru dan bisa diunduh kapan saja.</p>
                </div>
                <span class="text-xs uppercase tracking-wide text-slate-300">Total dokumen: {{ $items->count() }}</span>
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
                <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-200">
                    <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Judul</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Diunggah</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($items as $document)
                            <tr class="bg-white/5">
                                <td class="px-4 py-3 text-sm font-semibold text-white">
                                    {{ $document->title }}
                                </td>
                                <td class="px-4 py-3 text-xs uppercase tracking-wide text-slate-300">
                                    {{ $document->category ?? 'Umum' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-300 leading-relaxed">
                                    {{ $document->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-400">
                                    {{ $document->created_at?->translatedFormat('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($document->file_path)
                                        <a
                                            href="{{ route('dashboard.documents.download', $document) }}"
                                            class="inline-flex items-center gap-2 rounded-full border border-sky-400/40 bg-sky-500/15 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-sky-100 hover:bg-sky-500/25 transition"
                                        >
                                            Unduh
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">File tidak tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-400">
                                    Belum ada dokumen yang diunggah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
