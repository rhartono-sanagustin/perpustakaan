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
            <a href="{{ route('admin.reports.export', $exportQuery) }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-spreadsheet me-1" aria-hidden="true"></i>Ekspor CSV
            </a>
            <a href="{{ route('admin.reports.print', $exportQuery) }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
                <i class="bi bi-printer me-1" aria-hidden="true"></i>Cetak
            </a>
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

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <x-kpi-card icon="journal-bookmark" :value="$summary['total_books']" label="Total Judul Aktif" variant="primary" />
        <x-kpi-card icon="collection" :value="$summary['total_copies']" label="Total Eksemplar" variant="info" />
        <x-kpi-card icon="arrow-left-right" :value="$summary['active_loans']" label="Peminjaman Aktif" variant="primary" />
        <x-kpi-card icon="exclamation-triangle" :value="$summary['overdue_loans']" label="Terlambat" variant="danger" />
        <x-kpi-card icon="hourglass-split" :value="$summary['pending_requests']" label="Permintaan Menunggu" variant="warning" />
        <x-kpi-card icon="inbox" :value="$summary['approved_requests']" label="Disetujui, Menunggu Diambil" variant="info" />
        <x-kpi-card icon="calendar-check" :value="$summary['loans_in_period']" label="Peminjaman pada Periode" variant="success" />
        <x-kpi-card icon="arrow-return-left" :value="$summary['returns_in_period']" label="Pengembalian pada Periode" variant="success" />
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h2 class="h6 mb-0">Ringkasan Eksemplar per Status</h2>
        </div>
        <div class="card-body">
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-2">
                @foreach ($copyStatuses as $copyStatus)
                    <div class="col">
                        <div class="border rounded p-3 h-100">
                            <div class="small text-muted">{{ $copyStatus->label() }}</div>
                            <div class="fs-4 fw-semibold">{{ $summary['copies_by_status'][$copyStatus->value] ?? 0 }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">Rekap Kategori</h2>
                    <span class="small text-muted">{{ $summary['categories']->count() }} kategori</span>
                </div>

                @if ($summary['categories']->isEmpty())
                    <div class="card-body">
                        <x-empty-state icon="tags" title="Belum ada kategori" message="Tambahkan kategori untuk melihat rekap koleksi." />
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
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
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">Buku Paling Populer</h2>
                    <a href="{{ route('admin.reports.popular', $filters->query()) }}" class="small">Lihat laporan</a>
                </div>

                @if ($summary['top_books']->isEmpty())
                    <div class="card-body">
                        <x-empty-state icon="star" title="Belum ada data peminjaman" message="Tidak ada peminjaman pada periode ini." />
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
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
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div>{{ $book->title }}</div>
                                            <div class="small text-muted">{{ $book->category?->name ?? 'Tanpa kategori' }}</div>
                                        </td>
                                        <td class="text-end fw-semibold">{{ $book->checkout_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
