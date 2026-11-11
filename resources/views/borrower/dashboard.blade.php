@extends('layouts.borrower')

@section('title', 'Beranda')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Selamat datang, {{ auth()->user()->name }}</h1>
            <p class="text-muted mb-0">
                {{ auth()->user()->roleLabel() }} &middot; {{ auth()->user()->nomor_induk }}
                @if (auth()->user()->kelas)
                    &middot; {{ auth()->user()->kelas }}
                @elseif (auth()->user()->unit_kerja)
                    &middot; {{ auth()->user()->unit_kerja }}
                @endif
            </p>
        </div>
        <span class="text-muted small align-self-md-end">
            {{ now()->locale('id')->translatedFormat('l, d F Y') }}
        </span>
    </div>

    <div class="card mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h5 mb-1">Cari buku favoritmu</h2>
                <p class="text-muted mb-0">Telusuri katalog dan periksa ketersediaan eksemplar.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">
                <i class="bi bi-search me-2" aria-hidden="true"></i>Cari Buku
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-3 g-3 mb-4">
        <x-kpi-card icon="arrow-left-right" :value="$activeLoansCount" label="Pinjaman Aktif" variant="primary" :href="route('borrower.loans.index')" />
        <x-kpi-card icon="hourglass-split" :value="$pendingRequestsCount" label="Menunggu Diproses" variant="warning" :href="route('borrower.loans.index', ['tab' => 'menunggu'])" />
        <x-kpi-card icon="exclamation-triangle" :value="$overdueLoansCount" label="Terlambat" variant="danger" :href="route('borrower.loans.index', ['tab' => 'terlambat'])" />
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">Buku yang Sedang Dipinjam</h2>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge text-bg-light border">{{ $activeLoansCount }} buku</span>
                        <a href="{{ route('borrower.loans.index') }}" class="small">Lihat semua</a>
                    </div>
                </div>

                @if ($activeLoans->isEmpty())
                    <div class="card-body">
                        <x-empty-state
                            icon="book"
                            title="Belum ada pinjaman aktif"
                            message="Pinjaman yang sedang berjalan akan tampil di sini."
                        />
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($activeLoans as $loan)
                            <li class="list-group-item px-3 py-3">
                                <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $loan->copy->book->title }}</div>
                                        <div class="small text-muted">
                                            {{ $loan->copy->inventory_code }} &middot; {{ $loan->copy->shelf_location ?? 'Lokasi rak belum diatur' }}
                                        </div>
                                    </div>
                                    <div class="text-sm-end">
                                        <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" :icon="$loan->isOverdue() ? 'exclamation-triangle' : 'arrow-left-right'" />
                                        <div class="small {{ $loan->isOverdue() ? 'text-danger' : 'text-muted' }} mt-1">
                                            Jatuh tempo {{ $loan->due_at->locale('id')->translatedFormat('d F Y') }}
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">Permintaan Terbaru</h2>
                    <a href="{{ route('borrower.loans.index', ['tab' => 'menunggu']) }}" class="small">Lihat semua</a>
                </div>

                @if ($pendingRequests->isEmpty())
                    <div class="card-body">
                        <x-empty-state
                            icon="inbox"
                            title="Tidak ada permintaan aktif"
                            message="Permintaan yang menunggu atau disetujui akan tampil di sini."
                        />
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($pendingRequests as $loanRequest)
                            <li class="list-group-item px-3 py-3">
                                <div class="fw-semibold">{{ $loanRequest->book->title }}</div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-1">
                                    <span class="small text-muted">
                                        {{ $loanRequest->requested_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    <x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" />
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Riwayat Terbaru</h2>
            <a href="{{ route('borrower.loans.history') }}" class="small">Lihat semua</a>
        </div>

        @if ($recentHistory->isEmpty())
            <div class="card-body">
                <x-empty-state
                    icon="clock-history"
                    title="Belum ada riwayat"
                    message="Riwayat peminjaman akan tampil setelah transaksi pertama."
                />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Buku</th>
                            <th scope="col">Tanggal Pinjam</th>
                            <th scope="col">Tanggal Kembali</th>
                            <th scope="col">Kondisi</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentHistory as $loan)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $loan->copy->book->title }}</div>
                                    <div class="small text-muted">{{ $loan->copy->inventory_code }}</div>
                                </td>
                                <td class="small text-muted">{{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}</td>
                                <td class="small text-muted">{{ $loan->returned_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}</td>
                                <td>
                                    @if ($loan->return_condition)
                                        <x-status-badge :label="$loan->return_condition->label()" :variant="$loan->return_condition->badgeVariant()" />
                                    @else
                                        <span class="small text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
