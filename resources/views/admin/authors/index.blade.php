@extends('layouts.admin')

@section('title', 'Penulis')
@section('breadcrumb', 'Penulis')

@section('content')
    <x-page-header title="Penulis" subtitle="Kelola data penulis yang terhubung dengan koleksi.">
        <x-slot:actions>
            <a href="{{ route('admin.authors.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Penulis
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.authors.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-8">
                    <label for="q" class="form-label">Cari penulis</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Nama penulis">
                    </div>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Cari</button>
                    @if ($search !== '')
                        <a href="{{ route('admin.authors.index') }}" class="btn btn-outline-secondary" aria-label="Reset pencarian">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Daftar Penulis</h2>
            <span class="small text-muted">{{ $authors->total() }} penulis</span>
        </div>

        @if ($authors->isEmpty())
            <div class="card-body">
                <x-empty-state icon="pen" title="Belum ada penulis" message="Tambahkan penulis agar dapat dihubungkan dengan buku.">
                    <a href="{{ route('admin.authors.create') }}" class="btn btn-primary btn-sm">Tambah Penulis</a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Biografi</th>
                            <th scope="col" class="text-center">Jumlah Buku</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($authors as $author)
                            <tr>
                                <td class="fw-semibold">{{ $author->name }}</td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($author->bio ?: '-', 90) }}</td>
                                <td class="text-center">{{ $author->books_count }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.authors.edit', $author) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        @if ($author->books_count === 0)
                                            <form method="POST" action="{{ route('admin.authors.destroy', $author) }}" data-confirm="Hapus penulis {{ $author->name }}?">
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
                    Menampilkan {{ $authors->firstItem() }}&ndash;{{ $authors->lastItem() }} dari {{ $authors->total() }} penulis
                </span>
                {{ $authors->links() }}
            </div>
        @endif
    </div>
@endsection
