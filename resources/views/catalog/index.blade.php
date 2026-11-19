@extends('layouts.public')

@section('title', 'Katalog Buku')

@section('content')
    <section class="mx-auto w-full max-w-7xl px-4 py-6 lg:px-8 lg:py-10">
        <x-page-header title="Katalog Buku" subtitle="Cari koleksi perpustakaan dan periksa ketersediaan eksemplar.">
            <x-slot:actions>
                <flux:badge color="zinc" size="lg">{{ $books->total() }} judul ditemukan</flux:badge>
            </x-slot:actions>
        </x-page-header>

        <flux:card class="mb-6">
            <form method="GET" action="{{ route('catalog.index') }}" role="search" class="grid items-end gap-3 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <flux:field>
                        <flux:label for="q">Cari buku</flux:label>
                        <flux:input
                            type="search"
                            id="q"
                            name="q"
                            value="{{ $search }}"
                            icon="magnifying-glass"
                            placeholder="Cari judul, penulis, ISBN, atau kata kunci"
                            maxlength="100"
                        />
                    </flux:field>
                </div>

                <div class="lg:col-span-3" data-auto-submit>
                    <flux:field>
                        <flux:label for="category">Kategori</flux:label>
                        <flux:select id="category" name="category">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" @selected($categorySlug === $category->slug)>
                                    {{ $category->name }} ({{ $category->books_count }})
                                </option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <div class="lg:col-span-2" data-auto-submit>
                    <flux:field>
                        <flux:label for="availability">Ketersediaan</flux:label>
                        <flux:select id="availability" name="availability">
                            <option value="">Semua</option>
                            <option value="tersedia" @selected($availability === 'tersedia')>Tersedia</option>
                            <option value="habis" @selected($availability === 'habis')>Tidak tersedia</option>
                        </flux:select>
                    </flux:field>
                </div>

                <div class="flex gap-2 lg:col-span-2">
                    <flux:button type="submit" variant="primary" icon="funnel" class="flex-1">
                        Terapkan
                    </flux:button>
                    @if ($search !== '' || $categorySlug !== '' || $availability !== '')
                        <flux:button variant="outline" href="{{ route('catalog.index') }}" icon="arrow-path" aria-label="Reset filter" />
                    @endif
                </div>
            </form>

            @if ($search !== '' || $selectedCategory || $availability !== '')
                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-zinc-100 pt-4 dark:border-white/10">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Filter aktif:</span>
                    @if ($search !== '')
                        <flux:badge color="zinc" size="sm">Kata kunci: {{ $search }}</flux:badge>
                    @endif
                    @if ($selectedCategory)
                        <flux:badge color="zinc" size="sm">Kategori: {{ $selectedCategory->name }}</flux:badge>
                    @endif
                    @if ($availability !== '')
                        <flux:badge color="zinc" size="sm">{{ $availability === 'tersedia' ? 'Tersedia' : 'Tidak tersedia' }}</flux:badge>
                    @endif
                    <a href="{{ route('catalog.index') }}" class="ms-auto text-xs font-medium text-[var(--color-accent-content)] hover:underline">Reset filter</a>
                </div>
            @endif
        </flux:card>

        @if ($books->isEmpty())
            <flux:card>
                <x-empty-state
                    icon="magnifying-glass"
                    title="Buku tidak ditemukan"
                    message="Coba ubah kata kunci atau reset filter untuk melihat seluruh koleksi."
                >
                    <flux:button variant="outline" size="sm" icon="arrow-path" href="{{ route('catalog.index') }}">
                        Reset Filter
                    </flux:button>
                </x-empty-state>
            </flux:card>
        @else
            <div class="mb-4 flex flex-col justify-between gap-2 sm:flex-row">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    Menampilkan {{ $books->firstItem() }}&ndash;{{ $books->lastItem() }} dari {{ $books->total() }} judul
                </span>
                <span class="flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400">
                    <flux:icon.information-circle variant="mini" class="size-4" />
                    Ketersediaan dihitung dari eksemplar berstatus Tersedia.
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($books as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @endif
    </section>
@endsection
