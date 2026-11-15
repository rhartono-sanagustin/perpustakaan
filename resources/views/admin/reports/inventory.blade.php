@extends('layouts.admin')

@section('title', 'Laporan Inventaris')
@section('breadcrumb', 'Laporan / Inventaris')

@section('content')
    @php
        $exportQuery = ['report' => 'inventaris'] + $filters->query();
    @endphp

    <x-page-header
        title="Laporan Inventaris"
        subtitle="Daftar eksemplar beserta status, kondisi, dan lokasi penyimpanan."
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
        'action' => route('admin.reports.inventory'),
        'includeDate' => false,
        'includeRole' => false,
        'statusOptions' => $statusOptions,
        'conditionOptions' => $conditionOptions,
        'statusLabel' => 'Status eksemplar',
        'conditionLabel' => 'Kondisi fisik',
        'searchPlaceholder' => 'Kode inventaris, judul buku, atau ISBN',
        'resetUrl' => route('admin.reports.inventory'),
    ])

    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-2 mb-4">
        @foreach ($copyStatuses as $copyStatus)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-semibold">{{ $copiesByStatus[$copyStatus->value] ?? 0 }}</div>
                        <div class="small text-muted">{{ $copyStatus->label() }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Daftar Eksemplar</h2>
            <span class="small text-muted">{{ $copies->total() }} eksemplar</span>
        </div>

        @if ($copies->isEmpty())
            <div class="card-body">
                <x-empty-state
                    icon="box-seam"
                    title="Tidak ada eksemplar yang cocok"
                    message="Ubah filter status, kondisi, atau kata kunci pencarian."
                >
                    <a href="{{ route('admin.reports.inventory') }}" class="btn btn-outline-primary btn-sm">Reset Filter</a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Kode Inventaris</th>
                            <th scope="col">Judul Buku</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Lokasi Rak</th>
                            <th scope="col">Kondisi</th>
                            <th scope="col">Status</th>
                            <th scope="col">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($copies as $copy)
                            <tr>
                                <td class="fw-semibold">{{ $copy->inventory_code }}</td>
                                <td>{{ $copy->book->title }}</td>
                                <td class="small text-muted">{{ $copy->book->category?->name ?? '-' }}</td>
                                <td class="small text-muted">{{ $copy->shelf_location ?? '-' }}</td>
                                <td>
                                    <x-status-badge :label="$copy->condition->label()" :variant="$copy->condition->badgeVariant()" />
                                </td>
                                <td>
                                    <x-status-badge :label="$copy->status->label()" :variant="$copy->status->badgeVariant()" />
                                </td>
                                <td class="small text-muted">{{ $copy->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">Menampilkan {{ $copies->firstItem() }}&ndash;{{ $copies->lastItem() }} dari {{ $copies->total() }} eksemplar</span>
                {{ $copies->links() }}
            </div>
        @endif
    </div>
@endsection
