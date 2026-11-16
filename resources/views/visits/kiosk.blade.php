@extends('layouts.kiosk')
@inject('identity', 'App\Services\LibraryIdentity')

@section('title', 'Kiosk Kunjungan')

@section('content')
    <div class="kiosk-card">
        @if ($errors->any())
            <div class="alert alert-danger kiosk-result" role="alert">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-exclamation-circle kiosk-result-icon" aria-hidden="true"></i>
                    <div>
                        <div class="kiosk-result-title">Data Belum Lengkap</div>
                        <div class="mt-1">{{ $errors->first('nomor_induk') }}</div>
                    </div>
                </div>
            </div>
        @endif

        @if ($visitResult)
            <div
                class="alert alert-{{ $visitResult['variant'] }} kiosk-result"
                role="{{ $visitResult['variant'] === 'danger' ? 'alert' : 'status' }}"
                data-kiosk-result
            >
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-{{ $visitResult['icon'] }} kiosk-result-icon" aria-hidden="true"></i>
                    <div class="min-w-0">
                        <div class="kiosk-result-title">{{ $visitResult['title'] }}</div>

                        @if ($visitResult['name'])
                            <div class="kiosk-result-name">{{ $visitResult['name'] }}</div>
                            <div class="kiosk-result-meta">
                                {{ $visitResult['nomor_induk'] }}
                                @if ($visitResult['role'])
                                    <span aria-hidden="true">&middot;</span> {{ $visitResult['role'] }}
                                @endif
                                @if ($visitResult['unit'])
                                    <span aria-hidden="true">&middot;</span> {{ $visitResult['unit'] }}
                                @endif
                                @if ($visitResult['time'])
                                    <span aria-hidden="true">&middot;</span> Pukul {{ $visitResult['time'] }}
                                @endif
                            </div>
                        @endif

                        <div class="mt-2">{{ $visitResult['message'] }}</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card kiosk-scan-card">
            <div class="card-body">
                <div class="text-center mb-4">
                    <span class="kiosk-scan-icon" aria-hidden="true">
                        <i class="bi bi-upc-scan"></i>
                    </span>
                    <h1 class="h3 mt-3 mb-2">Selamat Datang di {{ $identity->libraryName() }}</h1>
                    <p class="text-muted mb-0">
                        Scan kartu anggota atau ketik nomor induk untuk mencatat kunjungan.
                    </p>
                </div>

                <form method="POST" action="{{ route('visits.kiosk.store') }}" data-loading>
                    @csrf

                    <label for="nomor_induk" class="form-label">Nomor induk</label>
                    <div class="input-group input-group-lg mb-2">
                        <span class="input-group-text" aria-hidden="true">
                            <i class="bi bi-upc-scan"></i>
                        </span>
                        <input
                            type="text"
                            id="nomor_induk"
                            name="nomor_induk"
                            value="{{ old('nomor_induk') }}"
                            class="form-control kiosk-input @error('nomor_induk') is-invalid @enderror"
                            placeholder="Contoh: NIS001"
                            autocomplete="off"
                            autocapitalize="off"
                            spellcheck="false"
                            maxlength="50"
                            autofocus
                            required
                            data-kiosk-input
                        >
                        @error('nomor_induk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text mb-3">
                        Arahkan kursor ke kolom ini lalu scan barcode pada kartu anggota. Scanner USB mengirim Enter otomatis.
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100" data-loading-text="Mencatat...">
                        <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Catat Kunjungan
                    </button>
                </form>

                <div class="kiosk-help border-top mt-4 pt-3 small text-muted">
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <i class="bi bi-clock-history mt-1" aria-hidden="true"></i>
                        <span>Kunjungan yang sama dalam 30 menit tidak dicatat dua kali.</span>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-person-check mt-1" aria-hidden="true"></i>
                        <span>Hanya kartu guru dan siswa aktif yang dapat digunakan.</span>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center small text-muted mt-3 mb-0">
            Butuh bantuan? Hubungi petugas perpustakaan.
        </p>
    </div>
@endsection
