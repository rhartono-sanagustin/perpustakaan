@extends('layouts.admin')

@section('title', 'Carousel Beranda')
@section('breadcrumb', 'Manajemen / Pengaturan / Carousel Beranda')

@section('content')
    <x-page-header
        title="Carousel Beranda"
        subtitle="Kelola gambar yang tampil pada carousel di landing page."
    >
        <x-slot:actions>
            <a href="{{ route('admin.settings.carousel.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Slide
            </a>
        </x-slot:actions>
    </x-page-header>

    @include('admin.settings._nav')

    <div class="alert alert-info d-flex align-items-start gap-2" role="status">
        <i class="bi bi-info-circle mt-1" aria-hidden="true"></i>
        <div>
            <div class="fw-semibold">{{ $activeCount }} dari {{ $maxActive }} slide aktif</div>
            <div class="small">Slide nonaktif tetap tersimpan, tetapi tidak tampil di beranda. Urutan slide dapat diubah dengan tombol naik/turun.</div>
        </div>
    </div>

    @if ($slides->isEmpty())
        <x-empty-state
            icon="images"
            title="Belum ada slide carousel"
            message="Tambahkan gambar untuk ditampilkan di landing page."
        >
            <a href="{{ route('admin.settings.carousel.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Slide
            </a>
        </x-empty-state>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" style="width: 80px;">Urutan</th>
                            <th scope="col" style="width: 150px;">Gambar</th>
                            <th scope="col">Informasi Slide</th>
                            <th scope="col" style="width: 110px;">Status</th>
                            <th scope="col" class="text-end" style="width: 320px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($slides as $slide)
                            <tr>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="fw-semibold">{{ $loop->iteration }}</span>
                                        <div class="btn-group btn-group-sm" role="group" aria-label="Ubah urutan slide">
                                            <form method="POST" action="{{ route('admin.settings.carousel.move', $slide) }}">
                                                @csrf
                                                <input type="hidden" name="direction" value="up">
                                                <button type="submit" class="btn btn-outline-secondary" aria-label="Naikkan slide {{ $loop->iteration }}" @disabled($loop->first)>
                                                    <i class="bi bi-arrow-up" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.settings.carousel.move', $slide) }}">
                                                @csrf
                                                <input type="hidden" name="direction" value="down">
                                                <button type="submit" class="btn btn-outline-secondary" aria-label="Turunkan slide {{ $loop->iteration }}" @disabled($loop->last)>
                                                    <i class="bi bi-arrow-down" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <img
                                        src="{{ $slide->imageUrl() }}"
                                        alt=""
                                        class="border"
                                        style="width: 130px; height: 73px; object-fit: cover;"
                                    >
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $slide->title ?: 'Tanpa judul' }}</div>
                                    @if ($slide->caption)
                                        <div class="small text-muted text-truncate" style="max-width: 420px;">{{ $slide->caption }}</div>
                                    @endif
                                    @if ($slide->link_url)
                                        <div class="small text-muted text-truncate" style="max-width: 420px;">
                                            <i class="bi bi-link-45deg" aria-hidden="true"></i>{{ $slide->link_url }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($slide->is_active)
                                        <span class="badge text-bg-success">Aktif</span>
                                    @else
                                        <span class="badge text-bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap justify-content-end gap-1">
                                        <a href="{{ route('admin.settings.carousel.edit', $slide) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil me-1" aria-hidden="true"></i>Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.settings.carousel.toggle', $slide) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                @if ($slide->is_active)
                                                    <i class="bi bi-pause-circle me-1" aria-hidden="true"></i>Nonaktifkan
                                                @else
                                                    <i class="bi bi-play-circle me-1" aria-hidden="true"></i>Aktifkan
                                                @endif
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.settings.carousel.destroy', $slide) }}" data-confirm="Hapus slide &quot;{{ $slide->title ?: 'Tanpa judul' }}&quot;? Gambar juga akan dihapus.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash me-1" aria-hidden="true"></i>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
