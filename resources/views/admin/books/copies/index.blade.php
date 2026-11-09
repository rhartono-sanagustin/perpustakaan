@extends('layouts.admin')

@section('title', 'Eksemplar Buku')
@section('breadcrumb', 'Buku / Eksemplar')

@section('content')
    <x-page-header title="Eksemplar Buku" :subtitle="$book->title">
        <x-slot:actions>
            <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke Buku
            </a>
            <a href="{{ route('admin.books.copies.create', $book) }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Eksemplar
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="row row-cols-2 row-cols-md-5 g-2 mb-4">
        @foreach ($statuses as $copyStatus)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold">{{ $summary[$copyStatus->value] ?? 0 }}</div>
                        <div class="small text-muted">{{ $copyStatus->label() }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.books.copies.index', $book) }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label for="q" class="form-label">Cari eksemplar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Kode inventaris atau lokasi rak">
                    </div>
                </div>

                <div class="col-8 col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $copyStatus)
                            <option value="{{ $copyStatus->value }}" @selected($status === $copyStatus->value)>{{ $copyStatus->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-4 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    @if ($search !== '' || $status !== '')
                        <a href="{{ route('admin.books.copies.index', $book) }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Daftar Eksemplar</h2>
            <span class="small text-muted">{{ $copies->total() }} eksemplar</span>
        </div>

        @if ($copies->isEmpty())
            <div class="card-body">
                <x-empty-state icon="collection" title="Belum ada eksemplar" message="Tambahkan eksemplar fisik agar buku dapat dipinjam.">
                    <a href="{{ route('admin.books.copies.create', $book) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Eksemplar
                    </a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Kode Inventaris</th>
                            <th scope="col">Lokasi Rak</th>
                            <th scope="col">Kondisi</th>
                            <th scope="col">Status</th>
                            <th scope="col">Pinjaman Aktif</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($copies as $copy)
                            <tr>
                                <td class="fw-semibold">{{ $copy->inventory_code }}</td>
                                <td>{{ $copy->shelf_location ?? '-' }}</td>
                                <td>
                                    <x-status-badge :label="$copy->condition->label()" :variant="$copy->condition->badgeVariant()" />
                                </td>
                                <td>
                                    <x-status-badge :label="$copy->status->label()" :variant="$copy->status->badgeVariant()" />
                                </td>
                                <td>
                                    @if ($copy->activeLoan)
                                        <div class="small fw-semibold">{{ $copy->activeLoan->user->name }}</div>
                                        <div class="small text-muted">Jatuh tempo {{ $copy->activeLoan->due_at->locale('id')->translatedFormat('d M Y') }}</div>
                                    @else
                                        <span class="small text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.books.copies.edit', [$book, $copy]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.books.copies.destroy', [$book, $copy]) }}" data-confirm="Hapus atau nonaktifkan eksemplar {{ $copy->inventory_code }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">
                    Menampilkan {{ $copies->firstItem() }}&ndash;{{ $copies->lastItem() }} dari {{ $copies->total() }} eksemplar
                </span>
                {{ $copies->links() }}
            </div>
        @endif
    </div>
@endsection
