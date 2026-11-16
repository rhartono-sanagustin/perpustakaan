@extends('layouts.admin')

@section('title', 'Kartu Anggota')
@section('breadcrumb', 'Manajemen / Kartu Anggota')

@section('content')
    <x-page-header
        title="Kartu Anggota"
        subtitle="Cetak kartu anggota guru dan siswa beserta barcode nomor induk untuk pemindaian kunjungan."
    >
        <x-slot:actions>
            @if ($users->total() > 0)
                <a href="{{ route('admin.member-cards.print', request()->query()) }}" target="_blank" rel="noopener" class="btn btn-primary">
                    <i class="bi bi-printer me-1" aria-hidden="true"></i>Cetak Massal
                </a>
            @endif
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-people me-1" aria-hidden="true"></i>Kelola Pengguna
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="alert alert-info d-flex gap-2" role="alert">
        <i class="bi bi-info-circle mt-1" aria-hidden="true"></i>
        <div>
            Kartu hanya dapat dicetak untuk akun guru atau siswa yang aktif dan memiliki nomor induk.
            Setiap lembar A4 memuat hingga 8 kartu (2 &times; 4).
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.member-cards.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-lg-4">
                    <label for="q" class="form-label">Cari anggota</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Nama, nomor induk, atau email">
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <label for="role" class="form-label">Peran</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">Semua peran</option>
                        @foreach ($roles as $roleOption)
                            <option value="{{ $roleOption->value }}" @selected($role === $roleOption)>{{ $roleOption->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-lg-3">
                    <label for="kelas" class="form-label">Kelas / Unit kerja</label>
                    <input type="text" id="kelas" name="kelas" value="{{ $kelas }}" class="form-control" list="kelas-options" placeholder="Contoh: XI IPA 1">
                    <datalist id="kelas-options">
                        @foreach ($classes->merge($units)->unique() as $option)
                            <option value="{{ $option }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="col-12 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                    @if ($search !== '' || $role !== null || $kelas !== '')
                        <a href="{{ route('admin.member-cards.index') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Anggota yang Dapat Dicetak</h2>
            <span class="small text-muted">{{ $users->total() }} anggota</span>
        </div>

        @if ($users->isEmpty())
            <div class="card-body">
                <x-empty-state icon="person-vcard" title="Tidak ada anggota" message="Tidak ada akun guru atau siswa aktif yang sesuai dengan filter saat ini.">
                    <a href="{{ route('admin.member-cards.index') }}" class="btn btn-outline-primary btn-sm">Reset Filter</a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Nomor Induk</th>
                            <th scope="col">Peran</th>
                            <th scope="col">Kelas / Unit</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="small text-muted">{{ $user->email ?? 'Email belum diisi' }}</div>
                                </td>
                                <td>{{ $user->nomor_induk }}</td>
                                <td>
                                    <span class="badge text-bg-light border">{{ $user->roleLabel() }}</span>
                                </td>
                                <td class="small text-muted">{{ $user->kelas ?? $user->unit_kerja ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.card', $user) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-printer me-1" aria-hidden="true"></i>Cetak
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">Menampilkan {{ $users->firstItem() }}&ndash;{{ $users->lastItem() }} dari {{ $users->total() }} anggota</span>
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
