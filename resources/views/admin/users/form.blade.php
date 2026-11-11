@extends('layouts.admin')

@section('title', $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('breadcrumb', 'Pengguna / Form')

@section('content')
    <x-page-header
        :title="$user->exists ? 'Edit Pengguna' : 'Tambah Pengguna'"
        :subtitle="$user->exists ? 'Perbarui data akun dan hak akses pengguna.' : 'Buat akun baru untuk admin, guru, atau siswa.'"
    >
        <x-slot:actions>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
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

    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" data-loading>
        @csrf
        @method($user->exists ? 'PUT' : 'POST')

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Data Akun</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label">Nama lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" maxlength="255" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="nomor_induk" class="form-label">Nomor induk <span class="text-danger">*</span></label>
                                <input type="text" id="nomor_induk" name="nomor_induk" value="{{ old('nomor_induk', $user->nomor_induk) }}" class="form-control @error('nomor_induk') is-invalid @enderror" maxlength="50" required>
                                @error('nomor_induk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">NIS untuk siswa, NIP untuk guru, atau kode admin.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" maxlength="255">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="role" class="form-label">Peran <span class="text-danger">*</span></label>
                                <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    @foreach ($roles as $roleOption)
                                        <option value="{{ $roleOption->value }}" @selected(old('role', $user->role?->value) === $roleOption->value)>{{ $roleOption->label() }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label">Status akun <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach ($statuses as $statusOption)
                                        <option value="{{ $statusOption->value }}" @selected(old('status', $user->status?->value) === $statusOption->value)>{{ $statusOption->label() }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Akun nonaktif tidak dapat login atau mengajukan peminjaman.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="kelas" class="form-label">Kelas</label>
                                <input type="text" id="kelas" name="kelas" value="{{ old('kelas', $user->kelas) }}" class="form-control @error('kelas') is-invalid @enderror" maxlength="50" placeholder="Contoh: XI IPA 1">
                                @error('kelas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Isi untuk siswa.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="unit_kerja" class="form-label">Unit kerja</label>
                                <input type="text" id="unit_kerja" name="unit_kerja" value="{{ old('unit_kerja', $user->unit_kerja) }}" class="form-control @error('unit_kerja') is-invalid @enderror" maxlength="100" placeholder="Contoh: Matematika">
                                @error('unit_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Isi untuk guru atau admin.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="no_telepon" class="form-label">Nomor telepon</label>
                                <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" class="form-control @error('no_telepon') is-invalid @enderror" maxlength="30" placeholder="08xxxxxxxxxx">
                                @error('no_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @unless ($user->exists)
                                <div class="col-12 col-md-6">
                                    <label for="password" class="form-label">Kata sandi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="8" required autocomplete="new-password">
                                        <button type="button" class="btn btn-outline-secondary" data-toggle-password="#password" aria-label="Tampilkan kata sandi">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Minimal 8 karakter.</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="password_confirmation" class="form-label">Ulangi kata sandi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" minlength="8" required autocomplete="new-password">
                                        <button type="button" class="btn btn-outline-secondary" data-toggle-password="#password_confirmation" aria-label="Tampilkan kata sandi">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            @endunless
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Ringkasan</h2>
                    </div>
                    <div class="card-body">
                        <dl class="row small mb-3">
                            <dt class="col-5 text-muted fw-normal">Peran</dt>
                            <dd class="col-7 mb-2">{{ $user->exists ? $user->roleLabel() : 'Siswa' }}</dd>
                            <dt class="col-5 text-muted fw-normal">Status</dt>
                            <dd class="col-7 mb-2">{{ $user->exists ? $user->statusLabel() : 'Aktif' }}</dd>
                        </dl>

                        <button type="submit" class="btn btn-primary w-100" data-loading-text="Menyimpan...">
                            <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @if ($user->exists && auth()->user()->can('resetPassword', $user))
        <div class="row g-3 mt-1" id="reset-password">
            <div class="col-12 col-lg-8">
                <div class="card border-warning">
                    <div class="card-header bg-white">
                        <h2 class="h6 mb-0">Reset Kata Sandi</h2>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">
                            Masukkan kata sandi baru untuk {{ $user->name }}. Kata sandi tidak akan ditampilkan setelah disimpan.
                        </p>

                        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" data-loading>
                            @csrf

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="new_password" class="form-label">Kata sandi baru <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" id="new_password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="8" required autocomplete="new-password">
                                        <button type="button" class="btn btn-outline-secondary" data-toggle-password="#new_password" aria-label="Tampilkan kata sandi">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="new_password_confirmation" class="form-label">Ulangi kata sandi baru <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" id="new_password_confirmation" name="password_confirmation" class="form-control" minlength="8" required autocomplete="new-password">
                                        <button type="button" class="btn btn-outline-secondary" data-toggle-password="#new_password_confirmation" aria-label="Tampilkan kata sandi">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning mt-3" data-loading-text="Mereset...">
                                <i class="bi bi-key me-1" aria-hidden="true"></i>Reset Kata Sandi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
