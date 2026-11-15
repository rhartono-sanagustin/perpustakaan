@extends('layouts.admin')

@section('title', 'Identitas Sekolah')
@section('breadcrumb', 'Manajemen / Pengaturan / Identitas Sekolah')

@section('content')
    <x-page-header
        title="Identitas Sekolah"
        subtitle="Atur nama dan logo yang tampil di sidebar, halaman login, dan halaman publik."
    />

    @include('admin.settings._nav')

    <div class="alert alert-info d-flex align-items-start gap-2" role="status">
        <i class="bi bi-info-circle mt-1" aria-hidden="true"></i>
        <div>
            <div class="fw-semibold">Perubahan langsung tampil</div>
            <div class="small">Nama sekolah, nama perpustakaan, tagline, dan logo dipakai di seluruh halaman aplikasi.</div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.identity.update') }}" enctype="multipart/form-data" data-loading>
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-12 col-xl-7">
                <div class="card h-100">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Nama &amp; Tagline</h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="school_name" class="form-label">Nama sekolah <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="school_name"
                                name="school_name"
                                value="{{ old('school_name', $identity->schoolName()) }}"
                                class="form-control @error('school_name') is-invalid @enderror"
                                maxlength="{{ config('library.identity_definitions.school_name.max', 120) }}"
                                required
                            >
                            @error('school_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ config('library.identity_definitions.school_name.description') }}</div>
                            @include('admin.settings._updated-at', ['settingKey' => 'school_name'])
                        </div>

                        <div class="mb-4">
                            <label for="library_name" class="form-label">Nama perpustakaan <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="library_name"
                                name="library_name"
                                value="{{ old('library_name', $identity->libraryName()) }}"
                                class="form-control @error('library_name') is-invalid @enderror"
                                maxlength="{{ config('library.identity_definitions.library_name.max', 100) }}"
                                required
                            >
                            @error('library_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ config('library.identity_definitions.library_name.description') }}</div>
                            @include('admin.settings._updated-at', ['settingKey' => 'library_name'])
                        </div>

                        <div>
                            <label for="tagline" class="form-label">Tagline</label>
                            <input
                                type="text"
                                id="tagline"
                                name="tagline"
                                value="{{ old('tagline', $identity->tagline()) }}"
                                class="form-control @error('tagline') is-invalid @enderror"
                                maxlength="{{ config('library.identity_definitions.tagline.max', 160) }}"
                            >
                            @error('tagline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ config('library.identity_definitions.tagline.description') }}</div>
                            @include('admin.settings._updated-at', ['settingKey' => 'tagline'])
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Logo Sekolah</h2>
                    </div>
                    <div class="card-body">
                        <div class="border bg-white d-flex align-items-center justify-content-center p-3 mb-3" style="min-height: 160px;">
                            <img
                                src="{{ $identity->logoUrl() }}"
                                alt="Pratinjau logo {{ $identity->schoolName() }}"
                                class="img-fluid"
                                style="max-height: 120px; max-width: 100%; object-fit: contain;"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Ganti logo</label>
                            <input
                                type="file"
                                id="logo"
                                name="logo"
                                class="form-control @error('logo') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            >
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">JPG, PNG, atau WEBP. Maksimal 1 MB. Disarankan gambar persegi atau transparan.</div>
                        </div>

                        @if ($identity->hasCustomLogo())
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    id="remove_logo"
                                    name="remove_logo"
                                    value="1"
                                    class="form-check-input"
                                    @checked(old('remove_logo'))
                                >
                                <label for="remove_logo" class="form-check-label">Hapus logo kustom dan gunakan logo bawaan</label>
                            </div>
                        @endif

                        @include('admin.settings._updated-at', ['settingKey' => 'logo_path'])
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div class="small text-muted">
                    Perubahan identitas juga tercatat pada audit log.
                </div>
                <button type="submit" class="btn btn-primary" data-loading-text="Menyimpan...">
                    <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan Identitas
                </button>
            </div>
        </div>
    </form>
@endsection
