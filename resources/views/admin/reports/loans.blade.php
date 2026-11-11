@extends('layouts.admin')

@section('title', $title)
@section('breadcrumb', 'Laporan / '.$title)

@section('content')
    @php
        $exportQuery = ['report' => $activeReport] + $filters->query();
        $action = $mode === 'peminjaman' ? route('admin.reports.loans') : route('admin.reports.returns');
    @endphp

    <x-page-header :title="$title" :subtitle="$subtitle.' Periode '.$filters->periodLabel().'.'">
        <x-slot:actions>
            <a href="{{ route('admin.reports.export', $exportQuery) }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-spreadsheet me-1" aria-hidden="true"></i>Ekspor CSV
            </a>
            <a href="{{ route('admin.reports.print', $exportQuery) }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
                <i class="bi bi-printer me-1" aria-hidden="true"></i>Cetak
            </a>
        </x-slot:actions>
    </x-page-header>

    @include('admin.reports._nav')

    @include('admin.reports._filters', [
        'action' => $action,
        'statusOptions' => $mode === 'peminjaman' ? $statusOptions : [],
        'conditionOptions' => $mode === 'pengembalian' ? $conditionOptions : [],
        'statusLabel' => 'Status pinjaman',
        'conditionLabel' => 'Kondisi kembali',
        'searchPlaceholder' => 'Nama peminjam, nomor induk, judul buku, atau kode eksemplar',
        'resetUrl' => $action,
    ])

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">{{ $mode === 'peminjaman' ? 'Daftar Peminjaman' : 'Daftar Pengembalian' }}</h2>
            <span class="small text-muted">{{ $loans->total() }} transaksi</span>
        </div>

        @if ($loans->isEmpty())
            <div class="card-body">
                <x-empty-state
                    icon="file-earmark-text"
                    title="Tidak ada data untuk filter periode ini"
                    message="Ubah rentang tanggal atau filter lain untuk melihat transaksi."
                >
                    <a href="{{ $action }}" class="btn btn-outline-primary btn-sm">Ubah Periode</a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Transaksi</th>
                            <th scope="col">Peminjam</th>
                            <th scope="col">Buku / Eksemplar</th>
                            <th scope="col">Tanggal Pinjam</th>
                            <th scope="col">{{ $mode === 'peminjaman' ? 'Jatuh Tempo' : 'Tanggal Kembali' }}</th>
                            <th scope="col">{{ $mode === 'peminjaman' ? 'Status' : 'Kondisi' }}</th>
                            <th scope="col">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>
                                    <div class="fw-semibold">#{{ $loan->reference }}</div>
                                    <div class="small text-muted">
                                        {{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $loan->user->name }}</div>
                                    <div class="small text-muted">
                                        {{ $loan->user->roleLabel() }} &middot; {{ $loan->user->nomor_induk }}
                                        @if ($loan->user->kelas)
                                            &middot; {{ $loan->user->kelas }}
                                        @elseif ($loan->user->unit_kerja)
                                            &middot; {{ $loan->user->unit_kerja }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $loan->copy->book->title }}</div>
                                    <div class="small text-muted">{{ $loan->copy->inventory_code }}</div>
                                </td>
                                <td class="small text-muted">
                                    {{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}
                                </td>
                                <td class="small">
                                    @if ($mode === 'peminjaman')
                                        <span class="{{ $loan->isOverdue() ? 'text-danger fw-semibold' : 'text-muted' }}">
                                            {{ $loan->due_at->locale('id')->translatedFormat('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            {{ $loan->returned_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($mode === 'peminjaman')
                                        <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" :icon="$loan->isOverdue() ? 'exclamation-triangle' : 'arrow-left-right'" />
                                    @else
                                        <x-status-badge
                                            :label="$loan->return_condition?->label() ?? '-'"
                                            :variant="$loan->return_condition?->badgeVariant() ?? 'secondary'"
                                        />
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $loan->processor?->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">Menampilkan {{ $loans->firstItem() }}&ndash;{{ $loans->lastItem() }} dari {{ $loans->total() }} transaksi</span>
                {{ $loans->links() }}
            </div>
        @endif
    </div>
@endsection
