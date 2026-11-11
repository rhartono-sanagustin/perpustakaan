@extends('layouts.admin')

@section('title', 'Catat Pengembalian')
@section('breadcrumb', 'Sirkulasi / Pengembalian / Form')

@section('content')
    <x-page-header title="Catat Pengembalian" :subtitle="'#'.$loan->reference.' · '.$loan->copy->book->title">
        <x-slot:actions>
            <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Pengembalian belum dapat dicatat:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <form method="POST" action="{{ route('admin.loans.return.store', $loan) }}" data-confirm="Catat pengembalian buku ini? Status eksemplar akan diperbarui sesuai kondisi yang dipilih." data-loading>
                @csrf

                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Kondisi Buku Saat Dikembalikan</h2>
                    </div>
                    <div class="card-body">
                        <fieldset>
                            <legend class="form-label fs-6">Pilih kondisi <span class="text-danger">*</span></legend>

                            <div class="row g-2">
                                @foreach ($conditions as $condition)
                                    <div class="col-12 col-sm-6">
                                        <div class="form-check border rounded p-3 ps-5 h-100">
                                            <input
                                                type="radio"
                                                id="condition-{{ $condition->value }}"
                                                name="return_condition"
                                                value="{{ $condition->value }}"
                                                class="form-check-input @error('return_condition') is-invalid @enderror"
                                                @checked(old('return_condition', \App\Enums\ReturnCondition::Baik->value) === $condition->value)
                                                required
                                            >
                                            <label class="form-check-label w-100" for="condition-{{ $condition->value }}">
                                                <span class="fw-semibold">{{ $condition->label() }}</span>
                                                <span class="d-block small text-muted mt-1">
                                                    @switch($condition)
                                                        @case(\App\Enums\ReturnCondition::Baik)
                                                            Eksemplar kembali tersedia dan dapat dipinjam lagi.
                                                            @break
                                                        @case(\App\Enums\ReturnCondition::RusakRingan)
                                                            Eksemplar ditandai rusak ringan dan tidak otomatis tersedia.
                                                            @break
                                                        @case(\App\Enums\ReturnCondition::RusakBerat)
                                                            Eksemplar ditandai rusak berat dan perlu pemeriksaan.
                                                            @break
                                                        @case(\App\Enums\ReturnCondition::Hilang)
                                                            Eksemplar ditandai hilang dan tidak dapat dipinjam.
                                                            @break
                                                    @endswitch
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('return_condition')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </fieldset>

                        <div class="mt-4">
                            <label for="return_notes" class="form-label">Catatan pengembalian</label>
                            <textarea id="return_notes" name="return_notes" rows="3" maxlength="1000" class="form-control @error('return_notes') is-invalid @enderror" placeholder="Contoh: sampul lepas pada bagian belakang.">{{ old('return_notes') }}</textarea>
                            @error('return_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Catatan tidak wajib, tetapi sangat membantu petugas inventaris saat menindaklanjuti kerusakan.</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-primary" data-loading-text="Menyimpan pengembalian...">
                        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>Simpan Pengembalian
                    </button>
                    <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">Ringkasan Pinjaman</h2>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 mb-3">
                        <img src="{{ $loan->copy->book->cover_url }}" alt="Sampul {{ $loan->copy->book->title }}" class="admin-cover" style="width: 64px; height: 90px;">
                        <div class="min-w-0">
                            <div class="fw-semibold">{{ $loan->copy->book->title }}</div>
                            <div class="small text-muted">{{ $loan->copy->book->authors_label ?: 'Penulis tidak diketahui' }}</div>
                            <div class="small text-muted">{{ $loan->copy->inventory_code }}</div>
                        </div>
                    </div>

                    <dl class="row small mb-0">
                        <dt class="col-6 text-muted">Peminjam</dt>
                        <dd class="col-6 text-end">{{ $loan->user->name }}</dd>

                        <dt class="col-6 text-muted">Nomor induk</dt>
                        <dd class="col-6 text-end">{{ $loan->user->nomor_induk }}</dd>

                        <dt class="col-6 text-muted">Dipinjam</dt>
                        <dd class="col-6 text-end">{{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}</dd>

                        <dt class="col-6 text-muted">Jatuh tempo</dt>
                        <dd class="col-6 text-end">{{ $loan->due_at->locale('id')->translatedFormat('d M Y') }}</dd>

                        <dt class="col-6 text-muted">Status</dt>
                        <dd class="col-6 text-end">
                            <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" :icon="$loan->isOverdue() ? 'exclamation-triangle' : 'arrow-left-right'" />
                        </dd>

                        <dt class="col-6 text-muted">Kondisi eksemplar</dt>
                        <dd class="col-6 text-end">{{ $loan->copy->condition->label() }}</dd>
                    </dl>

                    @if ($loan->isOverdue())
                        <div class="alert alert-warning mt-3 mb-0 py-2 small" role="alert">
                            <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>
                            Pinjaman ini terlambat {{ $loan->daysOverdue() }} hari.
                        </div>
                    @endif
                </div>
            </div>

            <div class="alert alert-info mb-0" role="alert">
                <div class="fw-semibold mb-1"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Setelah pengembalian</div>
                Transaksi ditutup dan tidak dapat dikembalikan lagi. Eksemplar dengan kondisi Baik menjadi Tersedia; kondisi rusak atau hilang akan ditandai pada inventaris.
            </div>
        </div>
    </div>
@endsection
