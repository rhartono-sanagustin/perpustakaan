@extends('layouts.admin')

@section('title', $publisher->exists ? 'Edit Penerbit' : 'Tambah Penerbit')
@section('breadcrumb', 'Penerbit / Form')

@section('content')
    <x-page-header
        :title="$publisher->exists ? 'Edit Penerbit' : 'Tambah Penerbit'"
        :subtitle="$publisher->exists ? 'Perbarui data penerbit.' : 'Tambahkan penerbit baru ke basis data perpustakaan.'"
    >
        <x-slot:actions>
            <a href="{{ route('admin.publishers.index') }}" class="btn btn-outline-secondary">
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

    <form method="POST" action="{{ $publisher->exists ? route('admin.publishers.update', $publisher) : route('admin.publishers.store') }}" data-loading>
        @csrf
        @method($publisher->exists ? 'PUT' : 'POST')

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Data Penerbit</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label">Nama penerbit <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $publisher->name) }}" class="form-control @error('name') is-invalid @enderror" maxlength="150" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Alamat</label>
                                <input type="text" id="address" name="address" value="{{ old('address', $publisher->address) }}" class="form-control @error('address') is-invalid @enderror" maxlength="255">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $publisher->phone) }}" class="form-control @error('phone') is-invalid @enderror" maxlength="30">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $publisher->email) }}" class="form-control @error('email') is-invalid @enderror" maxlength="255">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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
                        <a href="{{ route('admin.publishers.index') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
