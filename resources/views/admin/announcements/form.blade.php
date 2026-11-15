@extends('layouts.admin')

@section('title', $announcement->exists ? 'Edit Pengumuman' : 'Tambah Pengumuman')
@section('breadcrumb', 'Pengumuman / Form')

@section('content')
    <x-page-header
        :title="$announcement->exists ? 'Edit Pengumuman' : 'Tambah Pengumuman'"
        :subtitle="$announcement->exists ? 'Perbarui isi, audiens, atau jadwal terbit pengumuman.' : 'Buat informasi baru untuk dashboard guru dan siswa.'"
    >
        <x-slot:actions>
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
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

    <form method="POST" action="{{ $announcement->exists ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}" data-loading>
        @csrf
        @method($announcement->exists ? 'PUT' : 'POST')

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Isi Pengumuman</h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title', $announcement->title) }}" class="form-control @error('title') is-invalid @enderror" maxlength="150" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Isi pengumuman <span class="text-danger">*</span></label>
                            <textarea id="body" name="body" rows="8" class="form-control @error('body') is-invalid @enderror" maxlength="5000" required>{{ old('body', $announcement->body) }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Gunakan teks biasa. Baris baru akan ditampilkan apa adanya; HTML tidak dijalankan.</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="audience" class="form-label">Audiens <span class="text-danger">*</span></label>
                                <select id="audience" name="audience" class="form-select @error('audience') is-invalid @enderror" required>
                                    @foreach ($audiences as $audienceOption)
                                        <option value="{{ $audienceOption->value }}" @selected(old('audience', $announcement->audience?->value) === $audienceOption->value)>{{ $audienceOption->label() }}</option>
                                    @endforeach
                                </select>
                                @error('audience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="published_at" class="form-label">Tanggal terbit</label>
                                <input type="datetime-local" id="published_at" name="published_at" value="{{ old('published_at', $announcement->published_at?->format('Y-m-d\TH:i')) }}" class="form-control @error('published_at') is-invalid @enderror">
                                @error('published_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kosongkan untuk menerbitkan segera saat status aktif.</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($announcement->exists)
                    <div class="card mt-3">
                        <div class="card-body small text-muted">
                            Dibuat oleh {{ $announcement->creator?->name ?? '-' }} pada
                            {{ $announcement->created_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}.
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Publikasi</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $announcement->is_active ?? true))>
                            <label for="is_active" class="form-check-label">Aktif</label>
                        </div>
                        <p class="small text-muted">
                            Pengumuman aktif akan tampil di dashboard pengguna sesuai audiens, maksimal tiga pengumuman terbaru.
                            Pengumuman dengan tanggal terbit di masa depan baru tampil setelah tanggal tersebut.
                        </p>

                        <button type="submit" class="btn btn-primary w-100" data-loading-text="Menyimpan...">
                            <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan
                        </button>
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
