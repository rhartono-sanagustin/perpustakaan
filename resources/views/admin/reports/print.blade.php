@extends('layouts.print')

@section('title', $data['title'])

@section('content')
    <header class="print-header">
        <div>
            <div class="print-brand">{{ config('library.name') }}</div>
            <div class="print-school">{{ config('library.school_name') }}</div>
            <div class="print-tagline">{{ config('library.tagline') }}</div>
        </div>
        <div class="print-info">
            <div>Dicetak: {{ $printedAt->locale('id')->translatedFormat('d F Y, H:i') }}</div>
            <div>Oleh: {{ auth()->user()->name }}</div>
        </div>
    </header>

    <h1 class="print-title">{{ $data['title'] }}</h1>
    <div class="print-meta">
        @if (in_array($report, ['keterlambatan', 'inventaris'], true))
            <span>Data per {{ $printedAt->locale('id')->translatedFormat('d F Y') }}</span>
        @else
            <span>Periode: {{ $filters->periodLabel() }}</span>
        @endif

        @if ($filters->search !== '')
            <span>Pencarian: "{{ $filters->search }}"</span>
        @endif

        @if ($filters->role)
            <span>Peran: {{ $filters->role->label() }}</span>
        @endif

        @if ($filters->status !== '')
            <span>Status: {{ ucfirst(str_replace('_', ' ', $filters->status)) }}</span>
        @endif

        @if ($filters->condition !== '')
            <span>Kondisi: {{ ucfirst(str_replace('_', ' ', $filters->condition)) }}</span>
        @endif
    </div>

    @if ($report === 'ringkasan')
        @php($summary = $data['summary'])
        <table class="table table-sm print-table print-summary-table">
            <tbody>
                <tr>
                    <th scope="row">Total judul aktif</th>
                    <td>{{ $summary['total_books'] }}</td>
                    <th scope="row">Total eksemplar</th>
                    <td>{{ $summary['total_copies'] }}</td>
                </tr>
                <tr>
                    <th scope="row">Peminjaman aktif</th>
                    <td>{{ $summary['active_loans'] }}</td>
                    <th scope="row">Terlambat</th>
                    <td>{{ $summary['overdue_loans'] }}</td>
                </tr>
                <tr>
                    <th scope="row">Permintaan menunggu</th>
                    <td>{{ $summary['pending_requests'] }}</td>
                    <th scope="row">Disetujui, menunggu diambil</th>
                    <td>{{ $summary['approved_requests'] }}</td>
                </tr>
                <tr>
                    <th scope="row">Peminjaman pada periode</th>
                    <td>{{ $summary['loans_in_period'] }}</td>
                    <th scope="row">Pengembalian pada periode</th>
                    <td>{{ $summary['returns_in_period'] }}</td>
                </tr>
                @foreach ($copyStatuses as $copyStatus)
                    <tr>
                        <th scope="row">Eksemplar {{ strtolower($copyStatus->label()) }}</th>
                        <td>{{ $summary['copies_by_status'][$copyStatus->value] ?? 0 }}</td>
                        @if (! $loop->last)
                            <th scope="row">&nbsp;</th>
                            <td>&nbsp;</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2 class="print-section-title">Rekap Kategori</h2>
        <table class="table table-sm print-table">
            <thead>
                <tr>
                    <th scope="col">Kategori</th>
                    <th scope="col" class="text-end">Judul Aktif</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($summary['categories'] as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td class="text-end">{{ $category->active_books_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h2 class="print-section-title">Buku Paling Populer</h2>
        <table class="table table-sm print-table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Judul Buku</th>
                    <th scope="col">Kategori</th>
                    <th scope="col" class="text-end">Jumlah Checkout</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($summary['top_books'] as $index => $book)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->category?->name ?? '-' }}</td>
                        <td class="text-end">{{ $book->checkout_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Tidak ada peminjaman pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif (in_array($report, ['peminjaman', 'pengembalian'], true))
        @php($isReturn = $report === 'pengembalian')
        <table class="table table-sm print-table">
            <thead>
                <tr>
                    <th scope="col">No. Transaksi</th>
                    <th scope="col">Peminjam</th>
                    <th scope="col">Nomor Induk</th>
                    <th scope="col">Judul Buku</th>
                    <th scope="col">Kode Inventaris</th>
                    <th scope="col">Tanggal Pinjam</th>
                    <th scope="col">{{ $isReturn ? 'Tanggal Kembali' : 'Jatuh Tempo' }}</th>
                    <th scope="col">{{ $isReturn ? 'Kondisi Kembali' : 'Status' }}</th>
                    <th scope="col">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['loans'] as $loan)
                    <tr>
                        <td>{{ $loan->reference }}</td>
                        <td>{{ $loan->user->name }}</td>
                        <td>{{ $loan->user->nomor_induk }}</td>
                        <td>{{ $loan->copy->book->title }}</td>
                        <td>{{ $loan->copy->inventory_code }}</td>
                        <td>{{ $loan->borrowed_at?->format('d/m/Y') }}</td>
                        <td>
                            {{ $isReturn ? $loan->returned_at?->format('d/m/Y') : $loan->due_at?->format('d/m/Y') }}
                        </td>
                        <td>
                            {{ $isReturn ? ($loan->return_condition?->label() ?? '-') : $loan->statusLabel() }}
                        </td>
                        <td>{{ $loan->processor?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">Tidak ada data untuk filter periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($report === 'keterlambatan')
        <table class="table table-sm print-table">
            <thead>
                <tr>
                    <th scope="col">No. Transaksi</th>
                    <th scope="col">Peminjam</th>
                    <th scope="col">Nomor Induk</th>
                    <th scope="col">Kelas/Unit</th>
                    <th scope="col">Telepon</th>
                    <th scope="col">Judul Buku</th>
                    <th scope="col">Kode Inventaris</th>
                    <th scope="col">Jatuh Tempo</th>
                    <th scope="col" class="text-end">Hari Terlambat</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['loans'] as $loan)
                    <tr>
                        <td>{{ $loan->reference }}</td>
                        <td>{{ $loan->user->name }}</td>
                        <td>{{ $loan->user->nomor_induk }}</td>
                        <td>{{ $loan->user->kelas ?? $loan->user->unit_kerja ?? '-' }}</td>
                        <td>{{ $loan->user->no_telepon ?? '-' }}</td>
                        <td>{{ $loan->copy->book->title }}</td>
                        <td>{{ $loan->copy->inventory_code }}</td>
                        <td>{{ $loan->due_at?->format('d/m/Y') }}</td>
                        <td class="text-end">{{ $loan->daysOverdue() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">Tidak ada pinjaman terlambat saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($report === 'inventaris')
        <div class="print-meta mb-2">
            @foreach ($copyStatuses as $copyStatus)
                <span>{{ $copyStatus->label() }}: {{ $data['copies_by_status'][$copyStatus->value] ?? 0 }}</span>
            @endforeach
        </div>

        <table class="table table-sm print-table">
            <thead>
                <tr>
                    <th scope="col">Kode Inventaris</th>
                    <th scope="col">Judul Buku</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Lokasi Rak</th>
                    <th scope="col">Kondisi</th>
                    <th scope="col">Status</th>
                    <th scope="col">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['copies'] as $copy)
                    <tr>
                        <td>{{ $copy->inventory_code }}</td>
                        <td>{{ $copy->book->title }}</td>
                        <td>{{ $copy->book->category?->name ?? '-' }}</td>
                        <td>{{ $copy->shelf_location ?? '-' }}</td>
                        <td>{{ $copy->condition->label() }}</td>
                        <td>{{ $copy->status->label() }}</td>
                        <td>{{ $copy->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Tidak ada eksemplar yang cocok dengan filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($report === 'populer')
        <table class="table table-sm print-table">
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
                @forelse ($data['books'] as $index => $book)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->authors_label ?: '-' }}</td>
                        <td>{{ $book->category?->name ?? '-' }}</td>
                        <td class="text-end">{{ $book->checkout_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada data checkout pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <footer class="print-footer">
        <div class="print-footer-note">
            Dokumen ini dihasilkan otomatis oleh {{ config('library.name') }} pada
            {{ $printedAt->locale('id')->translatedFormat('d F Y, H:i') }}.
        </div>
        <div class="print-signature">
            <div>{{ config('library.school_name') }}, {{ $printedAt->locale('id')->translatedFormat('d F Y') }}</div>
            <div>Petugas Perpustakaan</div>
            <div class="print-signature-space"></div>
            <div class="print-signature-name">{{ auth()->user()->name }}</div>
        </div>
    </footer>
@endsection
