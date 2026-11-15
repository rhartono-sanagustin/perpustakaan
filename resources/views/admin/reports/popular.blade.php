@extends('layouts.admin')

@section('title', 'Laporan Buku Populer')
@section('breadcrumb', 'Laporan / Buku Populer')

@section('content')
    @php
        $exportQuery = ['report' => 'populer'] + $filters->query();
    @endphp

    <x-page-header
        title="Laporan Buku Populer"
        :subtitle="'Peringkat buku berdasarkan jumlah checkout periode '.$filters->periodLabel().'.'"
    >
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
        'action' => route('admin.reports.popular'),
        'includeRole' => false,
        'includeLimit' => true,
        'searchPlaceholder' => 'Judul, penulis, ISBN, kategori, atau penerbit',
        'resetUrl' => route('admin.reports.popular'),
    ])

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">Peringkat Buku</h2>
            <span class="small text-muted">{{ $books->count() }} buku</span>
        </div>

        @if ($books->isEmpty())
            <div class="card-body">
                <x-empty-state
                    icon="star"
                    title="Tidak ada data untuk filter periode ini"
                    message="Belum ada checkout buku yang sesuai dengan periode dan filter yang dipilih."
                >
                    <a href="{{ route('admin.reports.popular') }}" class="btn btn-outline-primary btn-sm">Ubah Periode</a>
                </x-empty-state>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Peringkat</th>
                            <th scope="col">Judul Buku</th>
                            <th scope="col">Penulis</th>
                            <th scope="col">Kategori</th>
                            <th scope="col" class="text-end">Jumlah Checkout</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $index => $book)
                            <tr>
                                <td>
                                    <span class="badge {{ $index < 3 ? 'text-bg-primary' : 'text-bg-light border' }}">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('catalog.show', $book) }}" class="fw-semibold">{{ $book->title }}</a>
                                    @if ($book->isbn)
                                        <div class="small text-muted">ISBN {{ $book->isbn }}</div>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $book->authors_label ?: '-' }}</td>
                                <td class="small text-muted">{{ $book->category?->name ?? '-' }}</td>
                                <td class="text-end fw-semibold">{{ $book->checkout_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
