@extends('layouts.admin')

@section('title', 'Eksemplar Buku')
@section('breadcrumb', 'Buku / Eksemplar')

@section('content')
    <x-page-header title="Eksemplar Buku" :subtitle="$book->title">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.books.edit', $book) }}">
                Kembali ke Buku
            </flux:button>
            <flux:button variant="primary" icon="plus" href="{{ route('admin.books.copies.create', $book) }}">
                Tambah Eksemplar
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-2 gap-2 md:grid-cols-5">
        @foreach ($statuses as $copyStatus)
            <flux:card class="py-3!">
                <div class="text-2xl font-bold">{{ $summary[$copyStatus->value] ?? 0 }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $copyStatus->label() }}</div>
            </flux:card>
        @endforeach
    </div>

    <flux:card class="mb-4">
        <form method="GET" action="{{ route('admin.books.copies.index', $book) }}" class="grid items-end gap-3 md:grid-cols-6">
            <div class="md:col-span-3">
                <flux:field>
                    <flux:label for="q">Cari eksemplar</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Kode inventaris atau lokasi rak" />
                </flux:field>
            </div>

            <div class="md:col-span-2">
                <flux:field>
                    <flux:label for="status">Status</flux:label>
                    <flux:select id="status" name="status">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $copyStatus)
                            <option value="{{ $copyStatus->value }}" @selected($status === $copyStatus->value)>{{ $copyStatus->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" class="flex-1">Filter</flux:button>
                @if ($search !== '' || $status !== '')
                    <flux:button variant="outline" href="{{ route('admin.books.copies.index', $book) }}" icon="arrow-path" aria-label="Reset filter" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Daftar Eksemplar</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $copies->total() }} eksemplar</flux:badge>
        </div>

        @if ($copies->isEmpty())
            <x-empty-state icon="collection" title="Belum ada eksemplar" message="Tambahkan eksemplar fisik agar buku dapat dipinjam.">
                <flux:button variant="primary" size="sm" icon="plus" href="{{ route('admin.books.copies.create', $book) }}">
                    Tambah Eksemplar
                </flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Kode Inventaris</th>
                            <th scope="col">Lokasi Rak</th>
                            <th scope="col">Kondisi</th>
                            <th scope="col">Status</th>
                            <th scope="col">Pinjaman Aktif</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($copies as $copy)
                            <tr>
                                <td class="font-semibold">{{ $copy->inventory_code }}</td>
                                <td>{{ $copy->shelf_location ?? '-' }}</td>
                                <td>
                                    <x-status-badge :label="$copy->condition->label()" :variant="$copy->condition->badgeVariant()" />
                                </td>
                                <td>
                                    <x-status-badge :label="$copy->status->label()" :variant="$copy->status->badgeVariant()" />
                                </td>
                                <td>
                                    @if ($copy->activeLoan)
                                        <div class="text-sm font-semibold">{{ $copy->activeLoan->user->name }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Jatuh tempo {{ $copy->activeLoan->due_at->locale('id')->translatedFormat('d M Y') }}</div>
                                    @else
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="inline-flex gap-1">
                                        <flux:button size="sm" variant="outline" href="{{ route('admin.books.copies.edit', [$book, $copy]) }}">Edit</flux:button>
                                        <form method="POST" action="{{ route('admin.books.copies.destroy', [$book, $copy]) }}" data-confirm="Hapus atau nonaktifkan eksemplar {{ $copy->inventory_code }}?">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" variant="danger">Hapus</flux:button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    Menampilkan {{ $copies->firstItem() }}&ndash;{{ $copies->lastItem() }} dari {{ $copies->total() }} eksemplar
                </span>
                {{ $copies->links() }}
            </div>
        @endif
    </flux:card>
@endsection
