@extends('layouts.admin')

@section('title', 'Eksemplar / Inventaris')
@section('breadcrumb', 'Eksemplar')

@section('content')
    <x-page-header title="Eksemplar / Inventaris" subtitle="Pantau seluruh eksemplar fisik dan statusnya.">
        <x-slot:actions>
            <a href="{{ route('admin.books.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-journal-bookmark me-1" aria-hidden="true"></i>Pilih Buku
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
            <form method="GET" action="{{ route('admin.copies.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label for="q" class="form-label">Cari eksemplar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Kode inventaris, judul buku, atau rak">
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
                        <a href="{{ route('admin.copies.index') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
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
                <x-empty-state icon="collection" title="Belum ada eksemplar" message="Tambahkan eksemplar melalui halaman daftar buku.">
                    <a href="{{ route('admin.books.index') }}" class="btn btn-primary btn-sm">Buka Daftar Buku</a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Kode Inventaris</th>
                            <th scope="col">Buku</th>
                            <th scope="col">Rak</th>
                            <th scope="col">Kondisi</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($copies as $copy)
                            <tr>
                                <td class="fw-semibold">{{ $copy->inventory_code }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $copy->book->title }}</div>
                                    <div class="small text-muted">{{ $copy->book->category?->name }}</div>
                                </td>
                                <td>{{ $copy->shelf_location ?? '-' }}</td>
                                <td>
                                    <x-status-badge :label="$copy->condition->label()" :variant="$copy->condition->badgeVariant()" />
                                </td>
                                <td>
                                    <x-status-badge :label="$copy->status->label()" :variant="$copy->status->badgeVariant()" />
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.books.copies.index', $copy->book) }}" class="btn btn-sm btn-outline-primary">
                                        Kelola
                                    </a>
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
