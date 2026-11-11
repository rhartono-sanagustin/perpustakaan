@extends('layouts.borrower')

@section('title', 'Ajukan Peminjaman')

@section('content')
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('borrower.dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Katalog</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.show', $book) }}">Detail Buku</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ajukan Peminjaman</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Ajukan Peminjaman</h1>
            <p class="text-muted mb-0">Periksa ringkasan buku dan ketentuan peminjaman sebelum mengirim pengajuan.</p>
        </div>
        <a href="{{ route('catalog.show', $book) }}" class="btn btn-outline-secondary align-self-md-start">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke Detail Buku
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Pengajuan belum dapat dikirim:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h2 class="h6 mb-0">Ringkasan Buku</h2>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <img src="{{ $book->cover_url }}" alt="Sampul {{ $book->title }}" class="admin-cover" style="width: 88px; height: 120px;">
                        <div class="min-w-0">
                            <h3 class="h5 mb-1">{{ $book->title }}</h3>
                            <p class="text-muted mb-2">{{ $book->authors_label ?: 'Penulis tidak diketahui' }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge text-bg-primary">{{ $book->category?->name ?? 'Tanpa kategori' }}</span>
                                <x-status-badge
                                    :label="$eligibility['available_copies'].' eksemplar tersedia'"
                                    :variant="$eligibility['available_copies'] > 0 ? 'success' : 'secondary'"
                                    :icon="$eligibility['available_copies'] > 0 ? 'check-circle' : 'x-circle'"
                                />
                            </div>
                        </div>
                    </div>

                    <dl class="row small mt-4 mb-0">
                        <dt class="col-sm-4 text-muted">Penerbit</dt>
                        <dd class="col-sm-8">{{ $book->publisher?->name ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted">Tahun terbit</dt>
                        <dd class="col-sm-8">{{ $book->publication_year ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted">ISBN</dt>
                        <dd class="col-sm-8">{{ $book->isbn ?? 'Tidak tersedia' }}</dd>

                        <dt class="col-sm-4 text-muted">Bahasa</dt>
                        <dd class="col-sm-8">{{ $book->language }}</dd>
                    </dl>
                </div>
            </div>

            <div class="alert alert-info mt-4" role="alert">
                <div class="fw-semibold mb-1"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Pengajuan belum berarti buku dipinjam.</div>
                Buku baru resmi dipinjam setelah admin mencatat penyerahan (checkout) dan menetapkan tanggal jatuh tempo.
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">Ketentuan Peminjaman</h2>
                </div>
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-7 text-muted">Kuota terpakai</dt>
                        <dd class="col-5 text-end">{{ $eligibility['quota_used'] }} / {{ $eligibility['quota_max'] }}</dd>

                        <dt class="col-7 text-muted">Sisa kuota</dt>
                        <dd class="col-5 text-end">{{ $eligibility['remaining_quota'] }} eksemplar</dd>

                        <dt class="col-7 text-muted">Durasi pinjam</dt>
                        <dd class="col-5 text-end">{{ $eligibility['duration_days'] }} hari</dd>

                        <dt class="col-7 text-muted">Batas pengambilan</dt>
                        <dd class="col-5 text-end">{{ $eligibility['pickup_deadline_days'] }} hari setelah disetujui</dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="h6 mb-0">Form Pengajuan</h2>
                </div>
                <div class="card-body">
                    @if ($eligibility['allowed'])
                        <form method="POST" action="{{ route('borrower.requests.store', $book) }}" data-loading>
                            @csrf

                            <div class="mb-3">
                                <label for="notes" class="form-label">Catatan untuk admin <span class="text-muted">(opsional)</span></label>
                                <textarea id="notes" name="notes" rows="3" maxlength="500" class="form-control @error('notes') is-invalid @enderror" placeholder="Contoh: dibutuhkan untuk tugas Bahasa Indonesia.">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100" data-loading-text="Mengirim pengajuan...">
                                <i class="bi bi-send me-1" aria-hidden="true"></i>Kirim Pengajuan
                            </button>
                            <a href="{{ route('catalog.show', $book) }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                        </form>
                    @else
                        <div class="alert alert-warning mb-3" role="alert">
                            <div class="fw-semibold mb-1">Pengajuan tidak dapat dikirim.</div>
                            {{ $eligibility['reason'] }}
                        </div>

                        @if ($eligibility['open_request'])
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('borrower.loans.index', ['tab' => $eligibility['open_request']->isApproved() ? 'disetujui' : 'menunggu']) }}" class="btn btn-outline-primary">
                                    Lihat Pengajuan
                                </a>
                                @can('cancel', $eligibility['open_request'])
                                    <form method="POST" action="{{ route('borrower.requests.cancel', $eligibility['open_request']) }}" data-confirm="Batalkan pengajuan untuk buku ini?">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger w-100">Batalkan Pengajuan</button>
                                    </form>
                                @endcan
                            </div>
                        @endif

                        <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary w-100 mt-2">Kembali ke Katalog</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
