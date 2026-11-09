@extends('layouts.admin')

@section('title', $copy->exists ? 'Edit Eksemplar' : 'Tambah Eksemplar')
@section('breadcrumb', 'Buku / Eksemplar / Form')

@section('content')
    <x-page-header
        :title="$copy->exists ? 'Edit Eksemplar' : 'Tambah Eksemplar'"
        :subtitle="$book->title"
    >
        <x-slot:actions>
            <a href="{{ route('admin.books.copies.index', $book) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Periksa kembali data berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $copy->exists ? route('admin.books.copies.update', [$book, $copy]) : route('admin.books.copies.store', $book) }}" data-loading>
        @csrf
        @method($copy->exists ? 'PUT' : 'POST')

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Data Eksemplar</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="inventory_code" class="form-label">Kode inventaris <span class="text-danger">*</span></label>
                                <input type="text" id="inventory_code" name="inventory_code" value="{{ old('inventory_code', $copy->inventory_code) }}" class="form-control @error('inventory_code') is-invalid @enderror" maxlength="50" required>
                                @error('inventory_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kode harus unik untuk setiap eksemplar fisik.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="shelf_location" class="form-label">Lokasi rak</label>
                                <input type="text" id="shelf_location" name="shelf_location" value="{{ old('shelf_location', $copy->shelf_location) }}" class="form-control @error('shelf_location') is-invalid @enderror" maxlength="100" placeholder="Contoh: Rak A-1">
                                @error('shelf_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="condition" class="form-label">Kondisi <span class="text-danger">*</span></label>
                                <select id="condition" name="condition" class="form-select @error('condition') is-invalid @enderror" required>
                                    @foreach ($conditions as $condition)
                                        <option value="{{ $condition->value }}" @selected(old('condition', $copy->condition?->value) === $condition->value)>{{ $condition->label() }}</option>
                                    @endforeach
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach ($statuses as $copyStatus)
                                        <option value="{{ $copyStatus->value }}" @selected(old('status', $copy->status?->value) === $copyStatus->value)>{{ $copyStatus->label() }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Status Dipinjam hanya diatur otomatis melalui checkout pada fase sirkulasi.</div>
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Catatan</label>
                                <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror" maxlength="1000">{{ old('notes', $copy->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Ringkasan Buku</h2>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <img src="{{ $book->cover_url }}" alt="" class="admin-cover">
                            <div class="min-w-0">
                                <div class="fw-semibold">{{ $book->title }}</div>
                                <div class="small text-muted">{{ $book->authors_label }}</div>
                                <div class="small text-muted">{{ $book->category?->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100" data-loading-text="Menyimpan...">
                            <i class="bi bi-save me-1" aria-hidden="true"></i>{{ $copy->exists ? 'Simpan Perubahan' : 'Simpan Eksemplar' }}
                        </button>
                        <a href="{{ route('admin.books.copies.index', $book) }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
