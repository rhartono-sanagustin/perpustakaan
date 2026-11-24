@extends('layouts.admin')

@section('title', 'Eksemplar / Inventaris')
@section('breadcrumb', 'Eksemplar')

@section('content')
    <x-page-header title="Eksemplar / Inventaris" subtitle="Pantau seluruh eksemplar fisik dan statusnya.">
        <x-slot:actions>
            <flux:button variant="outline" icon="book-open" href="{{ route('admin.books.index') }}">
                Pilih Buku
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
        <form method="GET" action="{{ route('admin.copies.index') }}" class="grid items-end gap-3 md:grid-cols-6">
            <div class="md:col-span-3">
                <flux:field>
                    <flux:label for="q">Cari eksemplar</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Kode inventaris, judul buku, atau rak" />
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
                    <flux:button variant="outline" href="{{ route('admin.copies.index') }}" icon="arrow-path" aria-label="Reset filter" />
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
            <x-empty-state icon="collection" title="Belum ada eksemplar" message="Tambahkan eksemplar melalui halaman daftar buku.">
                <flux:button variant="primary" size="sm" href="{{ route('admin.books.index') }}">Buka Daftar Buku</flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Kode Inventaris</th>
                            <th scope="col">Buku</th>
                            <th scope="col">Rak</th>
                            <th scope="col">Kondisi</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($copies as $copy)
                            <tr>
                                <td class="font-semibold">{{ $copy->inventory_code }}</td>
                                <td>
                                    <div class="font-semibold">{{ $copy->book->title }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $copy->book->category?->name }}</div>
                                </td>
                                <td>{{ $copy->shelf_location ?? '-' }}</td>
                                <td>
                                    <x-status-badge :label="$copy->condition->label()" :variant="$copy->condition->badgeVariant()" />
                                </td>
                                <td>
                                    <x-status-badge :label="$copy->status->label()" :variant="$copy->status->badgeVariant()" />
                                </td>
                                <td class="text-end">
                                    <flux:button size="sm" variant="outline" href="{{ route('admin.books.copies.index', $copy->book) }}">
                                        Kelola
                                    </flux:button>
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
