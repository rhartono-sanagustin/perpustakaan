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
        <x-kpi-card icon="journal-bookmark" value="0" label="Total Judul" variant="primary" />
        <x-kpi-card icon="collection" value="0" label="Eksemplar Tersedia" variant="success" />
        <x-kpi-card icon="arrow-left-right" value="0" label="Peminjaman Aktif" variant="info" />
        <x-kpi-card icon="exclamation-triangle" value="0" label="Terlambat" variant="danger" />
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">Permintaan Peminjaman Terbaru</h2>
                    <span class="badge text-bg-light border">Fase 2</span>
                </div>
                <div class="card-body">
                    <x-empty-state
                        icon="inbox"
                        title="Belum ada permintaan"
                        message="Data permintaan akan tampil setelah modul katalog dan sirkulasi tersedia."
                    />
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="h6 mb-0">Perlu Ditindaklanjuti</h2>
                </div>
                <div class="card-body">
                    <x-empty-state
                        icon="check2-circle"
                        title="Semua terkendali"
                        message="Belum ada pinjaman terlambat atau permintaan yang menunggu."
                    />
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h6 mb-1">Fondasi aplikasi siap</h2>
                <p class="text-muted mb-0 small">
                    Autentikasi, layout Bootstrap, dan konfigurasi dasar sudah aktif.
                    Modul katalog dan sirkulasi akan ditambahkan pada fase berikutnya.
                </p>
            </div>
            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-up-right me-2" aria-hidden="true"></i>Lihat Halaman Publik
            </a>
        </div>
    </div>
@endsection
