@extends('layouts.borrower')

@section('title', 'Profil')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Profil</h1>
            <p class="text-muted mb-0">Perbarui data kontak dan kata sandi akun Anda.</p>
        </div>
        <a href="{{ route('borrower.loans.index') }}" class="btn btn-outline-primary align-self-md-start">
            <i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i>Pinjaman Saya
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Periksa kembali data berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">Identitas Akun</h2>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="kpi-icon text-bg-primary">
                            <i class="bi bi-person-circle" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate">{{ $user->name }}</div>
                            <div class="small text-muted">{{ $user->roleLabel() }}</div>
                        </div>
                    </div>

                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted">Nomor induk</dt>
                        <dd class="col-7 text-end">{{ $user->nomor_induk }}</dd>

                        @if ($user->kelas)
                            <dt class="col-5 text-muted">Kelas</dt>
                            <dd class="col-7 text-end">{{ $user->kelas }}</dd>
                        @endif

                        @if ($user->unit_kerja)
                            <dt class="col-5 text-muted">Unit kerja</dt>
                            <dd class="col-7 text-end">{{ $user->unit_kerja }}</dd>
                        @endif

                        <dt class="col-5 text-muted">Status akun</dt>
                        <dd class="col-7 text-end">
                            <x-status-badge :label="$user->statusLabel()" :variant="$user->isActive() ? 'success' : 'secondary'" />
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="h6 mb-0">Ketentuan Peminjaman</h2>
                </div>
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-7 text-muted">Kuota terpakai</dt>
                        <dd class="col-5 text-end">{{ $quota['used'] }} / {{ $quota['max'] }}</dd>

                        <dt class="col-7 text-muted">Sisa kuota</dt>
                        <dd class="col-5 text-end">{{ $quota['remaining'] }} eksemplar</dd>

                        <dt class="col-7 text-muted">Durasi pinjam</dt>
                        <dd class="col-5 text-end">{{ $quota['duration_days'] }} hari</dd>

                        <dt class="col-7 text-muted">Batas pengambilan</dt>
                        <dd class="col-5 text-end">{{ $quota['pickup_deadline_days'] }} hari</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="h6 mb-0">Data Profil</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('borrower.profile.update') }}" data-loading>
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">Nama lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" maxlength="100" required autocomplete="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" maxlength="255" autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Boleh dikosongkan jika tidak memiliki email.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="no_telepon" class="form-label">Nomor telepon</label>
                                <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" class="form-control @error('no_telepon') is-invalid @enderror" maxlength="20" autocomplete="tel">
                                @error('no_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h2 class="h6">Ganti Kata Sandi</h2>
                        <p class="small text-muted">Kosongkan bagian ini jika tidak ingin mengganti kata sandi.</p>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="current_password" class="form-label">Kata sandi saat ini</label>
                                <div class="input-group">
                                    <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                                    <button type="button" class="btn btn-outline-secondary" data-toggle-password="#current_password" aria-label="Tampilkan kata sandi saat ini">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="password" class="form-label">Kata sandi baru</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary" data-toggle-password="#password" aria-label="Tampilkan kata sandi baru">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Minimal 8 karakter.</div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="password_confirmation" class="form-label">Ulangi kata sandi baru</label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary" data-toggle-password="#password_confirmation" aria-label="Tampilkan konfirmasi kata sandi">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" data-loading-text="Menyimpan...">
                                <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan Profil
                            </button>
                            <a href="{{ route('borrower.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
