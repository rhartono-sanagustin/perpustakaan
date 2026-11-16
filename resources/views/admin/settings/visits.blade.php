@extends('layouts.admin')

@section('title', 'Pengaturan Kunjungan')
@section('breadcrumb', 'Manajemen / Pengaturan / Kunjungan')

@section('content')
    <x-page-header
        title="Pengaturan Kunjungan"
        subtitle="Atur apakah pengunjung dapat mencatat kunjungan sendiri melalui kiosk publik."
    />

    @include('admin.settings._nav')

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <form method="POST" action="{{ route('admin.settings.visits.update') }}" data-loading>
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Kiosk Kunjungan Mandiri</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-2">
                            <input
                                type="checkbox"
                                role="switch"
                                id="visit_kiosk_enabled"
                                name="visit_kiosk_enabled"
                                value="1"
                                class="form-check-input @error('visit_kiosk_enabled') is-invalid @enderror"
                                @checked(old('visit_kiosk_enabled', $enabled))
                            >
                            <label for="visit_kiosk_enabled" class="form-check-label fw-semibold">
                                Aktifkan kiosk kunjungan mandiri
                            </label>
                            @error('visit_kiosk_enabled')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <p class="text-muted small mb-3">
                            Saat aktif, pengunjung dapat membuka halaman kiosk tanpa login, memindai barcode kartu
                            anggota atau mengetik nomor induk, lalu melihat konfirmasi nama, kelas/unit, dan waktu
                            kunjungan. Kunjungan tercatat sebagai <strong>Mandiri</strong>.
                        </p>

                        <div class="alert alert-light border mb-0" role="status">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle mt-1" aria-hidden="true"></i>
                                <div class="small">
                                    <div class="fw-semibold">Hanya guru dan siswa aktif yang dapat mencatat kunjungan.</div>
                                    <div class="text-muted">
                                        Scan berulang dalam 30 menit dianggap kunjungan yang sama. Akun nonaktif dan
                                        akun admin akan ditolak. Halaman kiosk tidak diindeks mesin pencari dan tidak disimpan di cache.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div class="small text-muted">
                            Perubahan langsung berlaku pada tautan beranda dan akses halaman kiosk.
                        </div>
                        <button type="submit" class="btn btn-primary" data-loading-text="Menyimpan...">
                            <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="h6 mb-0">Status Kiosk</h2>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <x-status-badge
                            :label="$enabled ? 'Aktif' : 'Nonaktif'"
                            :variant="$enabled ? 'success' : 'secondary'"
                            :icon="$enabled ? 'check-circle' : 'pause-circle'"
                        />
                    </div>

                    <p class="small text-muted">
                        Alamat halaman kiosk:
                        <code class="d-block text-break text-body">{{ route('visits.kiosk') }}</code>
                    </p>

                    @if ($enabled)
                        <a href="{{ route('visits.kiosk') }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Buka Halaman Kiosk
                        </a>
                    @else
                        <p class="small text-muted mb-0">
                            Aktifkan kiosk untuk membuka halaman dan menampilkan tautan di beranda.
                        </p>
                    @endif

                    @if ($setting)
                        <hr>
                        <div class="small text-muted">
                            Terakhir diperbarui
                            {{ $setting->updated_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}
                            @if ($setting->editor)
                                oleh {{ $setting->editor->name }}
                            @endif
                        </div>
                    @else
                        <hr>
                        <div class="small text-muted">Masih menggunakan nilai awal (aktif).</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
