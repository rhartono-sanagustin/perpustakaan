@extends('layouts.admin')

@section('title', 'Daftar Buku')
@section('breadcrumb', 'Buku')

@section('content')
    <x-page-header title="Daftar Buku" subtitle="Kelola data bibliografi, status katalog, dan eksemplar.">
        <x-slot:actions>
            <flux:button variant="primary" icon="plus" href="{{ route('admin.books.create') }}">
                Tambah Buku
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <flux:card class="mb-4">
        <form method="GET" action="{{ route('admin.books.index') }}" class="grid items-end gap-3 md:grid-cols-2 xl:grid-cols-12">
            <div class="xl:col-span-5">
                <flux:field>
                    <flux:label for="q">Cari</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Judul, ISBN, penulis, atau kata kunci" />
                </flux:field>
            </div>

            <div class="xl:col-span-3">
                <flux:field>
                    <flux:label for="category_id">Kategori</flux:label>
                    <flux:select id="category_id" name="category_id">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label for="status">Status</flux:label>
                    <flux:select id="status" name="status">
                        <option value="">Semua status</option>
                        <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                        <option value="arsip" @selected($status === 'arsip')>Diarsipkan</option>
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex gap-2 xl:col-span-2">
                <flux:button type="submit" variant="primary" class="flex-1">Terapkan</flux:button>
                @if ($search !== '' || $categoryId || $status !== '')
                    <flux:button variant="outline" href="{{ route('admin.books.index') }}" icon="arrow-path" aria-label="Reset filter" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Data Buku</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $books->total() }} judul</flux:badge>
        </div>

        @if ($books->isEmpty())
            <x-empty-state icon="journal-bookmark" title="Belum ada data buku" message="Tambahkan buku pertama atau ubah filter pencarian.">
                <flux:button variant="primary" size="sm" icon="plus" href="{{ route('admin.books.create') }}">Tambah Buku</flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Sampul</th>
                            <th scope="col">Judul / ISBN</th>
                            <th scope="col">Kategori</th>
                            <th scope="col" class="text-center">Total</th>
                            <th scope="col" class="text-center">Tersedia</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                            <tr>
                                <td>
                                    <img src="{{ $book->cover_url }}" alt="" class="h-16 w-12 rounded-md object-cover">
                                </td>
                                <td>
                                    <a href="{{ route('admin.books.edit', $book) }}" class="font-semibold text-zinc-900 dark:text-white">{{ $book->title }}</a>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $book->isbn ?? 'Tanpa ISBN' }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $book->authors_label }}</div>
                                </td>
                                <td>{{ $book->category->name }}</td>
                                <td class="text-center">{{ $book->copies_count }}</td>
                                <td class="text-center">
                                    <flux:badge :color="$book->available_copies_count > 0 ? 'green' : 'zinc'" size="sm">
                                        {{ $book->available_copies_count }}
                                    </flux:badge>
                                </td>
                                <td>
                                    <x-status-badge
                                        :label="$book->is_active ? 'Aktif' : 'Diarsipkan'"
                                        :variant="$book->is_active ? 'success' : 'secondary'"
                                        :icon="$book->is_active ? 'check-circle' : 'archive-box'"
                                    />
                                </td>
                                <td class="text-end">
                                    <div class="inline-flex flex-wrap justify-end gap-1">
                                        @if ($book->is_active)
                                            <flux:button size="sm" variant="outline" href="{{ route('catalog.show', $book) }}" target="_blank" rel="noopener">
                                                Lihat
                                            </flux:button>
                                        @endif
                                        <flux:button size="sm" variant="outline" href="{{ route('admin.books.edit', $book) }}">
                                            Edit
                                        </flux:button>
                                        <flux:button size="sm" variant="outline" href="{{ route('admin.books.copies.index', $book) }}">
                                            Eksemplar
                                        </flux:button>
                                        @if ($book->is_active)
                                            <form method="POST" action="{{ route('admin.books.archive', $book) }}" data-confirm="Arsipkan buku &quot;{{ $book->title }}&quot; dari katalog publik?">
                                                @csrf
                                                <flux:button type="submit" size="sm" variant="danger">Arsipkan</flux:button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.books.activate', $book) }}">
                                                @csrf
                                                <flux:button type="submit" size="sm" variant="primary">Aktifkan</flux:button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    Menampilkan {{ $books->firstItem() }}&ndash;{{ $books->lastItem() }} dari {{ $books->total() }} buku
                </span>
                {{ $books->links() }}
            </div>
        @endif
    </flux:card>
@endsection
