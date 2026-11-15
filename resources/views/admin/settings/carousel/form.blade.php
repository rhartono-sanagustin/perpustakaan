@extends('layouts.admin')

@section('title', $slide->exists ? 'Edit Slide Carousel' : 'Tambah Slide Carousel')
@section('breadcrumb', 'Manajemen / Pengaturan / Carousel Beranda / ' . ($slide->exists ? 'Edit' : 'Tambah'))

@section('content')
    <x-page-header
        :title="$slide->exists ? 'Edit Slide Carousel' : 'Tambah Slide Carousel'"
        subtitle="Atur gambar, teks, tautan, dan status tampil slide di landing page."
    />

    @include('admin.settings._nav')

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ $slide->exists ? route('admin.settings.carousel.update', $slide) : route('admin.settings.carousel.store') }}"
        enctype="multipart/form-data"
        data-loading
    >
        @csrf
        @if ($slide->exists)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12 col-xl-7">
                <div class="card h-100">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Gambar Slide</h2>
                    </div>
                    <div class="card-body">
                        @if ($slide->exists)
                            <div class="border bg-white d-flex align-items-center justify-content-center p-3 mb-3">
                                <img
                                    src="{{ $slide->imageUrl() }}"
                                    alt="Pratinjau slide {{ $slide->title ?: 'carousel' }}"
                                    class="img-fluid"
                                    style="max-height: 260px; max-width: 100%; object-fit: contain;"
                                >
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="image" class="form-label">
                                Gambar <span class="text-danger">*</span>
                            </label>
                            <input
                                type="file"
                                id="image"
                                name="image"
                                class="form-control @error('image') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                @required(! $slide->exists)
                            >
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                JPG, PNG, atau WEBP. Maksimal 2 MB. Rasio yang disarankan 16:9, misalnya 1600 &times; 900 piksel.
                                @if ($slide->exists)
                                    Kosongkan jika tidak ingin mengganti gambar.
                                @endif
                            </div>
                        </div>

                        <div class="form-check">
                            <input
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                value="1"
                                class="form-check-input"
                                @checked(old('is_active', $slide->is_active))
                            >
                            <label for="is_active" class="form-check-label">Tampilkan slide di beranda</label>
                        </div>
                        <div class="form-text">Maksimal {{ config('library.carousel.max_active', 5) }} slide dapat aktif pada waktu bersamaan.</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Detail Slide</h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul</label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $slide->title) }}"
                                class="form-control @error('title') is-invalid @enderror"
                                maxlength="150"
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="caption" class="form-label">Keterangan</label>
                            <textarea
                                id="caption"
                                name="caption"
                                rows="3"
                                class="form-control @error('caption') is-invalid @enderror"
                                maxlength="500"
                            >{{ old('caption', $slide->caption) }}</textarea>
                            @error('caption')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="alt_text" class="form-label">Teks alternatif gambar</label>
                            <input
                                type="text"
                                id="alt_text"
                                name="alt_text"
                                value="{{ old('alt_text', $slide->alt_text) }}"
                                class="form-control @error('alt_text') is-invalid @enderror"
                                maxlength="255"
                            >
                            @error('alt_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Dibacakan pembaca layar. Kosongkan jika gambar dekoratif.</div>
                        </div>

                        <div>
                            <label for="link_url" class="form-label">Tautan</label>
                            <input
                                type="text"
                                id="link_url"
                                name="link_url"
                                value="{{ old('link_url', $slide->link_url) }}"
                                class="form-control @error('link_url') is-invalid @enderror"
                                maxlength="2048"
                                placeholder="/katalog atau https://contoh.sch.id"
                            >
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Gunakan path internal yang diawali <code>/</code> atau URL <code>http/https</code>.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <a href="{{ route('admin.settings.carousel.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary" data-loading-text="Menyimpan...">
                    <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan Slide
                </button>
            </div>
        </div>
    </form>
@endsection
