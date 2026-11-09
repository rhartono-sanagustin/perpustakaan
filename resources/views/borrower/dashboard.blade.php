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
                <p class="text-muted mb-0">Katalog buku akan tersedia pada fase berikutnya.</p>
            </div>
            <button class="btn btn-primary" type="button" disabled>
                <i class="bi bi-search me-2" aria-hidden="true"></i>Cari Buku
            </button>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-3 g-3 mb-4">
        <x-kpi-card icon="arrow-left-right" value="0" label="Pinjaman Aktif" variant="primary" />
        <x-kpi-card icon="hourglass-split" value="0" label="Menunggu Diproses" variant="warning" />
        <x-kpi-card icon="exclamation-triangle" value="0" label="Terlambat" variant="danger" />
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="h6 mb-0">Buku yang Sedang Dipinjam</h2>
                </div>
                <div class="card-body">
                    <x-empty-state
                        icon="book"
                        title="Belum ada pinjaman aktif"
                        message="Pinjaman yang sedang berjalan akan tampil di sini."
                    />
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="h6 mb-0">Riwayat Terbaru</h2>
                </div>
                <div class="card-body">
                    <x-empty-state
                        icon="clock-history"
                        title="Belum ada riwayat"
                        message="Riwayat peminjaman akan tampil setelah transaksi pertama."
                    />
                </div>
            </div>
        </div>
    </div>
@endsection
