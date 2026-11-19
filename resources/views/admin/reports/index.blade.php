@extends('layouts.admin')

@section('title', 'Ringkasan Laporan')
@section('breadcrumb', 'Laporan / Ringkasan')

@section('content')
    @php
        $exportQuery = ['report' => 'ringkasan'] + $filters->query();
    @endphp

    <x-page-header
        title="Ringkasan Operasional"
        :subtitle="'Ringkasan koleksi dan aktivitas perpustakaan periode '.$filters->periodLabel().'.'"
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
        'action' => route('admin.reports.index'),
        'includeCategory' => false,
        'includeRole' => false,
        'searchPlaceholder' => 'Pencarian memengaruhi daftar buku populer',
        'resetUrl' => route('admin.reports.index'),
    ])

    <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi-card icon="journal-bookmark" :value="$summary['total_books']" label="Total Judul Aktif" variant="primary" />
        <x-kpi-card icon="collection" :value="$summary['total_copies']" label="Total Eksemplar" variant="info" />
        <x-kpi-card icon="arrow-left-right" :value="$summary['active_loans']" label="Peminjaman Aktif" variant="primary" />
        <x-kpi-card icon="exclamation-triangle" :value="$summary['overdue_loans']" label="Terlambat" variant="danger" />
        <x-kpi-card icon="hourglass-split" :value="$summary['pending_requests']" label="Permintaan Menunggu" variant="warning" />
        <x-kpi-card icon="inbox" :value="$summary['approved_requests']" label="Disetujui, Menunggu Diambil" variant="info" />
        <x-kpi-card icon="calendar-check" :value="$summary['loans_in_period']" label="Peminjaman pada Periode" variant="success" />
        <x-kpi-card icon="arrow-return-left" :value="$summary['returns_in_period']" label="Pengembalian pada Periode" variant="success" />
    </div>

    <flux:card class="mb-4 p-0!">
        <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Ringkasan Eksemplar per Status</flux:heading>
        </div>
        <div class="grid grid-cols-2 gap-2 p-5 md:grid-cols-3 xl:grid-cols-5">
            @foreach ($copyStatuses as $copyStatus)
                <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $copyStatus->label() }}</div>
                    <div class="text-xl font-semibold">{{ $summary['copies_by_status'][$copyStatus->value] ?? 0 }}</div>
                </div>
            @endforeach
        </div>
    </flux:card>

    <div class="grid gap-3 xl:grid-cols-2">
        <flux:card class="p-0!">
            <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Rekap Kategori</flux:heading>
                <flux:badge color="zinc" size="sm">{{ $summary['categories']->count() }} kategori</flux:badge>
            </div>

            @if ($summary['categories']->isEmpty())
                <x-empty-state icon="tags" title="Belum ada kategori" message="Tambahkan kategori untuk melihat rekap koleksi." />
            @else
                <div class="overflow-x-auto" data-scrollable-region>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th scope="col">Kategori</th>
                                <th scope="col" class="text-end">Judul Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($summary['categories'] as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td class="text-end">{{ $category->active_books_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </flux:card>

        <flux:card class="p-0!">
            <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Buku Paling Populer</flux:heading>
                <flux:link href="{{ route('admin.reports.popular', $filters->query()) }}" class="text-sm">Lihat laporan</flux:link>
            </div>

            @if ($summary['top_books']->isEmpty())
                <x-empty-state icon="star" title="Belum ada data peminjaman" message="Tidak ada peminjaman pada periode ini." />
            @else
                <div class="overflow-x-auto" data-scrollable-region>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Judul</th>
                                <th scope="col" class="text-end">Checkout</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($summary['top_books'] as $index => $book)
                                <tr>
                                    <td class="text-zinc-500 dark:text-zinc-400">{{ $index + 1 }}</td>
                                    <td>
                                        <div>{{ $book->title }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $book->category?->name ?? 'Tanpa kategori' }}</div>
                                    </td>
                                    <td class="text-end font-semibold">{{ $book->checkout_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </flux:card>
    </div>
@endsection
