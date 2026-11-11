@extends('layouts.borrower')

@section('title', 'Riwayat')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Riwayat</h1>
            <p class="text-muted mb-0">Lihat riwayat peminjaman yang sudah dikembalikan dan pengajuan yang selesai.</p>
        </div>
        <a href="{{ route('borrower.loans.index') }}" class="btn btn-outline-primary align-self-md-start">
            <i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i>Pinjaman Saya
        </a>
    </div>

    <ul class="nav nav-pills flex-wrap gap-2 mb-4" role="tablist" aria-label="Jenis riwayat">
        <li class="nav-item" role="presentation">
            <a href="{{ route('borrower.loans.history', ['tab' => 'pinjaman']) }}" class="nav-link {{ $tab === 'pinjaman' ? 'active' : '' }}" role="tab" aria-selected="{{ $tab === 'pinjaman' ? 'true' : 'false' }}">
                <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Riwayat Peminjaman
                <span class="badge {{ $tab === 'pinjaman' ? 'text-bg-light' : 'text-bg-secondary' }} ms-1">{{ $returnedCount }}</span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('borrower.loans.history', ['tab' => 'pengajuan']) }}" class="nav-link {{ $tab === 'pengajuan' ? 'active' : '' }}" role="tab" aria-selected="{{ $tab === 'pengajuan' ? 'true' : 'false' }}">
                <i class="bi bi-inbox me-1" aria-hidden="true"></i>Riwayat Pengajuan
                <span class="badge {{ $tab === 'pengajuan' ? 'text-bg-light' : 'text-bg-secondary' }} ms-1">{{ $finishedRequestCount }}</span>
            </a>
        </li>
    </ul>

    @if ($tab === 'pengajuan')
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Pengajuan Ditolak atau Dibatalkan</h2>
                <span class="small text-muted">{{ $requestHistory->total() }} data</span>
            </div>

            @if ($requestHistory->isEmpty())
                <div class="card-body">
                    <x-empty-state icon="inbox" title="Belum ada riwayat pengajuan" message="Pengajuan yang ditolak atau dibatalkan akan tampil di sini." />
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Buku</th>
                                <th scope="col">Diajukan</th>
                                <th scope="col">Status</th>
                                <th scope="col">Alasan / Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requestHistory as $loanRequest)
                                <tr>
                                    <td>
                                        <a href="{{ route('catalog.show', $loanRequest->book) }}" class="fw-semibold text-reset">{{ $loanRequest->book->title }}</a>
                                        <div class="small text-muted">#{{ $loanRequest->reference }}</div>
                                    </td>
                                    <td class="small text-muted">{{ $loanRequest->requested_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                    <td><x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" /></td>
                                    <td class="small text-muted">
                                        {{ $loanRequest->rejection_reason ?: ($loanRequest->notes ?: '-') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="small text-muted">Menampilkan {{ $requestHistory->firstItem() }}&ndash;{{ $requestHistory->lastItem() }} dari {{ $requestHistory->total() }} pengajuan</span>
                    {{ $requestHistory->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Peminjaman yang Sudah Dikembalikan</h2>
                <span class="small text-muted">{{ $loanHistory->total() }} data</span>
            </div>

            @if ($loanHistory->isEmpty())
                <div class="card-body">
                    <x-empty-state icon="clock-history" title="Belum ada riwayat peminjaman" message="Peminjaman yang sudah dikembalikan akan tampil di sini.">
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm">Jelajahi Katalog</a>
                    </x-empty-state>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Buku</th>
                                <th scope="col">Dipinjam</th>
                                <th scope="col">Dikembalikan</th>
                                <th scope="col">Kondisi</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loanHistory as $loan)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $loan->copy->book->title }}</div>
                                        <div class="small text-muted">{{ $loan->copy->inventory_code }} &middot; #{{ $loan->reference }}</div>
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
                                    <td><x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="small text-muted">Menampilkan {{ $loanHistory->firstItem() }}&ndash;{{ $loanHistory->lastItem() }} dari {{ $loanHistory->total() }} peminjaman</span>
                    {{ $loanHistory->links() }}
                </div>
            @endif
        </div>
    @endif
@endsection
