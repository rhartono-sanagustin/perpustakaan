@extends('layouts.borrower')

@section('title', 'Pinjaman Saya')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Pinjaman Saya</h1>
            <p class="text-muted mb-0">Pantau pengajuan, pinjaman aktif, dan jatuh tempo buku Anda.</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="btn btn-primary align-self-md-start">
            <i class="bi bi-search me-1" aria-hidden="true"></i>Cari Buku
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Tindakan belum dapat diproses:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $tabs = [
            'menunggu' => ['label' => 'Menunggu Diproses', 'icon' => 'hourglass-split', 'count' => $counts['menunggu']],
            'disetujui' => ['label' => 'Disetujui', 'icon' => 'check-circle', 'count' => $counts['disetujui']],
            'dipinjam' => ['label' => 'Sedang Dipinjam', 'icon' => 'arrow-left-right', 'count' => $counts['dipinjam']],
            'terlambat' => ['label' => 'Terlambat', 'icon' => 'exclamation-triangle', 'count' => $counts['terlambat']],
        ];
    @endphp

    <ul class="nav nav-pills flex-wrap gap-2 mb-4" role="tablist" aria-label="Filter status pinjaman">
        @foreach ($tabs as $key => $item)
            <li class="nav-item" role="presentation">
                <a
                    href="{{ route('borrower.loans.index', ['tab' => $key]) }}"
                    class="nav-link {{ $tab === $key ? 'active' : '' }}"
                    role="tab"
                    aria-selected="{{ $tab === $key ? 'true' : 'false' }}"
                >
                    <i class="bi bi-{{ $item['icon'] }} me-1" aria-hidden="true"></i>{{ $item['label'] }}
                    <span class="badge {{ $tab === $key ? 'text-bg-light' : 'text-bg-secondary' }} ms-1">{{ $item['count'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    @if ($items->isEmpty())
        <div class="card">
            <div class="card-body">
                @if ($tab === 'menunggu')
                    <x-empty-state icon="hourglass-split" title="Tidak ada pengajuan yang menunggu" message="Pengajuan baru akan tampil di sini setelah Anda mengirimkannya dari katalog." />
                @elseif ($tab === 'disetujui')
                    <x-empty-state icon="check-circle" title="Tidak ada pengajuan yang disetujui" message="Pengajuan yang disetujui admin akan tampil di sini beserta batas pengambilannya." />
                @elseif ($tab === 'terlambat')
                    <x-empty-state icon="exclamation-triangle" title="Tidak ada pinjaman terlambat" message="Pinjaman yang melewati jatuh tempo akan ditandai di sini." />
                @else
                    <x-empty-state icon="book" title="Anda belum memiliki pinjaman aktif" message="Jelajahi katalog dan ajukan buku yang ingin Anda baca.">
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm">Jelajahi Katalog</a>
                    </x-empty-state>
                @endif
            </div>
        </div>
    @elseif (in_array($tab, ['menunggu', 'disetujui'], true))
        @foreach ($items as $loanRequest)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                        <div class="min-w-0">
                            <h2 class="h6 mb-1">{{ $loanRequest->book->title }}</h2>
                            <p class="small text-muted mb-2">{{ $loanRequest->book->authors_label ?: 'Penulis tidak diketahui' }}</p>
                            <div class="small text-muted">
                                Diajukan {{ $loanRequest->requested_at->locale('id')->translatedFormat('d F Y, H:i') }}
                                &middot; <span class="text-body">#{{ $loanRequest->reference }}</span>
                            </div>
                        </div>
                        <div class="text-lg-end">
                            <x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" />
                            @if ($loanRequest->isApproved())
                                <div class="small {{ $loanRequest->isExpired() ? 'text-danger' : 'text-muted' }} mt-2">
                                    @if ($loanRequest->isExpired())
                                        Batas pengambilan sudah lewat
                                    @else
                                        Ambil sebelum {{ $loanRequest->expires_at?->locale('id')->translatedFormat('d F Y, H:i') }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($loanRequest->isApproved() && ! $loanRequest->isExpired())
                        <div class="alert alert-info mt-3 mb-0 py-2 small" role="alert">
                            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                            Pengajuan sudah disetujui. Buku belum dipinjam sampai Anda mengambilnya dan admin mencatat checkout.
                        </div>
                    @endif

                    @if ($loanRequest->isApproved() && $loanRequest->isExpired())
                        <div class="alert alert-warning mt-3 mb-0 py-2 small" role="alert">
                            <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>
                            Batas pengambilan sudah lewat. Batalkan pengajuan ini lalu ajukan ulang jika buku masih dibutuhkan.
                        </div>
                    @endif

                    @if ($loanRequest->notes)
                        <div class="small text-muted mt-3">
                            <span class="fw-semibold">Catatan Anda:</span> {{ $loanRequest->notes }}
                        </div>
                    @endif

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('catalog.show', $loanRequest->book) }}" class="btn btn-sm btn-outline-secondary">Lihat Buku</a>

                        @can('cancel', $loanRequest)
                            <form method="POST" action="{{ route('borrower.requests.cancel', $loanRequest) }}" data-confirm="Batalkan pengajuan untuk buku ini?">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger">Batalkan Pengajuan</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
            <span class="small text-muted">Menampilkan {{ $items->firstItem() }}&ndash;{{ $items->lastItem() }} dari {{ $items->total() }} pengajuan</span>
            {{ $items->links() }}
        </div>
    @else
        @foreach ($items as $loan)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                        <div class="min-w-0">
                            <h2 class="h6 mb-1">{{ $loan->copy->book->title }}</h2>
                            <p class="small text-muted mb-2">{{ $loan->copy->book->authors_label ?: 'Penulis tidak diketahui' }}</p>
                            <div class="small text-muted">
                                {{ $loan->copy->inventory_code }} &middot; {{ $loan->copy->shelf_location ?? 'Lokasi rak belum diatur' }}
                                &middot; <span class="text-body">#{{ $loan->reference }}</span>
                            </div>
                            <div class="small text-muted mt-1">
                                Dipinjam {{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}
                            </div>
                        </div>
                        <div class="text-lg-end">
                            <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" :icon="$loan->isOverdue() ? 'exclamation-triangle' : 'arrow-left-right'" />
                            <div class="small {{ $loan->isOverdue() ? 'text-danger' : 'text-muted' }} mt-2">
                                Jatuh tempo {{ $loan->due_at->locale('id')->translatedFormat('d F Y') }}
                            </div>
                            @if ($loan->isOverdue())
                                <div class="small text-danger mt-1">{{ $loan->daysOverdue() }} hari terlambat</div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('catalog.show', $loan->copy->book) }}" class="btn btn-sm btn-outline-secondary">Lihat Buku</a>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
            <span class="small text-muted">Menampilkan {{ $items->firstItem() }}&ndash;{{ $items->lastItem() }} dari {{ $items->total() }} pinjaman</span>
            {{ $items->links() }}
        </div>
    @endif
@endsection
