@extends('layouts.admin')

@section('title', $author->exists ? 'Edit Penulis' : 'Tambah Penulis')
@section('breadcrumb', 'Penulis / Form')

@section('content')
    <x-page-header
        :title="$author->exists ? 'Edit Penulis' : 'Tambah Penulis'"
        :subtitle="$author->exists ? 'Perbarui data penulis.' : 'Tambahkan penulis baru ke basis data perpustakaan.'"
    >
        <x-slot:actions>
            <a href="{{ route('admin.authors.index') }}" class="btn btn-outline-secondary">
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

    <form method="POST" action="{{ $author->exists ? route('admin.authors.update', $author) : route('admin.authors.store') }}" data-loading>
        @csrf
        @method($author->exists ? 'PUT' : 'POST')

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Data Penulis</h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama penulis <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $author->name) }}" class="form-control @error('name') is-invalid @enderror" maxlength="150" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="bio" class="form-label">Biografi</label>
                            <textarea id="bio" name="bio" rows="6" class="form-control @error('bio') is-invalid @enderror" maxlength="2000">{{ old('bio', $author->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100" data-loading-text="Menyimpan...">
                            <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan
                        </button>
                        <a href="{{ route('admin.authors.index') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
