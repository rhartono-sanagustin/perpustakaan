@extends('layouts.admin')

@section('title', $title)
@section('breadcrumb', 'Laporan / '.$title)

@section('content')
    @php
        $exportQuery = ['report' => $activeReport] + $filters->query();
        $action = $mode === 'peminjaman' ? route('admin.reports.loans') : route('admin.reports.returns');
    @endphp

    <x-page-header :title="$title" :subtitle="$subtitle.' Periode '.$filters->periodLabel().'.'">
        <x-slot:actions>
            <flux:button variant="outline" icon="document-text" href="{{ route('admin.reports.export', $exportQuery) }}">
                Ekspor CSV
            </flux:button>
            <flux:button variant="outline" icon="printer" href="{{ route('admin.reports.print', $exportQuery) }}" target="_blank" rel="noopener">
                Cetak
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    @include('admin.reports._nav')

    @include('admin.reports._filters', [
        'action' => $action,
        'statusOptions' => $mode === 'peminjaman' ? $statusOptions : [],
        'conditionOptions' => $mode === 'pengembalian' ? $conditionOptions : [],
        'statusLabel' => 'Status pinjaman',
        'conditionLabel' => 'Kondisi kembali',
        'searchPlaceholder' => 'Nama peminjam, nomor induk, judul buku, atau kode eksemplar',
        'resetUrl' => $action,
    ])

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">{{ $mode === 'peminjaman' ? 'Daftar Peminjaman' : 'Daftar Pengembalian' }}</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $loans->total() }} transaksi</flux:badge>
        </div>

        @if ($loans->isEmpty())
            <x-empty-state
                icon="file-earmark-text"
                title="Tidak ada data untuk filter periode ini"
                message="Ubah rentang tanggal atau filter lain untuk melihat transaksi."
            >
                <flux:button variant="outline" size="sm" href="{{ $action }}">Ubah Periode</flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Transaksi</th>
                            <th scope="col">Peminjam</th>
                            <th scope="col">Buku / Eksemplar</th>
                            <th scope="col">Tanggal Pinjam</th>
                            <th scope="col">{{ $mode === 'peminjaman' ? 'Jatuh Tempo' : 'Tanggal Kembali' }}</th>
                            <th scope="col">{{ $mode === 'peminjaman' ? 'Status' : 'Kondisi' }}</th>
                            <th scope="col">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>
                                    <div class="font-semibold">#{{ $loan->reference }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="font-semibold">{{ $loan->user->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $loan->user->roleLabel() }} &middot; {{ $loan->user->nomor_induk }}
                                        @if ($loan->user->kelas)
                                            &middot; {{ $loan->user->kelas }}
                                        @elseif ($loan->user->unit_kerja)
                                            &middot; {{ $loan->user->unit_kerja }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $loan->copy->book->title }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->inventory_code }}</div>
                                </td>
                                <td class="text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400">
                                    {{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}
                                </td>
                                <td class="text-xs">
                                    @if ($mode === 'peminjaman')
                                        <span class="{{ $loan->isOverdue() ? 'font-semibold text-rose-600 dark:text-rose-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                                            {{ $loan->due_at->locale('id')->translatedFormat('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-zinc-500 dark:text-zinc-400">
                                            {{ $loan->returned_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($mode === 'peminjaman')
                                        <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" :icon="$loan->isOverdue() ? 'exclamation-triangle' : 'arrow-left-right'" />
                                    @else
                                        <x-status-badge
                                            :label="$loan->return_condition?->label() ?? '-'"
                                            :variant="$loan->return_condition?->badgeVariant() ?? 'secondary'"
                                        />
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
