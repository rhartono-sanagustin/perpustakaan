@extends('layouts.admin')

@section('title', 'Kategori')
@section('breadcrumb', 'Kategori')

@section('content')
    <x-page-header title="Kategori" subtitle="Kelola kategori koleksi yang digunakan pada katalog.">
        <x-slot:actions>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Kategori
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label for="q" class="form-label">Cari kategori</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Nama kategori">
                    </div>
                </div>

                <div class="col-8 col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                    </select>
                </div>

                <div class="col-4 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    @if ($search !== '' || $status !== '')
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Daftar Kategori</h2>
            <span class="small text-muted">{{ $categories->total() }} kategori</span>
        </div>

        @if ($categories->isEmpty())
            <div class="card-body">
                <x-empty-state icon="tags" title="Belum ada kategori" message="Tambahkan kategori untuk mengelompokkan koleksi.">
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">Tambah Kategori</a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Slug</th>
                            <th scope="col" class="text-center">Jumlah Buku</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $category->name }}</div>
                                    <div class="small text-muted">{{ $category->description ?: 'Tanpa deskripsi' }}</div>
                                </td>
                                <td><code>{{ $category->slug }}</code></td>
                                <td class="text-center">{{ $category->books_count }}</td>
                                <td>
                                    <x-status-badge
                                        :label="$category->is_active ? 'Aktif' : 'Nonaktif'"
                                        :variant="$category->is_active ? 'success' : 'secondary'"
                                        :icon="$category->is_active ? 'check-circle' : 'pause-circle'"
                                    />
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.categories.toggle', $category) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        @if ($category->books_count === 0)
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="Hapus kategori {{ $category->name }}?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
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
                    Menampilkan {{ $categories->firstItem() }}&ndash;{{ $categories->lastItem() }} dari {{ $categories->total() }} kategori
                </span>
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
