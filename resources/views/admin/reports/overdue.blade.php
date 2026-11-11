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
        'action' => route('admin.reports.overdue'),
        'includeDate' => false,
        'searchPlaceholder' => 'Nama peminjam, nomor induk, judul buku, atau kode eksemplar',
        'resetUrl' => route('admin.reports.overdue'),
    ])

    <div class="alert alert-warning d-flex align-items-start gap-2" role="status">
        <i class="bi bi-info-circle mt-1" aria-hidden="true"></i>
        <div>
            <div class="fw-semibold">{{ $loans->total() }} pinjaman terlambat</div>
            <div class="small">Laporan ini menampilkan kondisi saat ini, bukan dibatasi periode tanggal.</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Pinjaman Terlambat</h2>
            <span class="small text-muted">Diurutkan dari jatuh tempo terlama</span>
        </div>

        @if ($loans->isEmpty())
            <div class="card-body">
                <x-empty-state
                    icon="check2-circle"
                    title="Tidak ada pinjaman terlambat"
                    message="Semua pinjaman aktif saat ini masih dalam masa tenggat."
                />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
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
                                    <div class="fw-semibold">#{{ $loan->reference }}</div>
                                    <div class="small text-muted">Dipinjam {{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $loan->user->name }}</div>
                                    <div class="small text-muted">
                                        {{ $loan->user->roleLabel() }} &middot; {{ $loan->user->nomor_induk }}
                                    </div>
                                    <div class="small text-muted">
                                        <i class="bi bi-telephone me-1" aria-hidden="true"></i>{{ $loan->user->no_telepon ?? 'Nomor telepon belum diisi' }}
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $loan->copy->book->title }}</div>
                                    <div class="small text-muted">{{ $loan->copy->inventory_code }}</div>
                                </td>
                                <td class="small text-muted">
                                    {{ $loan->due_at->locale('id')->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge text-bg-danger">{{ $loan->daysOverdue() }} hari</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.loans.return', $loan) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-arrow-return-left me-1" aria-hidden="true"></i>Tindak Lanjut
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">Menampilkan {{ $loans->firstItem() }}&ndash;{{ $loans->lastItem() }} dari {{ $loans->total() }} pinjaman</span>
                {{ $loans->links() }}
            </div>
        @endif
    </div>
@endsection
