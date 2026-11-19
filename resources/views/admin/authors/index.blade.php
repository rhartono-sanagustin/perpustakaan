@extends('layouts.admin')

@section('title', 'Penulis')
@section('breadcrumb', 'Penulis')

@section('content')
    <x-page-header title="Penulis" subtitle="Kelola data penulis yang terhubung dengan koleksi.">
        <x-slot:actions>
            <flux:button variant="primary" icon="plus" href="{{ route('admin.authors.create') }}">
                Tambah Penulis
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <flux:card class="mb-4">
        <form method="GET" action="{{ route('admin.authors.index') }}" class="grid items-end gap-3 md:grid-cols-4">
            <div class="md:col-span-3">
                <flux:field>
                    <flux:label for="q">Cari penulis</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Nama penulis" />
                </flux:field>
            </div>
            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" class="flex-1">Cari</flux:button>
                @if ($search !== '')
                    <flux:button variant="outline" href="{{ route('admin.authors.index') }}" icon="arrow-path" aria-label="Reset pencarian" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Daftar Penulis</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $authors->total() }} penulis</flux:badge>
        </div>

        @if ($authors->isEmpty())
            <x-empty-state icon="pen" title="Belum ada penulis" message="Tambahkan penulis agar dapat dihubungkan dengan buku.">
                <flux:button variant="primary" size="sm" href="{{ route('admin.authors.create') }}">Tambah Penulis</flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Biografi</th>
                            <th scope="col" class="text-center">Jumlah Buku</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($authors as $author)
                            <tr>
                                <td class="font-semibold">{{ $author->name }}</td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ \Illuminate\Support\Str::limit($author->bio ?: '-', 90) }}</td>
                                <td class="text-center">{{ $author->books_count }}</td>
                                <td class="text-end">
                                    <div class="inline-flex gap-1">
                                        <flux:button size="sm" variant="outline" href="{{ route('admin.authors.edit', $author) }}">Edit</flux:button>
                                        @if ($author->books_count === 0)
                                            <form method="POST" action="{{ route('admin.authors.destroy', $author) }}" data-confirm="Hapus penulis {{ $author->name }}?">
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
                    Menampilkan {{ $authors->firstItem() }}&ndash;{{ $authors->lastItem() }} dari {{ $authors->total() }} penulis
                </span>
                {{ $authors->links() }}
            </div>
        @endif
    </flux:card>
@endsection
