@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Masuk ke Perpustakaan</h1>
        <p class="text-muted mb-0">Gunakan email atau nomor induk yang terdaftar.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="d-flex gap-2">
                <i class="bi bi-exclamation-triangle-fill mt-1" aria-hidden="true"></i>
                <div>
                    <strong>Periksa kembali data Anda.</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" data-loading>
        @csrf

        <div class="mb-3">
            <label for="login" class="form-label">Email atau Nomor Induk</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person" aria-hidden="true"></i></span>
                <input
                    id="login"
                    name="login"
                    type="text"
                    class="form-control @error('login') is-invalid @enderror"
                    value="{{ old('login') }}"
                    placeholder="nama@sekolah.sch.id / NIS"
                    required
                    autofocus
                    autocomplete="username"
                    aria-describedby="login-help"
                >
            </div>
            @error('login')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <div id="login-help" class="form-text">Gunakan akun yang diberikan oleh admin perpustakaan.</div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock" aria-hidden="true"></i></span>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Masukkan kata sandi"
                    required
                    autocomplete="current-password"
                >
                <button
                    class="btn btn-outline-secondary"
                    type="button"
                    data-toggle-password="#password"
                    aria-label="Tampilkan kata sandi"
                >
                    <i class="bi bi-eye" aria-hidden="true"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))>
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <span class="small text-muted">Lupa kata sandi? Hubungi admin.</span>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2" data-loading-text="Memproses...">
            <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>Masuk
        </button>
    </form>

    <hr class="my-4">

    <p class="small text-muted text-center mb-0">
        Belum punya akun? Hubungi admin perpustakaan untuk pendaftaran.
    </p>
@endsection
