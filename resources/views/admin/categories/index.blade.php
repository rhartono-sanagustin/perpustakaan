@extends('layouts.admin')

@section('title', 'Kategori')
@section('breadcrumb', 'Kategori')

@section('content')
    <x-page-header title="Kategori" subtitle="Kelola kategori koleksi yang digunakan pada katalog.">
        <x-slot:actions>
            <flux:button variant="primary" icon="plus" href="{{ route('admin.categories.create') }}">
                Tambah Kategori
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <flux:card class="mb-4">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="grid items-end gap-3 md:grid-cols-6">
            <div class="md:col-span-3">
                <flux:field>
                    <flux:label for="q">Cari kategori</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Nama kategori" />
                </flux:field>
            </div>

            <div class="md:col-span-2">
                <flux:field>
                    <flux:label for="status">Status</flux:label>
                    <flux:select id="status" name="status">
                        <option value="">Semua status</option>
                        <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" class="flex-1">Filter</flux:button>
                @if ($search !== '' || $status !== '')
                    <flux:button variant="outline" href="{{ route('admin.categories.index') }}" icon="arrow-path" aria-label="Reset filter" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Daftar Kategori</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $categories->total() }} kategori</flux:badge>
        </div>

        @if ($categories->isEmpty())
            <x-empty-state icon="tags" title="Belum ada kategori" message="Tambahkan kategori untuk mengelompokkan koleksi.">
                <flux:button variant="primary" size="sm" href="{{ route('admin.categories.create') }}">Tambah Kategori</flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Slug</th>
                            <th scope="col" class="text-center">Jumlah Buku</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $category->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $category->description ?: 'Tanpa deskripsi' }}</div>
                                </td>
                                <td><code class="rounded bg-zinc-100 px-1.5 py-0.5 text-xs dark:bg-white/10">{{ $category->slug }}</code></td>
                                <td class="text-center">{{ $category->books_count }}</td>
                                <td>
                                    <x-status-badge
                                        :label="$category->is_active ? 'Aktif' : 'Nonaktif'"
                                        :variant="$category->is_active ? 'success' : 'secondary'"
                                        :icon="$category->is_active ? 'check-circle' : 'pause-circle'"
                                    />
                                </td>
                                <td class="text-end">
                                    <div class="inline-flex gap-1">
                                        <flux:button size="sm" variant="outline" href="{{ route('admin.categories.edit', $category) }}">Edit</flux:button>
                                        <form method="POST" action="{{ route('admin.categories.toggle', $category) }}">
                                            @csrf
                                            <flux:button type="submit" size="sm" variant="outline">
                                                {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </flux:button>
                                        </form>
                                        @if ($category->books_count === 0)
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="Hapus kategori {{ $category->name }}?">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button type="submit" size="sm" variant="danger">Hapus</flux:button>
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
                    Menampilkan {{ $categories->firstItem() }}&ndash;{{ $categories->lastItem() }} dari {{ $categories->total() }} kategori
                </span>
                {{ $categories->links() }}
            </div>
        @endif
    </flux:card>
@endsection
