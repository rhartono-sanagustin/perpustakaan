@extends('layouts.public')

@section('title', 'Katalog Buku')

@section('content')
    <section class="container-xl py-4 py-lg-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Katalog Buku</h1>
                <p class="text-muted mb-0">Cari koleksi perpustakaan dan periksa ketersediaan eksemplar.</p>
            </div>
            <span class="badge text-bg-light border align-self-md-center">
                {{ $books->total() }} judul ditemukan
            </span>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('catalog.index') }}" role="search" class="row g-3 align-items-end">
                    <div class="col-12 col-lg-5">
                        <label for="q" class="form-label">Cari buku</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                            <input
                                type="search"
                                id="q"
                                name="q"
                                value="{{ $search }}"
                                class="form-control"
                                placeholder="Cari judul, penulis, ISBN, atau kata kunci"
                                maxlength="100"
                            >
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="category" class="form-label">Kategori</label>
                        <select id="category" name="category" class="form-select">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" @selected($categorySlug === $category->slug)>
                                    {{ $category->name }} ({{ $category->books_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-2">
                        <label for="availability" class="form-label">Ketersediaan</label>
                        <select id="availability" name="availability" class="form-select">
                            <option value="">Semua</option>
                            <option value="tersedia" @selected($availability === 'tersedia')>Tersedia</option>
                            <option value="habis" @selected($availability === 'habis')>Tidak tersedia</option>
                        </select>
                    </div>

                    <div class="col-12 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel me-1" aria-hidden="true"></i>Terapkan
                        </button>
                        @if ($search !== '' || $categorySlug !== '' || $availability !== '')
                            <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                                <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                </form>

                @if ($search !== '' || $selectedCategory || $availability !== '')
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top">
                        <span class="small text-muted">Filter aktif:</span>
                        @if ($search !== '')
                            <span class="badge text-bg-light border">Kata kunci: {{ $search }}</span>
                        @endif
                        @if ($selectedCategory)
                            <span class="badge text-bg-light border">Kategori: {{ $selectedCategory->name }}</span>
                        @endif
                        @if ($availability !== '')
                            <span class="badge text-bg-light border">
                                {{ $availability === 'tersedia' ? 'Tersedia' : 'Tidak tersedia' }}
                            </span>
                        @endif
                        <a href="{{ route('catalog.index') }}" class="small ms-auto">Reset filter</a>
                    </div>
                @endif
            </div>
        </div>

        @if ($books->isEmpty())
            <div class="card">
                <div class="card-body">
                    <x-empty-state
                        icon="search"
                        title="Buku tidak ditemukan"
                        message="Coba ubah kata kunci atau reset filter untuk melihat seluruh koleksi."
                    >
                        <a href="{{ route('catalog.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i>Reset Filter
                        </a>
                    </x-empty-state>
                </div>
            </div>
        @else
            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mb-3">
                <span class="small text-muted">
                    Menampilkan {{ $books->firstItem() }}&ndash;{{ $books->lastItem() }} dari {{ $books->total() }} judul
                </span>
                <span class="small text-muted">
                    <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                    Ketersediaan dihitung dari eksemplar berstatus Tersedia.
                </span>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
                @foreach ($books as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>

            <div class="mt-4">
                {{ $books->links() }}
            </div>
        @endif
    </section>
@endsection
