@extends('layouts.public')

@section('title', $book->title)

@section('content')
    <section class="container-xl py-4 py-lg-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Katalog</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Buku</li>
            </ol>
        </nav>

        <div class="row g-4 g-lg-5">
            <div class="col-12 col-md-4 col-lg-3">
                <div class="card">
                    <img src="{{ $book->cover_url }}" alt="Sampul {{ $book->title }}" class="card-img-top book-cover">
                </div>
            </div>

            <div class="col-12 col-md-8 col-lg-9">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge text-bg-primary">{{ $book->category->name }}</span>
                    @if ($book->hasAvailableCopies())
                        <x-status-badge label="Tersedia {{ $book->available_copies_count }} eksemplar" variant="success" icon="check-circle" />
                    @else
                        <x-status-badge label="Sedang tidak tersedia" variant="secondary" icon="x-circle" />
                    @endif
                </div>

                <h1 class="h2 mb-2">{{ $book->title }}</h1>
                <p class="text-muted mb-4">
                    oleh <span class="text-body">{{ $book->authors_label ?: 'Penulis tidak diketahui' }}</span>
                </p>

                <dl class="row small mb-4">
                    <dt class="col-sm-3 col-lg-2 text-muted">Penerbit</dt>
                    <dd class="col-sm-9 col-lg-10">{{ $book->publisher?->name ?? '-' }}</dd>

                    <dt class="col-sm-3 col-lg-2 text-muted">Tahun terbit</dt>
                    <dd class="col-sm-9 col-lg-10">{{ $book->publication_year ?? '-' }}</dd>

                    <dt class="col-sm-3 col-lg-2 text-muted">Edisi</dt>
                    <dd class="col-sm-9 col-lg-10">{{ $book->edition ?? '-' }}</dd>

                    <dt class="col-sm-3 col-lg-2 text-muted">ISBN</dt>
                    <dd class="col-sm-9 col-lg-10">{{ $book->isbn ?? 'Tidak tersedia' }}</dd>

                    <dt class="col-sm-3 col-lg-2 text-muted">Bahasa</dt>
                    <dd class="col-sm-9 col-lg-10">{{ $book->language }}</dd>
                </dl>

                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Ketersediaan Eksemplar</h2>
                    </div>
                    <div class="card-body">
                        <div class="row row-cols-2 row-cols-sm-4 g-3 text-center">
                            <div class="col">
                                <div class="fs-4 fw-bold">{{ $book->copies_count }}</div>
                                <div class="small text-muted">Total eksemplar</div>
                            </div>
                            <div class="col">
                                <div class="fs-4 fw-bold text-success">{{ $book->available_copies_count }}</div>
                                <div class="small text-muted">Tersedia</div>
                            </div>
                            <div class="col">
                                <div class="fs-4 fw-bold text-primary">{{ $book->borrowed_copies_count }}</div>
                                <div class="small text-muted">Dipinjam</div>
                            </div>
                            <div class="col">
                                <div class="fs-4 fw-bold text-warning">{{ $book->damaged_copies_count }}</div>
                                <div class="small text-muted">Rusak/hilang</div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                            <span class="small text-muted">Lokasi rak tersedia:</span>
                            @forelse ($shelfLocations as $shelf)
                                <span class="badge text-bg-light border">{{ $shelf }}</span>
                            @empty
                                <span class="small text-muted">Belum ada eksemplar tersedia.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <h2 class="h5">Ringkasan</h2>
                <p class="text-body">
                    {{ $book->summary ?: 'Ringkasan buku belum tersedia.' }}
                </p>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-primary">
                                <i class="bi bi-pencil-square me-2" aria-hidden="true"></i>Kelola Buku
                            </a>
                        @elseif ($book->hasAvailableCopies())
                            <button type="button" class="btn btn-primary btn-lg" disabled>
                                <i class="bi bi-journal-plus me-2" aria-hidden="true"></i>Ajukan Peminjaman
                            </button>
                            <div class="w-100 form-text">
                                Fitur pengajuan peminjaman akan tersedia pada tahap sirkulasi berikutnya.
                            </div>
                        @else
                            <button type="button" class="btn btn-secondary btn-lg" disabled>
                                <i class="bi bi-x-circle me-2" aria-hidden="true"></i>Belum Dapat Diajukan
                            </button>
                            <div class="w-100 form-text">
                                Semua eksemplar sedang dipinjam atau tidak tersedia.
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>Masuk untuk Mengajukan
                        </a>
                        <div class="w-100 form-text">
                            Anda dapat melihat katalog tanpa login. Pengajuan pinjaman memerlukan akun guru atau siswa yang aktif.
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        @if ($relatedBooks->isNotEmpty())
            <div class="mt-5 pt-4 border-top">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Buku Lain di Kategori {{ $book->category->name }}</h2>
                    <a href="{{ route('catalog.index', ['category' => $book->category->slug]) }}" class="small">Lihat semua</a>
                </div>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
                    @foreach ($relatedBooks as $relatedBook)
                        <x-book-card :book="$relatedBook" />
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
