@extends('layouts.public')

@section('title', $book->title)

@section('content')
    <section class="mx-auto w-full max-w-7xl px-4 py-6 lg:px-8 lg:py-10">
        <flux:breadcrumbs class="mb-6">
            <flux:breadcrumbs.item href="{{ route('home') }}">Beranda</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('catalog.index') }}">Katalog</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Detail Buku</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="grid gap-6 lg:grid-cols-12 lg:gap-8">
            <div class="lg:col-span-3">
                <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                    <img src="{{ $book->cover_url }}" alt="Sampul {{ $book->title }}" class="aspect-[3/4] w-full object-cover">
                </div>
            </div>

            <div class="lg:col-span-9">
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    <flux:badge color="sky">{{ $book->category->name }}</flux:badge>
                    @if ($book->hasAvailableCopies())
                        <x-status-badge label="Tersedia {{ $book->available_copies_count }} eksemplar" variant="success" icon="check-circle" />
                    @else
                        <x-status-badge label="Sedang tidak tersedia" variant="secondary" icon="x-circle" />
                    @endif
                </div>

                <flux:heading size="xl" level="1" class="mt-2">{{ $book->title }}</flux:heading>
                <p class="mt-1 text-zinc-500 dark:text-zinc-400">
                    oleh <span class="text-zinc-800 dark:text-zinc-200">{{ $book->authors_label ?: 'Penulis tidak diketahui' }}</span>
                </p>

                <dl class="mt-5 grid gap-x-4 gap-y-2 text-sm sm:grid-cols-4">
                    <dt class="text-zinc-500 dark:text-zinc-400">Penerbit</dt>
                    <dd class="sm:col-span-3">{{ $book->publisher?->name ?? '-' }}</dd>

                    <dt class="text-zinc-500 dark:text-zinc-400">Tahun terbit</dt>
                    <dd class="sm:col-span-3">{{ $book->publication_year ?? '-' }}</dd>

                    <dt class="text-zinc-500 dark:text-zinc-400">Edisi</dt>
                    <dd class="sm:col-span-3">{{ $book->edition ?? '-' }}</dd>

                    <dt class="text-zinc-500 dark:text-zinc-400">ISBN</dt>
                    <dd class="sm:col-span-3">{{ $book->isbn ?? 'Tidak tersedia' }}</dd>

                    <dt class="text-zinc-500 dark:text-zinc-400">Bahasa</dt>
                    <dd class="sm:col-span-3">{{ $book->language }}</dd>
                </dl>

                <flux:card class="mt-5 p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Ketersediaan Eksemplar</flux:heading>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-2 gap-3 text-center sm:grid-cols-4">
                            <div>
                                <div class="text-2xl font-bold">{{ $book->copies_count }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Total eksemplar</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $book->available_copies_count }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Tersedia</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-sky-600 dark:text-sky-400">{{ $book->borrowed_copies_count }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Dipinjam</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $book->damaged_copies_count }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Rusak/hilang</div>
                            </div>
                        </div>

                        <flux:separator class="my-5" />

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">Lokasi rak tersedia:</span>
                            @forelse ($shelfLocations as $shelf)
                                <flux:badge color="zinc" size="sm">{{ $shelf }}</flux:badge>
                            @empty
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">Belum ada eksemplar tersedia.</span>
                            @endforelse
                        </div>
                    </div>
                </flux:card>

                <flux:heading size="lg" level="2" class="mt-6">Ringkasan</flux:heading>
                <p class="mt-2 leading-relaxed">
                    {{ $book->summary ?: 'Ringkasan buku belum tersedia.' }}
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <flux:button variant="primary" icon="pencil-square" href="{{ route('admin.books.edit', $book) }}" class="h-11 px-5">
                                Kelola Buku
                            </flux:button>
                        @elseif ($eligibility && $eligibility['allowed'])
                            <flux:button variant="primary" icon="book-open" href="{{ route('borrower.requests.create', $book) }}" class="h-11 px-5">
                                Ajukan Peminjaman
                            </flux:button>
                            <div class="w-full text-xs text-zinc-500 dark:text-zinc-400">
                                Sisa kuota {{ $eligibility['remaining_quota'] }} eksemplar &middot; durasi pinjam {{ $eligibility['duration_days'] }} hari.
                            </div>
                        @elseif ($eligibility && $eligibility['open_request'])
                            <flux:button variant="outline" icon="clock" href="{{ route('borrower.loans.index', ['tab' => $eligibility['open_request']->isApproved() ? 'disetujui' : 'menunggu']) }}" class="h-11 px-5">
                                Lihat Pengajuan
                            </flux:button>
                            <div class="w-full text-xs text-zinc-500 dark:text-zinc-400">{{ $eligibility['reason'] }}</div>
                        @else
                            <flux:button variant="filled" icon="x-circle" disabled class="h-11 px-5">
                                Belum Dapat Diajukan
                            </flux:button>
                            <div class="w-full text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $eligibility['reason'] ?? 'Pengajuan peminjaman belum tersedia untuk akun ini.' }}
                            </div>
                        @endif
                    @else
                        <flux:button variant="primary" icon="arrow-right-start-on-rectangle" href="{{ route('login') }}" class="h-11 px-5">
                            Masuk untuk Mengajukan
                        </flux:button>
                        <div class="w-full text-xs text-zinc-500 dark:text-zinc-400">
                            Anda dapat melihat katalog tanpa login. Pengajuan pinjaman memerlukan akun guru atau siswa yang aktif.
                        </div>
                    @endauth

                    <flux:button
                        variant="outline"
                        icon="share"
                        class="h-11 px-5"
                        data-share-url="{{ route('catalog.show', $book) }}"
                        data-share-title="{{ $book->title }}"
                        data-share-text="Lihat buku {{ $book->title }} di katalog perpustakaan."
                    >
                        Bagikan
                    </flux:button>
                </div>
            </div>
        </div>

        @if ($relatedBooks->isNotEmpty())
            <div class="mt-10 border-t border-zinc-200 pt-6 dark:border-zinc-800">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <flux:heading size="lg" level="2">Buku Lain di Kategori {{ $book->category->name }}</flux:heading>
                    <a href="{{ route('catalog.index', ['category' => $book->category->slug]) }}" class="text-sm font-medium text-[var(--color-accent-content)] hover:underline">Lihat semua</a>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($relatedBooks as $relatedBook)
                        <x-book-card :book="$relatedBook" />
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
