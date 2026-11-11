@extends('layouts.admin')

@section('title', 'Detail Permintaan')
@section('breadcrumb', 'Sirkulasi / Permintaan / Detail')

@section('content')
    <x-page-header title="Detail Permintaan" :subtitle="'#'.$loanRequest->reference.' &middot; '.$loanRequest->book->title">
        <x-slot:actions>
            <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke Antrean
            </a>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Tindakan belum dapat diproses:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">Data Peminjam</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3 small">
                        <div class="col-12 col-md-6">
                            <div class="text-muted">Nama</div>
                            <div class="fw-semibold">{{ $loanRequest->user->name }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted">Peran</div>
                            <div>{{ $loanRequest->user->roleLabel() }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted">Nomor induk</div>
                            <div>{{ $loanRequest->user->nomor_induk }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted">Kelas / unit</div>
                            <div>{{ $loanRequest->user->kelas ?? $loanRequest->user->unit_kerja ?? '-' }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted">Status akun</div>
                            <div>
                                <x-status-badge :label="$loanRequest->user->statusLabel()" :variant="$loanRequest->user->isActive() ? 'success' : 'secondary'" />
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="text-muted">Telepon</div>
                            <div>{{ $loanRequest->user->no_telepon ?? '-' }}</div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="text-muted">Email</div>
                            <div class="text-break">{{ $loanRequest->user->email ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">Buku yang Diajukan</h2>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <img src="{{ $loanRequest->book->cover_url }}" alt="Sampul {{ $loanRequest->book->title }}" class="admin-cover" style="width: 72px; height: 100px;">
                        <div class="min-w-0">
                            <h3 class="h6 mb-1">{{ $loanRequest->book->title }}</h3>
                            <div class="small text-muted">{{ $loanRequest->book->authors_label ?: 'Penulis tidak diketahui' }}</div>
                            <div class="small text-muted">{{ $loanRequest->book->category?->name ?? 'Tanpa kategori' }}</div>
                            <div class="small text-muted">ISBN: {{ $loanRequest->book->isbn ?? 'Tidak tersedia' }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="row row-cols-2 row-cols-sm-4 g-3 text-center">
                        <div class="col">
                            <div class="fs-5 fw-bold">{{ $availableCopies->count() }}</div>
                            <div class="small text-muted">Siap diserahkan</div>
                        </div>
                        <div class="col">
                            <div class="fs-5 fw-bold">{{ $approvedHolds }}</div>
                            <div class="small text-muted">Ditahan pengajuan lain</div>
                        </div>
                        <div class="col">
                            <div class="fs-5 fw-bold">{{ $quota['used'] }} / {{ $quota['max'] }}</div>
                            <div class="small text-muted">Kuota peminjam</div>
                        </div>
                        <div class="col">
                            <div class="fs-5 fw-bold">{{ $quota['duration_days'] }} hari</div>
                            <div class="small text-muted">Durasi pinjam</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">Riwayat Proses</h2>
                </div>
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-sm-4 text-muted">Diajukan</dt>
                        <dd class="col-sm-8">{{ $loanRequest->requested_at->locale('id')->translatedFormat('d F Y, H:i') }}</dd>

                        <dt class="col-sm-4 text-muted">Disetujui</dt>
                        <dd class="col-sm-8">{{ $loanRequest->approved_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted">Batas pengambilan</dt>
                        <dd class="col-sm-8">
                            @if ($loanRequest->expires_at)
                                <span class="{{ $loanRequest->isExpired() ? 'text-danger' : '' }}">
                                    {{ $loanRequest->expires_at->locale('id')->translatedFormat('d F Y, H:i') }}
                                    @if ($loanRequest->isExpired())
                                        (kedaluwarsa)
                                    @endif
                                </span>
                            @else
                                -
                            @endif
                        </dd>

                        <dt class="col-sm-4 text-muted">Ditolak</dt>
                        <dd class="col-sm-8">{{ $loanRequest->rejected_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted">Dibatalkan</dt>
                        <dd class="col-sm-8">{{ $loanRequest->cancelled_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted">Diproses oleh</dt>
                        <dd class="col-sm-8">{{ $loanRequest->processor?->name ?? '-' }}</dd>

                        @if ($loanRequest->notes)
                            <dt class="col-sm-4 text-muted">Catatan peminjam</dt>
                            <dd class="col-sm-8">{{ $loanRequest->notes }}</dd>
                        @endif

                        @if ($loanRequest->rejection_reason)
                            <dt class="col-sm-4 text-muted">Alasan penolakan</dt>
                            <dd class="col-sm-8">{{ $loanRequest->rejection_reason }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            @if ($loanRequest->loan)
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Transaksi Peminjaman</h2>
                    </div>
                    <div class="card-body">
                        <dl class="row small mb-0">
                            <dt class="col-sm-4 text-muted">Nomor transaksi</dt>
                            <dd class="col-sm-8">#{{ $loanRequest->loan->reference }}</dd>

                            <dt class="col-sm-4 text-muted">Eksemplar</dt>
                            <dd class="col-sm-8">{{ $loanRequest->loan->copy?->inventory_code ?? '-' }}</dd>

                            <dt class="col-sm-4 text-muted">Tanggal pinjam</dt>
                            <dd class="col-sm-8">{{ $loanRequest->loan->borrowed_at->locale('id')->translatedFormat('d F Y') }}</dd>

                            <dt class="col-sm-4 text-muted">Jatuh tempo</dt>
                            <dd class="col-sm-8">{{ $loanRequest->loan->due_at->locale('id')->translatedFormat('d F Y') }}</dd>

                            <dt class="col-sm-4 text-muted">Status</dt>
                            <dd class="col-sm-8">
                                <x-status-badge :label="$loanRequest->loan->statusLabel()" :variant="$loanRequest->loan->statusVariant()" />
                            </dd>
                        </dl>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-12 col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">Status Pengajuan</h2>
                </div>
                <div class="card-body">
                    <x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" />

                    @if ($loanRequest->isPending())
                        <p class="small text-muted mt-3 mb-0">
                            Periksa kuota, status akun, dan ketersediaan eksemplar sebelum menyetujui. Persetujuan belum menandai buku sebagai dipinjam.
                        </p>
                    @elseif ($loanRequest->isApproved() && ! $loanRequest->isExpired())
                        <p class="small text-muted mt-3 mb-0">
                            Pengajuan disetujui. Catat checkout setelah buku benar-benar diserahkan kepada peminjam.
                        </p>
                    @elseif ($loanRequest->isApproved() && $loanRequest->isExpired())
                        <div class="alert alert-warning mt-3 mb-0 py-2 small" role="alert">
                            Batas pengambilan sudah lewat. Checkout diblokir; batalkan pengajuan agar peminjam dapat mengajukan ulang.
                        </div>
                    @endif
                </div>
            </div>

            @if ($loanRequest->isPending())
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Tindakan</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.requests.approve', $loanRequest) }}" data-confirm="Setujui permintaan ini? Buku belum dipinjam sampai checkout dicatat." data-loading>
                            @csrf
                            <button type="submit" class="btn btn-success w-100" data-loading-text="Menyetujui...">
                                <i class="bi bi-check-circle me-1" aria-hidden="true"></i>Setujui Permintaan
                            </button>
                        </form>

                        <hr>

                        <form method="POST" action="{{ route('admin.requests.reject', $loanRequest) }}" data-confirm="Tolak permintaan ini? Alasan akan ditampilkan kepada peminjam." data-loading>
                            @csrf
                            <label for="rejection_reason" class="form-label">Alasan penolakan <span class="text-danger">*</span></label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="3" maxlength="1000" class="form-control @error('rejection_reason') is-invalid @enderror" required placeholder="Contoh: eksemplar sedang diperiksa.">{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <button type="submit" class="btn btn-outline-danger w-100 mt-2" data-loading-text="Menolak...">
                                <i class="bi bi-x-circle me-1" aria-hidden="true"></i>Tolak Permintaan
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if ($loanRequest->isApproved())
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Tindakan</h2>
                    </div>
                    <div class="card-body">
                        @if (! $loanRequest->isExpired())
                            <a href="{{ route('admin.requests.checkout', $loanRequest) }}" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Checkout Peminjaman
                            </a>
                        @endif

                        <form method="POST" action="{{ route('admin.requests.cancel', $loanRequest) }}" data-confirm="Batalkan pengajuan ini? Peminjam harus mengajukan ulang jika masih membutuhkan buku." class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">Batalkan Pengajuan</button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h2 class="h6 mb-0">Kebijakan &amp; Stok</h2>
                </div>
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-7 text-muted">Sisa kuota peminjam</dt>
                        <dd class="col-5 text-end">{{ $quota['remaining'] }} eksemplar</dd>

                        <dt class="col-7 text-muted">Durasi pinjam</dt>
                        <dd class="col-5 text-end">{{ $quota['duration_days'] }} hari</dd>

                        <dt class="col-7 text-muted">Batas pengambilan</dt>
                        <dd class="col-5 text-end">{{ $quota['pickup_deadline_days'] }} hari</dd>

                        <dt class="col-7 text-muted">Eksemplar siap serah</dt>
                        <dd class="col-5 text-end">{{ $availableCopies->count() }}</dd>

                        <dt class="col-7 text-muted">Ditahan pengajuan lain</dt>
                        <dd class="col-5 text-end">{{ $approvedHolds }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
