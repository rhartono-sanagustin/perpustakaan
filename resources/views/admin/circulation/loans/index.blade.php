@extends('layouts.admin')

@section('title', $pageTitle)
@section('breadcrumb', 'Sirkulasi / '.$pageTitle)

@section('content')
    <x-page-header :title="$pageTitle" :subtitle="$pageSubtitle">
        <x-slot:actions>
            <flux:button variant="outline" icon="inbox" href="{{ route('admin.requests.index') }}">
                Permintaan
            </flux:button>
            <flux:button variant="outline" icon="clock" href="{{ route('admin.loans.history') }}">
                Riwayat
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-2 gap-2 md:grid-cols-3">
        <flux:card class="py-3!">
            <div class="text-2xl font-bold">{{ $summary['active'] }}</div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Pinjaman aktif</div>
        </flux:card>
        <flux:card class="py-3!">
            <div class="text-2xl font-bold {{ $summary['overdue'] > 0 ? 'text-rose-600 dark:text-rose-400' : '' }}">{{ $summary['overdue'] }}</div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Terlambat</div>
        </flux:card>
        <flux:card class="flex flex-col justify-center py-3!">
            <flux:link href="{{ $mode === 'returns' ? route('admin.loans.index') : route('admin.returns.index') }}" class="text-sm font-semibold">
                {{ $mode === 'returns' ? 'Lihat peminjaman aktif' : 'Buka halaman pengembalian' }}
                <flux:icon.arrow-right variant="mini" class="ms-1 inline size-4" />
            </flux:link>
        </flux:card>
    </div>

    <flux:card class="mb-4">
        <form method="GET" action="{{ $mode === 'returns' ? route('admin.returns.index') : route('admin.loans.index') }}" class="grid items-end gap-3 md:grid-cols-2 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <flux:field>
                    <flux:label for="q">Cari</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Nama peminjam, nomor induk, kode eksemplar, atau judul buku" />
                </flux:field>
            </div>

            <div class="xl:col-span-2">
                <flux:checkbox id="overdue" name="overdue" value="1" :checked="$overdueOnly" label="Hanya terlambat" class="mb-2" />
            </div>

            <div class="flex gap-2 xl:col-span-2">
                <flux:button type="submit" variant="primary" class="flex-1">Terapkan</flux:button>
                @if ($search !== '' || $overdueOnly)
                    <flux:button variant="outline" href="{{ $mode === 'returns' ? route('admin.returns.index') : route('admin.loans.index') }}" icon="arrow-path" aria-label="Reset filter" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">{{ $mode === 'returns' ? 'Pinjaman yang Dapat Dikembalikan' : 'Daftar Peminjaman Aktif' }}</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $loans->total() }} transaksi</flux:badge>
        </div>

        @if ($loans->isEmpty())
            <x-empty-state
                icon="arrow-left-right"
                title="Tidak ada pinjaman aktif"
                message="Transaksi yang sedang berjalan atau sesuai filter akan tampil di sini."
            />
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Peminjam</th>
                            <th scope="col">Buku / Eksemplar</th>
                            <th scope="col">Dipinjam</th>
                            <th scope="col">Jatuh Tempo</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $loan->user->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $loan->user->roleLabel() }} &middot; {{ $loan->user->nomor_induk }}
                                        @if ($loan->user->kelas)
                                            &middot; {{ $loan->user->kelas }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $loan->copy->book->title }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->inventory_code }} &middot; #{{ $loan->reference }}</div>
                                </td>
                                <td class="text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400">
                                    {{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}
                                </td>
                                <td class="text-xs">
                                    <span class="{{ $loan->isOverdue() ? 'font-semibold text-rose-600 dark:text-rose-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                                        {{ $loan->due_at->locale('id')->translatedFormat('d M Y') }}
                                    </span>
                                    @if ($loan->isOverdue())
                                        <div class="text-xs text-rose-600 dark:text-rose-400">{{ $loan->daysOverdue() }} hari terlambat</div>
                                    @else
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ max(0, $loan->daysUntilDue()) }} hari lagi</div>
                                    @endif
                                </td>
                                <td>
                                    <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" :icon="$loan->isOverdue() ? 'exclamation-triangle' : 'arrow-left-right'" />
                                </td>
                                <td class="text-end">
                                    @if ($mode === 'returns')
                                        <flux:button size="sm" variant="primary" icon="arrow-uturn-left" href="{{ route('admin.loans.return', $loan) }}">
                                            Kembalikan
                                        </flux:button>
                                    @else
                                        <flux:button size="sm" variant="outline" href="{{ route('admin.loans.return', $loan) }}">
                                            Catat Kembali
                                        </flux:button>
                                    @endif
                                </td>
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
