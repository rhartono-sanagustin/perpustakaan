@extends('layouts.admin')

@section('title', 'Penerbit')
@section('breadcrumb', 'Penerbit')

@section('content')
    <x-page-header title="Penerbit" subtitle="Kelola data penerbit koleksi perpustakaan.">
        <x-slot:actions>
            <flux:button variant="primary" icon="plus" href="{{ route('admin.publishers.create') }}">
                Tambah Penerbit
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <flux:card class="mb-4">
        <form method="GET" action="{{ route('admin.publishers.index') }}" class="grid items-end gap-3 md:grid-cols-4">
            <div class="md:col-span-3">
                <flux:field>
                    <flux:label for="q">Cari penerbit</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Nama penerbit" />
                </flux:field>
            </div>
            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" class="flex-1">Cari</flux:button>
                @if ($search !== '')
                    <flux:button variant="outline" href="{{ route('admin.publishers.index') }}" icon="arrow-path" aria-label="Reset pencarian" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Daftar Penerbit</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $publishers->total() }} penerbit</flux:badge>
        </div>

        @if ($publishers->isEmpty())
            <x-empty-state icon="building" title="Belum ada penerbit" message="Tambahkan penerbit agar dapat dihubungkan dengan buku.">
                <flux:button variant="primary" size="sm" href="{{ route('admin.publishers.create') }}">Tambah Penerbit</flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Kontak</th>
                            <th scope="col" class="text-center">Jumlah Buku</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($publishers as $publisher)
                            <tr>
                                <td class="font-semibold">{{ $publisher->name }}</td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">
                                    <div>{{ $publisher->address ?: '-' }}</div>
                                    <div>{{ $publisher->phone ?: '' }}{{ $publisher->phone && $publisher->email ? ' · ' : '' }}{{ $publisher->email ?: '' }}</div>
                                </td>
                                <td class="text-center">{{ $publisher->books_count }}</td>
                                <td class="text-end">
                                    <div class="inline-flex gap-1">
                                        <flux:button size="sm" variant="outline" href="{{ route('admin.publishers.edit', $publisher) }}">Edit</flux:button>
                                        @if ($publisher->books_count === 0)
                                            <form method="POST" action="{{ route('admin.publishers.destroy', $publisher) }}" data-confirm="Hapus penerbit {{ $publisher->name }}?">
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
                    Menampilkan {{ $publishers->firstItem() }}&ndash;{{ $publishers->lastItem() }} dari {{ $publishers->total() }} penerbit
                </span>
                {{ $publishers->links() }}
            </div>
        @endif
    </flux:card>
@endsection
