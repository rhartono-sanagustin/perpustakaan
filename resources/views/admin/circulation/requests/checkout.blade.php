@extends('layouts.admin')

@section('title', 'Checkout Peminjaman')
@section('breadcrumb', 'Sirkulasi / Permintaan / Checkout')

@section('content')
    <x-page-header title="Checkout Peminjaman" :subtitle="'#'.$loanRequest->reference.' · '.$loanRequest->user->name">
        <x-slot:actions>
            <a href="{{ route('admin.requests.show', $loanRequest) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke Detail
            </a>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Checkout belum dapat dicatat:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <form method="POST" action="{{ route('admin.requests.checkout.store', $loanRequest) }}" data-confirm="Catat penyerahan buku ini? Status eksemplar akan berubah menjadi Dipinjam dan tanggal jatuh tempo ditetapkan." data-loading>
                @csrf

                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Pilih Eksemplar yang Diserahkan</h2>
                    </div>
                    <div class="card-body">
                        @if ($copies->isEmpty())
                            <div class="alert alert-warning mb-0" role="alert">
                                <div class="fw-semibold mb-1">Tidak ada eksemplar tersedia.</div>
                                Semua eksemplar buku ini sedang dipinjam, rusak, hilang, atau belum tersedia. Periksa inventaris terlebih dahulu.
                            </div>
                        @else
                            <p class="small text-muted">
                                Pilih eksemplar fisik yang benar-benar diserahkan. Satu eksemplar hanya dapat memiliki satu pinjaman aktif.
                            </p>

                            <div class="d-flex flex-column gap-2">
                                @foreach ($copies as $copy)
                                    <div class="form-check border rounded p-3 ps-5">
                                        <input
                                            type="radio"
                                            id="copy-{{ $copy->id }}"
                                            name="book_copy_id"
                                            value="{{ $copy->id }}"
                                            class="form-check-input @error('book_copy_id') is-invalid @enderror"
                                            @checked((string) old('book_copy_id') === (string) $copy->id)
                                            required
                                        >
                                        <label class="form-check-label w-100" for="copy-{{ $copy->id }}">
                                            <span class="d-flex flex-column flex-sm-row justify-content-between gap-1">
                                                <span class="fw-semibold">{{ $copy->inventory_code }}</span>
                                                <x-status-badge :label="$copy->condition->label()" variant="secondary" />
                                            </span>
                                            <span class="d-block small text-muted mt-1">
                                                {{ $copy->shelf_location ?? 'Lokasi rak belum diatur' }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            @error('book_copy_id')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Konfirmasi Penyerahan</h2>
                    </div>
                    <div class="card-body">
                        <dl class="row small mb-3">
                            <dt class="col-sm-5 text-muted">Tanggal pinjam</dt>
                            <dd class="col-sm-7">{{ today()->locale('id')->translatedFormat('d F Y') }}</dd>

                            <dt class="col-sm-5 text-muted">Jatuh tempo</dt>
                            <dd class="col-sm-7 fw-semibold">{{ $dueDate->locale('id')->translatedFormat('d F Y') }}</dd>

                            <dt class="col-sm-5 text-muted">Durasi pinjam</dt>
                            <dd class="col-sm-7">{{ $quota['duration_days'] }} hari</dd>

                            <dt class="col-sm-5 text-muted">Kuota peminjam</dt>
                            <dd class="col-sm-7">{{ $quota['used'] }} / {{ $quota['max'] }} terpakai</dd>
                        </dl>

                        @error('confirm')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror

                        <div class="form-check">
                            <input type="checkbox" id="confirm" name="confirm" value="1" class="form-check-input @error('confirm') is-invalid @enderror" @checked(old('confirm')) required>
                            <label class="form-check-label" for="confirm">
                                Saya mengonfirmasi buku telah diserahkan kepada peminjam dan data eksemplar serta jatuh tempo sudah benar.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-primary" data-loading-text="Mencatat checkout..." @disabled($copies->isEmpty())>
                        <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Catat Checkout
                    </button>
                    <a href="{{ route('admin.requests.show', $loanRequest) }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">Ringkasan Peminjam</h2>
                </div>
                <div class="card-body">
                    <div class="fw-semibold">{{ $loanRequest->user->name }}</div>
                    <div class="small text-muted">
                        {{ $loanRequest->user->roleLabel() }} &middot; {{ $loanRequest->user->nomor_induk }}
                    </div>
                    @if ($loanRequest->user->kelas || $loanRequest->user->unit_kerja)
                        <div class="small text-muted">{{ $loanRequest->user->kelas ?? $loanRequest->user->unit_kerja }}</div>
                    @endif

                    <hr>

                    <div class="d-flex gap-3">
                        <img src="{{ $loanRequest->book->cover_url }}" alt="Sampul {{ $loanRequest->book->title }}" class="admin-cover" style="width: 64px; height: 90px;">
                        <div class="min-w-0">
                            <div class="fw-semibold">{{ $loanRequest->book->title }}</div>
                            <div class="small text-muted">{{ $loanRequest->book->authors_label ?: 'Penulis tidak diketahui' }}</div>
                            <div class="small text-muted">{{ $loanRequest->book->category?->name ?? 'Tanpa kategori' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mb-0" role="alert">
                <div class="fw-semibold mb-1"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Sebelum mencatat checkout</div>
                Pastikan buku sudah benar-benar diterima peminjam. Setelah checkout, status pengajuan menjadi Dipinjam dan eksemplar tidak lagi tersedia untuk pengajuan lain.
            </div>
        </div>
    </div>
@endsection
