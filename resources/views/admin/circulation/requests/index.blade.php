@extends('layouts.admin')

@section('title', 'Permintaan Peminjaman')
@section('breadcrumb', 'Sirkulasi / Permintaan')

@section('content')
    <x-page-header title="Permintaan Peminjaman" subtitle="Proses pengajuan guru dan siswa sebelum buku diserahkan.">
        <x-slot:actions>
            <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i>Peminjaman Aktif
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.requests.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-lg-4">
                    <label for="q" class="form-label">Cari</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Nama, nomor induk, judul, atau ISBN">
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        @foreach ($statuses as $requestStatus)
                            <option value="{{ $requestStatus->value }}" @selected($status === $requestStatus->value)>
                                {{ $requestStatus->label() }} ({{ $counts[$requestStatus->value] ?? 0 }})
                            </option>
                        @endforeach
                        <option value="all" @selected($status === 'all')>Semua status</option>
                    </select>
                </div>

                <div class="col-6 col-lg-2">
                    <label for="date_from" class="form-label">Dari tanggal</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>

                <div class="col-6 col-lg-2">
                    <label for="date_to" class="form-label">Sampai tanggal</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>

                <div class="col-6 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                    @if ($search !== '' || $status !== 'menunggu' || $dateFrom || $dateTo)
                        <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Antrean Permintaan</h2>
            <span class="small text-muted">{{ $requests->total() }} data</span>
        </div>

        @if ($requests->isEmpty())
            <div class="card-body">
                <x-empty-state icon="inbox" title="Tidak ada permintaan" message="Tidak ada permintaan yang sesuai dengan filter saat ini." />
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
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $loanRequest)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $loanRequest->user->name }}</div>
                                    <div class="small text-muted">
                                        {{ $loanRequest->user->roleLabel() }} &middot; {{ $loanRequest->user->nomor_induk }}
                                        @if ($loanRequest->user->kelas)
                                            &middot; {{ $loanRequest->user->kelas }}
                                        @elseif ($loanRequest->user->unit_kerja)
                                            &middot; {{ $loanRequest->user->unit_kerja }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $loanRequest->book->title }}</div>
                                    <div class="small text-muted">{{ $loanRequest->book->authors_label ?: 'Penulis tidak diketahui' }}</div>
                                    <div class="small text-muted">#{{ $loanRequest->reference }}</div>
                                </td>
                                <td class="small text-muted">
                                    {{ $loanRequest->requested_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td>
                                    <x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" />
                                    @if ($loanRequest->isApproved() && ! $loanRequest->isExpired())
                                        <div class="small text-muted mt-1">
                                            Ambil s.d. {{ $loanRequest->expires_at?->locale('id')->translatedFormat('d M Y, H:i') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.requests.show', $loanRequest) }}" class="btn btn-sm btn-outline-primary">
                                        {{ $loanRequest->isPending() ? 'Proses' : 'Detail' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">Menampilkan {{ $requests->firstItem() }}&ndash;{{ $requests->lastItem() }} dari {{ $requests->total() }} permintaan</span>
                {{ $requests->links() }}
            </div>
        @endif
    </div>
@endsection
