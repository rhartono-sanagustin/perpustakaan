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
            <flux:button variant="outline" icon="document-text" href="{{ route('admin.reports.export', $exportQuery) }}">
                Ekspor CSV
            </flux:button>
            <flux:button variant="outline" icon="printer" href="{{ route('admin.reports.print', $exportQuery) }}" target="_blank" rel="noopener">
                Cetak
            </flux:button>
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

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Peringkat Buku</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $books->count() }} buku</flux:badge>
        </div>

        @if ($books->isEmpty())
            <x-empty-state
                icon="star"
                title="Tidak ada data untuk filter periode ini"
                message="Belum ada checkout buku yang sesuai dengan periode dan filter yang dipilih."
            >
                <flux:button variant="outline" size="sm" href="{{ route('admin.reports.popular') }}">Ubah Periode</flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
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
                                    <flux:badge :color="$index < 3 ? 'sky' : 'zinc'" size="sm">{{ $index + 1 }}</flux:badge>
                                </td>
                                <td>
                                    <a href="{{ route('catalog.show', $book) }}" class="font-semibold text-zinc-900 dark:text-white">{{ $book->title }}</a>
                                    @if ($book->isbn)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">ISBN {{ $book->isbn }}</div>
                                    @endif
                                </td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ $book->authors_label ?: '-' }}</td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ $book->category?->name ?? '-' }}</td>
                                <td class="text-end font-semibold">{{ $book->checkout_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </flux:card>
@endsection
