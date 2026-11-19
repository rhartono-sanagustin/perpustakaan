@extends('layouts.admin')

@section('title', 'Riwayat Transaksi')
@section('breadcrumb', 'Sirkulasi / Riwayat Transaksi')

@section('content')
    <x-page-header title="Riwayat Transaksi" subtitle="Telusuri peminjaman yang sudah dikembalikan beserta kondisinya.">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrows-right-left" href="{{ route('admin.loans.index') }}">
                Peminjaman Aktif
            </flux:button>
            <flux:button variant="outline" icon="inbox" href="{{ route('admin.requests.index') }}">
                Permintaan
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-2 gap-2">
        <flux:card class="py-3!">
            <div class="text-2xl font-bold">{{ $returnedCount }}</div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Total pengembalian tercatat</div>
        </flux:card>
        <flux:card class="py-3!">
            <div class="text-2xl font-bold">{{ $loans->total() }}</div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Hasil sesuai filter</div>
        </flux:card>
    </div>

    <flux:card class="mb-4">
        <form method="GET" action="{{ route('admin.loans.history') }}" class="grid items-end gap-3 md:grid-cols-2 xl:grid-cols-12">
            <div class="xl:col-span-4">
                <flux:field>
                    <flux:label for="q">Cari</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Nama, nomor induk, kode eksemplar, atau judul" />
                </flux:field>
            </div>

            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label for="date_from">Dari tanggal</flux:label>
                    <flux:input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" />
                </flux:field>
            </div>

            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label for="date_to">Sampai tanggal</flux:label>
                    <flux:input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" />
                </flux:field>
            </div>

            <div class="flex gap-2 xl:col-span-4">
                <flux:button type="submit" variant="primary" class="flex-1">Terapkan</flux:button>
                @if ($search !== '' || $dateFrom || $dateTo)
                    <flux:button variant="outline" href="{{ route('admin.loans.history') }}" icon="arrow-path" aria-label="Reset filter" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Pengembalian</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $loans->total() }} transaksi</flux:badge>
        </div>

        @if ($loans->isEmpty())
            <x-empty-state
                icon="clock-history"
                title="Belum ada riwayat pengembalian"
                message="Transaksi yang sudah dikembalikan akan tampil di sini."
            />
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Peminjam</th>
                            <th scope="col">Buku / Eksemplar</th>
                            <th scope="col">Dipinjam</th>
                            <th scope="col">Dikembalikan</th>
                            <th scope="col">Kondisi</th>
                            <th scope="col">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $loan->user->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->user->roleLabel() }} &middot; {{ $loan->user->nomor_induk }}</div>
                                </td>
                                <td>
                                    <div>{{ $loan->copy->book->title }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->inventory_code }} &middot; #{{ $loan->reference }}</div>
                                </td>
                                <td class="text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}</td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $loan->returned_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}
                                    @if ($loan->returned_at && $loan->returned_at->gt($loan->due_at))
                                        <div class="text-rose-600 dark:text-rose-400">Terlambat {{ (int) $loan->due_at->diffInDays($loan->returned_at) }} hari</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($loan->return_condition)
                                        <x-status-badge :label="$loan->return_condition->label()" :variant="$loan->return_condition->badgeVariant()" />
                                    @else
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">-</span>
                                    @endif
                                    @if ($loan->return_notes)
                                        <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->return_notes }}</div>
                                    @endif
                                </td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->processor?->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $loans->firstItem() }}&ndash;{{ $loans->lastItem() }} dari {{ $loans->total() }} transaksi</span>
                {{ $loans->links() }}
            </div>
        @endif
    </flux:card>
@endsection
