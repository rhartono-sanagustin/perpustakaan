@extends('layouts.admin')

@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('breadcrumb', 'Kategori / Form')

@section('content')
    <x-page-header
        :title="$category->exists ? 'Edit Kategori' : 'Tambah Kategori'"
        :subtitle="$category->exists ? 'Perbarui nama dan deskripsi kategori.' : 'Buat kategori baru untuk koleksi perpustakaan.'"
    >
        <x-slot:actions>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" data-loading>
        @csrf
        @method($category->exists ? 'PUT' : 'POST')

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Data Kategori</h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama kategori <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="form-control @error('name') is-invalid @enderror" maxlength="100" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($category->exists)
                                <div class="form-text">Slug saat ini: <code>{{ $category->slug }}</code></div>
                            @endif
                        </div>

                        <div>
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea id="description" name="description" rows="5" class="form-control @error('description') is-invalid @enderror" maxlength="1000">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Status</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $category->is_active ?? true))>
                            <label for="is_active" class="form-check-label">Aktif</label>
                        </div>
                        <div class="form-text">Kategori nonaktif tidak muncul sebagai pilihan filter katalog publik.</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100" data-loading-text="Menyimpan...">
                            <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
