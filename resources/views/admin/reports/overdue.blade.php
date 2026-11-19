@extends('layouts.admin')

@section('title', 'Laporan Keterlambatan')
@section('breadcrumb', 'Laporan / Keterlambatan')

@section('content')
    @php
        $exportQuery = ['report' => 'keterlambatan'] + $filters->query();
    @endphp

    <x-page-header
        title="Laporan Keterlambatan"
        subtitle="Daftar pinjaman aktif yang melewati tanggal jatuh tempo saat ini."
    >
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
        'action' => route('admin.reports.overdue'),
        'includeDate' => false,
        'searchPlaceholder' => 'Nama peminjam, nomor induk, judul buku, atau kode eksemplar',
        'resetUrl' => route('admin.reports.overdue'),
    ])

    <flux:callout variant="warning" icon="information-circle" class="mb-4">
        <flux:callout.heading>{{ $loans->total() }} pinjaman terlambat</flux:callout.heading>
        <flux:callout.text>Laporan ini menampilkan kondisi saat ini, bukan dibatasi periode tanggal.</flux:callout.text>
    </flux:callout>

    <flux:card class="p-0!">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Pinjaman Terlambat</flux:heading>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Diurutkan dari jatuh tempo terlama</span>
        </div>

        @if ($loans->isEmpty())
            <x-empty-state
                icon="check2-circle"
                title="Tidak ada pinjaman terlambat"
                message="Semua pinjaman aktif saat ini masih dalam masa tenggat."
            />
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Transaksi</th>
                            <th scope="col">Peminjam / Kontak</th>
                            <th scope="col">Buku / Eksemplar</th>
                            <th scope="col">Jatuh Tempo</th>
                            <th scope="col">Keterlambatan</th>
                            <th scope="col" class="text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>
                                    <div class="font-semibold">#{{ $loan->reference }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Dipinjam {{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}</div>
                                </td>
                                <td>
                                    <div class="font-semibold">{{ $loan->user->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $loan->user->roleLabel() }} &middot; {{ $loan->user->nomor_induk }}
                                    </div>
                                    <div class="mt-0.5 flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        <flux:icon.phone variant="mini" class="size-3.5" />{{ $loan->user->no_telepon ?? 'Nomor telepon belum diisi' }}
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $loan->copy->book->title }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->inventory_code }}</div>
                                </td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $loan->due_at->locale('id')->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <flux:badge color="red">{{ $loan->daysOverdue() }} hari</flux:badge>
                                </td>
                                <td class="text-end">
                                    <flux:button size="sm" variant="outline" icon="arrow-uturn-left" href="{{ route('admin.loans.return', $loan) }}">
                                        Tindak Lanjut
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $loans->firstItem() }}&ndash;{{ $loans->lastItem() }} dari {{ $loans->total() }} pinjaman</span>
                {{ $loans->links() }}
            </div>
        @endif
    </flux:card>
@endsection
