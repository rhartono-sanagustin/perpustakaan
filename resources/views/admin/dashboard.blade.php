@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Ringkasan operasional perpustakaan.</p>
        </div>
        <span class="text-muted small align-self-md-end">
            {{ now()->locale('id')->translatedFormat('l, d F Y') }}
        </span>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <x-kpi-card icon="journal-bookmark" :value="$totalBooks" label="Total Judul Aktif" variant="primary" :href="route('admin.books.index')" />
        <x-kpi-card icon="collection" :value="$availableCopies" label="Eksemplar Tersedia" variant="success" :href="route('admin.copies.index', ['status' => 'tersedia'])" />
        <x-kpi-card icon="arrow-left-right" :value="$activeLoans" label="Peminjaman Aktif" variant="info" />
        <x-kpi-card icon="exclamation-triangle" :value="$overdueLoansCount" label="Terlambat" variant="danger" />
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">Permintaan Peminjaman Terbaru</h2>
                    <span class="badge text-bg-light border">{{ $pendingRequestsCount }} menunggu</span>
                </div>

                @if ($recentRequests->isEmpty())
                    <div class="card-body">
                        <x-empty-state
                            icon="inbox"
                            title="Belum ada permintaan"
                            message="Permintaan dari guru dan siswa akan tampil di sini."
                        />
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Peminjam</th>
                                    <th scope="col">Buku</th>
                                    <th scope="col">Diajukan</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentRequests as $loanRequest)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $loanRequest->user->name }}</div>
                                            <div class="small text-muted">{{ $loanRequest->user->roleLabel() }} &middot; {{ $loanRequest->user->nomor_induk }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $loanRequest->book->title }}</div>
                                            <div class="small text-muted">{{ $loanRequest->book->authors_label }}</div>
                                        </td>
                                        <td class="small text-muted">
                                            {{ $loanRequest->requested_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                        </td>
                                        <td>
                                            <x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="h6 mb-0">Perlu Ditindaklanjuti</h2>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <div>
                            <div class="fw-semibold">Permintaan menunggu</div>
                            <div class="small text-muted">Belum diproses admin</div>
                        </div>
                        <span class="badge text-bg-warning">{{ $pendingRequestsCount }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <div>
                            <div class="fw-semibold">Pinjaman terlambat</div>
                            <div class="small text-muted">Melewati tanggal jatuh tempo</div>
                        </div>
                        <span class="badge text-bg-danger">{{ $overdueLoansCount }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">Eksemplar rusak/hilang</div>
                            <div class="small text-muted">Perlu pemeriksaan inventaris</div>
                        </div>
                        <span class="badge text-bg-secondary">{{ $damagedCopiesCount }}</span>
                    </div>

                    @if ($overdueLoans->isNotEmpty())
                        <hr>
                        <h3 class="h6">Terlambat teratas</h3>
                        <ul class="list-unstyled small mb-0">
                            @foreach ($overdueLoans as $loan)
                                <li class="mb-2">
                                    <div class="fw-semibold">{{ $loan->user->name }}</div>
                                    <div class="text-muted">{{ $loan->copy->book->title }}</div>
                                    <div class="text-danger">
                                        <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>
                                        {{ $loan->daysOverdue() }} hari terlambat
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h6 mb-1">Akses cepat</h2>
                <p class="text-muted mb-0 small">Kelola koleksi dan pantau katalog publik.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Buku
                </a>
                <a href="{{ route('admin.copies.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-collection me-1" aria-hidden="true"></i>Inventaris
                </a>
                <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Katalog Publik
                </a>
            </div>
        </div>
    </div>
@endsection
