@extends('layouts.admin')

@section('title', 'Riwayat Transaksi')
@section('breadcrumb', 'Sirkulasi / Riwayat Transaksi')

@section('content')
    <x-page-header title="Riwayat Transaksi" subtitle="Telusuri peminjaman yang sudah dikembalikan beserta kondisinya.">
        <x-slot:actions>
            <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i>Peminjaman Aktif
            </a>
            <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-inbox me-1" aria-hidden="true"></i>Permintaan
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="row row-cols-2 g-2 mb-4">
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $returnedCount }}</div>
                    <div class="small text-muted">Total pengembalian tercatat</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $loans->total() }}</div>
                    <div class="small text-muted">Hasil sesuai filter</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.loans.history') }}" class="row g-2 align-items-end">
                <div class="col-12 col-lg-4">
                    <label for="q" class="form-label">Cari</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Nama, nomor induk, kode eksemplar, atau judul">
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <label for="date_from" class="form-label">Dari tanggal</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>

                <div class="col-6 col-lg-2">
                    <label for="date_to" class="form-label">Sampai tanggal</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>

                <div class="col-12 col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                    @if ($search !== '' || $dateFrom || $dateTo)
                        <a href="{{ route('admin.loans.history') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Pengembalian</h2>
            <span class="small text-muted">{{ $loans->total() }} transaksi</span>
        </div>

        @if ($loans->isEmpty())
            <div class="card-body">
                <x-empty-state
                    icon="clock-history"
                    title="Belum ada riwayat pengembalian"
                    message="Transaksi yang sudah dikembalikan akan tampil di sini."
                />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
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
                                    <div class="fw-semibold">{{ $loan->user->name }}</div>
                                    <div class="small text-muted">{{ $loan->user->roleLabel() }} &middot; {{ $loan->user->nomor_induk }}</div>
                                </td>
                                <td>
                                    <div>{{ $loan->copy->book->title }}</div>
                                    <div class="small text-muted">{{ $loan->copy->inventory_code }} &middot; #{{ $loan->reference }}</div>
                                </td>
                                <td class="small text-muted">{{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}</td>
                                <td class="small text-muted">
                                    {{ $loan->returned_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}
                                    @if ($loan->returned_at && $loan->returned_at->gt($loan->due_at))
                                        <div class="small text-danger">Terlambat {{ (int) $loan->due_at->diffInDays($loan->returned_at) }} hari</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($loan->return_condition)
                                        <x-status-badge :label="$loan->return_condition->label()" :variant="$loan->return_condition->badgeVariant()" />
                                    @else
                                        <span class="small text-muted">-</span>
                                    @endif
                                    @if ($loan->return_notes)
                                        <div class="small text-muted mt-1">{{ $loan->return_notes }}</div>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $loan->processor?->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">Menampilkan {{ $loans->firstItem() }}&ndash;{{ $loans->lastItem() }} dari {{ $loans->total() }} transaksi</span>
                {{ $loans->links() }}
            </div>
        @endif
    </div>
@endsection
