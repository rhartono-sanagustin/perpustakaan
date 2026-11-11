@extends('layouts.admin')

@section('title', 'Pengguna')
@section('breadcrumb', 'Manajemen / Pengguna')

@section('content')
    <x-page-header
        title="Pengguna"
        subtitle="Kelola akun admin, guru, dan siswa beserta status aksesnya."
    >
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1" aria-hidden="true"></i>Tambah Pengguna
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="row row-cols-2 row-cols-md-4 g-2 mb-4">
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $counts['total'] }}</div>
                    <div class="small text-muted">Total pengguna</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $counts['aktif'] }}</div>
                    <div class="small text-muted">Akun aktif</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $counts['guru'] }}</div>
                    <div class="small text-muted">Guru</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $counts['siswa'] }}</div>
                    <div class="small text-muted">Siswa</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-lg-6">
                    <label for="q" class="form-label">Cari pengguna</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Nama, email, nomor induk, kelas, atau unit kerja">
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

                <div class="col-6 col-lg-2">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption->value }}" @selected($status === $statusOption)>{{ $statusOption->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Terapkan</button>
                    @if ($search !== '' || $role !== null || $status !== null)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" aria-label="Reset filter">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Daftar Pengguna</h2>
            <span class="small text-muted">{{ $users->total() }} akun</span>
        </div>

        @if ($users->isEmpty())
            <div class="card-body">
                <x-empty-state icon="people" title="Tidak ada pengguna" message="Tidak ada akun yang sesuai dengan filter saat ini.">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">Reset Filter</a>
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
                            <th scope="col">Status</th>
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
                                <td>
                                    <x-status-badge :label="$user->statusLabel()" :variant="$user->status->badgeVariant()" />
                                </td>
                                <td class="text-end">
                                    @if (auth()->user()->is($user))
                                        <span class="badge text-bg-light border">Akun Anda</span>
                                    @else
                                        <div class="d-flex justify-content-end gap-1">
                                            @can('update', $user)
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit pengguna">
                                                    <i class="bi bi-pencil" aria-hidden="true"></i>
                                                    <span class="visually-hidden">Edit {{ $user->name }}</span>
                                                </a>
                                            @endcan

                                            @can('toggleStatus', $user)
                                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" data-confirm="Ubah status akun {{ $user->name }} menjadi {{ $user->isActive() ? 'nonaktif' : 'aktif' }}?" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ $user->isActive() ? 'Nonaktifkan akun' : 'Aktifkan akun' }}">
                                                        <i class="bi bi-{{ $user->isActive() ? 'person-dash' : 'person-check' }}" aria-hidden="true"></i>
                                                        <span class="visually-hidden">{{ $user->isActive() ? 'Nonaktifkan' : 'Aktifkan' }} {{ $user->name }}</span>
                                                    </button>
                                                </form>
                                            @endcan

                                            @can('delete', $user)
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Hapus pengguna {{ $user->name }}? Akun dengan riwayat transaksi akan dinonaktifkan, bukan dihapus." class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus pengguna">
                                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                                        <span class="visually-hidden">Hapus {{ $user->name }}</span>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">Menampilkan {{ $users->firstItem() }}&ndash;{{ $users->lastItem() }} dari {{ $users->total() }} akun</span>
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
