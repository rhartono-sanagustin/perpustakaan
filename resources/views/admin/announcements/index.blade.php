@extends('layouts.admin')

@section('title', 'Pengumuman')
@section('breadcrumb', 'Manajemen / Pengumuman')

@section('content')
    <x-page-header
        title="Pengumuman"
        subtitle="Kelola informasi yang tampil di dashboard guru dan siswa."
    >
        <x-slot:actions>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
                <i class="bi bi-megaphone me-1" aria-hidden="true"></i>Tambah Pengumuman
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="row row-cols-2 g-2 mb-4">
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $activeCount }}</div>
                    <div class="small text-muted">Pengumuman aktif</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $draftCount }}</div>
                    <div class="small text-muted">Draf / nonaktif</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.announcements.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-lg-6">
                    <label for="q" class="form-label">Cari pengumuman</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Judul atau isi pengumuman">
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                        <option value="draf" @selected($status === 'draf')>Draf / nonaktif</option>
                    </select>
                </div>

                <div class="col-6 col-lg-2">
                    <label for="audience" class="form-label">Audiens</label>
                    <select id="audience" name="audience" class="form-select">
                        <option value="">Semua audiens</option>
                        @foreach ($audiences as $audienceOption)
                            <option value="{{ $audienceOption->value }}" @selected($audience === $audienceOption)>{{ $audienceOption->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                    @if ($search !== '' || $status !== '' || $audience !== null)
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Daftar Pengumuman</h2>
            <span class="small text-muted">{{ $announcements->total() }} pengumuman</span>
        </div>

        @if ($announcements->isEmpty())
            <div class="card-body">
                <x-empty-state icon="megaphone" title="Belum ada pengumuman" message="Pengumuman yang dibuat akan tampil di dashboard guru dan siswa.">
                    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm">Tambah Pengumuman</a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Judul / Isi</th>
                            <th scope="col">Audiens</th>
                            <th scope="col">Status</th>
                            <th scope="col">Terbit</th>
                            <th scope="col">Pembuat</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($announcements as $announcement)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $announcement->title }}</div>
                                    <div class="small text-muted">{{ \Illuminate\Support\Str::limit($announcement->body, 120) }}</div>
                                </td>
                                <td>
                                    <x-status-badge :label="$announcement->audience->label()" :variant="$announcement->audience->badgeVariant()" />
                                </td>
                                <td>
                                    @if (! $announcement->is_active)
                                        <x-status-badge label="Draf" variant="secondary" />
                                    @elseif ($announcement->published_at?->isFuture())
                                        <x-status-badge label="Terjadwal" variant="info" />
                                    @else
                                        <x-status-badge label="Aktif" variant="success" />
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $announcement->published_at?->locale('id')->translatedFormat('d M Y, H:i') ?? 'Belum dijadwalkan' }}
                                </td>
                                <td class="small text-muted">{{ $announcement->creator?->name ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-primary" title="Edit pengumuman">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                            <span class="visually-hidden">Edit {{ $announcement->title }}</span>
                                        </a>

                                        <form method="POST" action="{{ route('admin.announcements.toggle', $announcement) }}" data-confirm="{{ $announcement->is_active ? 'Nonaktifkan' : 'Aktifkan' }} pengumuman {{ $announcement->title }}?" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ $announcement->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="bi bi-{{ $announcement->is_active ? 'pause-circle' : 'play-circle' }}" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ $announcement->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $announcement->title }}</span>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" data-confirm="Hapus pengumuman {{ $announcement->title }}? Tindakan ini tidak dapat dibatalkan." class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus pengumuman">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                                <span class="visually-hidden">Hapus {{ $announcement->title }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">Menampilkan {{ $announcements->firstItem() }}&ndash;{{ $announcements->lastItem() }} dari {{ $announcements->total() }} pengumuman</span>
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
@endsection
