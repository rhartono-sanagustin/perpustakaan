@extends('layouts.admin')

@section('title', 'Penerbit')
@section('breadcrumb', 'Penerbit')

@section('content')
    <x-page-header title="Penerbit" subtitle="Kelola data penerbit koleksi perpustakaan.">
        <x-slot:actions>
            <a href="{{ route('admin.publishers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Penerbit
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.publishers.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-8">
                    <label for="q" class="form-label">Cari penerbit</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Nama penerbit">
                    </div>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Cari</button>
                    @if ($search !== '')
                        <a href="{{ route('admin.publishers.index') }}" class="btn btn-outline-secondary" aria-label="Reset pencarian">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Daftar Penerbit</h2>
            <span class="small text-muted">{{ $publishers->total() }} penerbit</span>
        </div>

        @if ($publishers->isEmpty())
            <div class="card-body">
                <x-empty-state icon="building" title="Belum ada penerbit" message="Tambahkan penerbit agar dapat dihubungkan dengan buku.">
                    <a href="{{ route('admin.publishers.create') }}" class="btn btn-primary btn-sm">Tambah Penerbit</a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Kontak</th>
                            <th scope="col" class="text-center">Jumlah Buku</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($publishers as $publisher)
                            <tr>
                                <td class="fw-semibold">{{ $publisher->name }}</td>
                                <td class="small text-muted">
                                    <div>{{ $publisher->address ?: '-' }}</div>
                                    <div>{{ $publisher->phone ?: '' }}{{ $publisher->phone && $publisher->email ? ' · ' : '' }}{{ $publisher->email ?: '' }}</div>
                                </td>
                                <td class="text-center">{{ $publisher->books_count }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.publishers.edit', $publisher) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        @if ($publisher->books_count === 0)
                                            <form method="POST" action="{{ route('admin.publishers.destroy', $publisher) }}" data-confirm="Hapus penerbit {{ $publisher->name }}?">
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
                    Menampilkan {{ $publishers->firstItem() }}&ndash;{{ $publishers->lastItem() }} dari {{ $publishers->total() }} penerbit
                </span>
                {{ $publishers->links() }}
            </div>
        @endif
    </div>
@endsection
