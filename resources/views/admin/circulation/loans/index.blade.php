@extends('layouts.admin')

@section('title', $pageTitle)
@section('breadcrumb', 'Sirkulasi / '.$pageTitle)

@section('content')
    <x-page-header :title="$pageTitle" :subtitle="$pageSubtitle">
        <x-slot:actions>
            <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-inbox me-1" aria-hidden="true"></i>Permintaan
            </a>
            <a href="{{ route('admin.loans.history') }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Riwayat
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="row row-cols-2 row-cols-md-3 g-2 mb-4">
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $summary['active'] }}</div>
                    <div class="small text-muted">Pinjaman aktif</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold {{ $summary['overdue'] > 0 ? 'text-danger' : '' }}">{{ $summary['overdue'] }}</div>
                    <div class="small text-muted">Terlambat</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3 d-flex flex-column justify-content-center">
                    <a href="{{ $mode === 'returns' ? route('admin.loans.index') : route('admin.returns.index') }}" class="small fw-semibold">
                        {{ $mode === 'returns' ? 'Lihat peminjaman aktif' : 'Buka halaman pengembalian' }}
                        <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ $mode === 'returns' ? route('admin.returns.index') : route('admin.loans.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-lg-8">
                    <label for="q" class="form-label">Cari</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Nama peminjam, nomor induk, kode eksemplar, atau judul buku">
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <div class="form-check mb-2">
                        <input type="checkbox" id="overdue" name="overdue" value="1" class="form-check-input" @checked($overdueOnly)>
                        <label class="form-check-label" for="overdue">Hanya terlambat</label>
                    </div>
                </div>

                <div class="col-6 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                    @if ($search !== '' || $overdueOnly)
                        <a href="{{ $mode === 'returns' ? route('admin.returns.index') : route('admin.loans.index') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">{{ $mode === 'returns' ? 'Pinjaman yang Dapat Dikembalikan' : 'Daftar Peminjaman Aktif' }}</h2>
            <span class="small text-muted">{{ $loans->total() }} transaksi</span>
        </div>

        @if ($loans->isEmpty())
            <div class="card-body">
                <x-empty-state
                    icon="arrow-left-right"
                    title="Tidak ada pinjaman aktif"
                    message="Transaksi yang sedang berjalan atau sesuai filter akan tampil di sini."
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
                            <th scope="col">Jatuh Tempo</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $loan->user->name }}</div>
                                    <div class="small text-muted">
                                        {{ $loan->user->roleLabel() }} &middot; {{ $loan->user->nomor_induk }}
                                        @if ($loan->user->kelas)
                                            &middot; {{ $loan->user->kelas }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $loan->copy->book->title }}</div>
                                    <div class="small text-muted">{{ $loan->copy->inventory_code }} &middot; #{{ $loan->reference }}</div>
                                </td>
                                <td class="small text-muted">
                                    {{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}
                                </td>
                                <td class="small">
                                    <span class="{{ $loan->isOverdue() ? 'text-danger fw-semibold' : 'text-muted' }}">
                                        {{ $loan->due_at->locale('id')->translatedFormat('d M Y') }}
                                    </span>
                                    @if ($loan->isOverdue())
                                        <div class="small text-danger">{{ $loan->daysOverdue() }} hari terlambat</div>
                                    @else
                                        <div class="small text-muted">{{ max(0, $loan->daysUntilDue()) }} hari lagi</div>
                                    @endif
                                </td>
                                <td>
                                    <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" :icon="$loan->isOverdue() ? 'exclamation-triangle' : 'arrow-left-right'" />
                                </td>
                                <td class="text-end">
                                    @if ($mode === 'returns')
                                        <a href="{{ route('admin.loans.return', $loan) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-arrow-return-left me-1" aria-hidden="true"></i>Kembalikan
                                        </a>
                                    @else
                                        <a href="{{ route('admin.loans.return', $loan) }}" class="btn btn-sm btn-outline-primary">
                                            Catat Kembali
                                        </a>
                                    @endif
                                </td>
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
