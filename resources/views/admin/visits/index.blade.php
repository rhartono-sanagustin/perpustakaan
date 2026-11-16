@extends('layouts.admin')

@section('title', 'Kunjungan')
@section('breadcrumb', 'Transaksi / Kunjungan')

@section('content')
    <x-page-header
        title="Kunjungan Perpustakaan"
        subtitle="Scan kartu anggota untuk mencatat kunjungan guru dan siswa."
    >
        <x-slot:actions>
            <a href="{{ route('admin.member-cards.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-person-vcard me-1" aria-hidden="true"></i>Kartu Anggota
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="row row-cols-2 row-cols-md-4 g-2 mb-4">
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $counts['total'] }}</div>
                    <div class="small text-muted">Kunjungan hari ini</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $counts['unique'] }}</div>
                    <div class="small text-muted">Pengunjung unik</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $counts['guru'] }}</div>
                    <div class="small text-muted">Kunjungan guru</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $counts['siswa'] }}</div>
                    <div class="small text-muted">Kunjungan siswa</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h2 class="h6 mb-0">Scan Kartu Anggota</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.visits.store') }}" class="row g-2 align-items-end" data-loading>
                @csrf

                <div class="col-12 col-lg-8">
                    <label for="nomor_induk" class="form-label">Nomor induk</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-upc-scan" aria-hidden="true"></i></span>
                        <input
                            type="text"
                            id="nomor_induk"
                            name="nomor_induk"
                            value="{{ old('nomor_induk') }}"
                            class="form-control @error('nomor_induk') is-invalid @enderror"
                            placeholder="Scan barcode atau ketik nomor induk"
                            autocomplete="off"
                            maxlength="50"
                            autofocus
                            required
                        >
                        @error('nomor_induk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text">
                        Arahkan kursor ke kolom ini lalu scan barcode pada kartu anggota. Scanner USB mengirim Enter otomatis.
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100" data-loading-text="Mencatat...">
                        <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Catat Kunjungan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($member)
        <div class="card border-primary mb-3">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h2 class="h6 mb-0">Anggota yang Baru Dipindai</h2>
                <span class="badge text-bg-primary">{{ $member->roleLabel() }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-lg-4">
                        <div class="fw-semibold fs-5">{{ $member->name }}</div>
                        <div class="text-muted">{{ $member->nomor_induk }}</div>
                        <div class="small text-muted mt-1">
                            {{ $member->isGuru() ? ($member->unit_kerja ?? 'Unit kerja belum diisi') : ($member->kelas ?? 'Kelas belum diisi') }}
                        </div>
                        <div class="mt-2">
                            <x-status-badge :label="$member->statusLabel()" :variant="$member->status->badgeVariant()" />
                        </div>
                    </div>

                    <div class="col-12 col-lg-8">
                        <div class="row row-cols-2 g-2 mb-3">
                            <div class="col">
                                <div class="border rounded p-2">
                                    <div class="fs-5 fw-bold">{{ $member->loans->count() }}</div>
                                    <div class="small text-muted">Pinjaman aktif</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border rounded p-2">
                                    <div class="fs-5 fw-bold text-danger">{{ $member->loans->filter(fn ($loan) => $loan->isOverdue())->count() }}</div>
                                    <div class="small text-muted">Terlambat</div>
                                </div>
                            </div>
                        </div>

                        @if ($member->loans->isEmpty())
                            <p class="small text-muted mb-0">Tidak ada pinjaman aktif.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Buku</th>
                                            <th scope="col">Jatuh Tempo</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($member->loans as $loan)
                                            <tr>
                                                <td>
                                                    <div>{{ $loan->copy->book->title }}</div>
                                                    <div class="small text-muted">{{ $loan->copy->inventory_code }}</div>
                                                </td>
                                                <td class="small">{{ $loan->due_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}</td>
                                                <td>
                                                    <x-status-badge :label="$loan->statusLabel()" :variant="$loan->isOverdue() ? 'danger' : 'info'" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a href="{{ route('admin.users.card', $member) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-person-vcard me-1" aria-hidden="true"></i>Cetak Kartu
                            </a>
                            <a href="{{ route('admin.loans.index', ['q' => $member->nomor_induk]) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i>Lihat Pinjaman
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="h6 mb-0">Kunjungan Hari Ini</h2>
            <span class="small text-muted">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
        </div>

        @if ($visits->isEmpty())
            <div class="card-body">
                <x-empty-state
                    icon="person-check"
                    title="Belum ada kunjungan"
                    message="Kunjungan yang tercatat hari ini akan tampil di sini."
                />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Waktu</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Nomor Induk</th>
                            <th scope="col">Peran</th>
                            <th scope="col">Kelas / Unit</th>
                            <th scope="col">Dicatat oleh</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visits as $visit)
                            <tr>
                                <td class="fw-semibold">{{ $visit->visited_at->format('H:i') }}</td>
                                <td>{{ $visit->user->name }}</td>
                                <td>{{ $visit->user->nomor_induk }}</td>
                                <td>
                                    <span class="badge text-bg-light border">{{ $visit->user->roleLabel() }}</span>
                                </td>
                                <td class="small text-muted">{{ $visit->user->kelas ?? $visit->user->unit_kerja ?? '-' }}</td>
                                <td class="small text-muted">{{ $visit->recorder?->name ?? '-' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.visits.destroy', $visit) }}" data-confirm="Batalkan kunjungan {{ $visit->user->name }} pukul {{ $visit->visited_at->format('H:i') }}?" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan kunjungan">
                                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                                            <span class="visually-hidden">Batalkan kunjungan {{ $visit->user->name }}</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
