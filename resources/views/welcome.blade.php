@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
    <section class="container-xl py-4 py-lg-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge text-bg-light border mb-3">Sistem Informasi Perpustakaan Sekolah</span>
                <h1 class="display-5 fw-bold mb-3">
                    Temukan buku, ajukan pinjaman, dan pantau koleksi dalam satu tempat.
                </h1>
                <p class="lead text-muted mb-4">
                    {{ config('library.tagline') }} Kelola koleksi, peminjaman, dan laporan perpustakaan
                    sekolah dengan alur yang jelas dan mudah digunakan.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-search me-2" aria-hidden="true"></i>Jelajahi Katalog
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>Masuk
                    </a>
                </div>
                <p class="small text-muted mt-3 mb-0">
                    Katalog dapat diakses tanpa login. Pengajuan pinjaman memerlukan akun aktif.
                </p>
            </div>

            <div class="col-lg-6">
                <div class="card p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="brand-mark"><i class="bi bi-book-half" aria-hidden="true"></i></span>
                        <div>
                            <div class="fw-semibold">{{ config('library.name') }}</div>
                            <div class="small text-muted">{{ config('library.school_name') }}</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="border p-3 h-100">
                                <i class="bi bi-search text-primary fs-4" aria-hidden="true"></i>
                                <div class="fw-semibold small mt-2">Cari koleksi</div>
                                <div class="text-muted small">Temukan buku berdasarkan judul, penulis, atau ISBN.</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-3 h-100">
                                <i class="bi bi-journal-check text-success fs-4" aria-hidden="true"></i>
                                <div class="fw-semibold small mt-2">Ajukan pinjaman</div>
                                <div class="text-muted small">Kirim pengajuan dan pantau statusnya.</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-3 h-100">
                                <i class="bi bi-calendar-check text-warning fs-4" aria-hidden="true"></i>
                                <div class="fw-semibold small mt-2">Pantau jatuh tempo</div>
                                <div class="text-muted small">Lihat tanggal kembali dan status pinjaman.</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-3 h-100">
                                <i class="bi bi-bar-chart text-info fs-4" aria-hidden="true"></i>
                                <div class="fw-semibold small mt-2">Laporan rapi</div>
                                <div class="text-muted small">Data sirkulasi dan inventaris siap diekspor.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="container-xl py-4">
        <div class="text-center mb-4">
            <h2 class="h4 mb-1">Dirancang untuk seluruh warga sekolah</h2>
            <p class="text-muted mb-0">Satu alur kerja untuk admin, guru, dan siswa.</p>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-3">
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="kpi-icon text-bg-primary mb-3">
                            <i class="bi bi-shield-check" aria-hidden="true"></i>
                        </span>
                        <h3 class="h6">Akses sesuai peran</h3>
                        <p class="text-muted small mb-0">
                            Admin mengelola operasional, guru dan siswa mengakses katalog serta pinjaman miliknya.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="kpi-icon text-bg-success mb-3">
                            <i class="bi bi-collection" aria-hidden="true"></i>
                        </span>
                        <h3 class="h6">Inventaris eksemplar</h3>
                        <p class="text-muted small mb-0">
                            Ketersediaan dihitung dari eksemplar yang benar-benar siap dipinjam.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="kpi-icon text-bg-warning mb-3">
                            <i class="bi bi-clock-history" aria-hidden="true"></i>
                        </span>
                        <h3 class="h6">Transaksi terlacak</h3>
                        <p class="text-muted small mb-0">
                            Setiap pengajuan, penyerahan, dan pengembalian tercatat dengan status yang jelas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
