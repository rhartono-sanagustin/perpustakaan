@extends('layouts.admin')

@section('title', 'Daftar Buku')
@section('breadcrumb', 'Buku')

@section('content')
    <x-page-header title="Daftar Buku" subtitle="Kelola data bibliografi, status katalog, dan eksemplar.">
        <x-slot:actions>
            <a href="{{ route('admin.books.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Buku
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.books.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-lg-5">
                    <label for="q" class="form-label">Cari</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Judul, ISBN, penulis, atau kata kunci">
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-lg-2">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                        <option value="arsip" @selected($status === 'arsip')>Diarsipkan</option>
                    </select>
                </div>

                <div class="col-12 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                    @if ($search !== '' || $categoryId || $status !== '')
                        <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Data Buku</h2>
            <span class="small text-muted">{{ $books->total() }} judul</span>
        </div>

        @if ($books->isEmpty())
            <div class="card-body">
                <x-empty-state icon="journal-bookmark" title="Belum ada data buku" message="Tambahkan buku pertama atau ubah filter pencarian.">
                    <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Buku
                    </a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Sampul</th>
                            <th scope="col">Judul / ISBN</th>
                            <th scope="col">Kategori</th>
                            <th scope="col" class="text-center">Total</th>
                            <th scope="col" class="text-center">Tersedia</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                            <tr>
                                <td>
                                    <img src="{{ $book->cover_url }}" alt="" class="admin-cover">
                                </td>
                                <td>
                                    <a href="{{ route('admin.books.edit', $book) }}" class="fw-semibold text-reset">{{ $book->title }}</a>
                                    <div class="small text-muted">{{ $book->isbn ?? 'Tanpa ISBN' }}</div>
                                    <div class="small text-muted">{{ $book->authors_label }}</div>
                                </td>
                                <td>{{ $book->category->name }}</td>
                                <td class="text-center">{{ $book->copies_count }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $book->available_copies_count > 0 ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $book->available_copies_count }}
                                    </span>
                                </td>
                                <td>
                                    <x-status-badge
                                        :label="$book->is_active ? 'Aktif' : 'Diarsipkan'"
                                        :variant="$book->is_active ? 'success' : 'secondary'"
                                        :icon="$book->is_active ? 'check-circle' : 'archive'"
                                    />
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                        @if ($book->is_active)
                                            <a href="{{ route('catalog.show', $book) }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener">
                                                Lihat
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>
                                        <a href="{{ route('admin.books.copies.index', $book) }}" class="btn btn-sm btn-outline-secondary">
                                            Eksemplar
                                        </a>
                                        @if ($book->is_active)
                                            <form method="POST" action="{{ route('admin.books.archive', $book) }}" data-confirm="Arsipkan buku &quot;{{ $book->title }}&quot; dari katalog publik?">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Arsipkan</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.books.activate', $book) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">Aktifkan</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">
                    Menampilkan {{ $books->firstItem() }}&ndash;{{ $books->lastItem() }} dari {{ $books->total() }} buku
                </span>
                {{ $books->links() }}
            </div>
        @endif
    </div>
@endsection
