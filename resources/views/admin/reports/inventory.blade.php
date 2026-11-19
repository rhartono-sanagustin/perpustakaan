@extends('layouts.admin')

@section('title', 'Laporan Inventaris')
@section('breadcrumb', 'Laporan / Inventaris')

@section('content')
    @php
        $exportQuery = ['report' => 'inventaris'] + $filters->query();
    @endphp

    <x-page-header
        title="Laporan Inventaris"
        subtitle="Daftar eksemplar beserta status, kondisi, dan lokasi penyimpanan."
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
        'action' => route('admin.reports.inventory'),
        'includeDate' => false,
        'includeRole' => false,
        'statusOptions' => $statusOptions,
        'conditionOptions' => $conditionOptions,
        'statusLabel' => 'Status eksemplar',
        'conditionLabel' => 'Kondisi fisik',
        'searchPlaceholder' => 'Kode inventaris, judul buku, atau ISBN',
        'resetUrl' => route('admin.reports.inventory'),
    ])

    <div class="mb-4 grid grid-cols-2 gap-2 md:grid-cols-3 xl:grid-cols-5">
        @foreach ($copyStatuses as $copyStatus)
            <flux:card class="py-3!">
                <div class="text-xl font-semibold">{{ $copiesByStatus[$copyStatus->value] ?? 0 }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $copyStatus->label() }}</div>
            </flux:card>
        @endforeach
    </div>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Daftar Eksemplar</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $copies->total() }} eksemplar</flux:badge>
        </div>

        @if ($copies->isEmpty())
            <x-empty-state
                icon="box-seam"
                title="Tidak ada eksemplar yang cocok"
                message="Ubah filter status, kondisi, atau kata kunci pencarian."
            >
                <flux:button variant="outline" size="sm" href="{{ route('admin.reports.inventory') }}">Reset Filter</flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
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
                        @foreach ($copies as $copy)
                            <tr>
                                <td class="font-semibold">{{ $copy->inventory_code }}</td>
                                <td>{{ $copy->book->title }}</td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ $copy->book->category?->name ?? '-' }}</td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ $copy->shelf_location ?? '-' }}</td>
                                <td>
                                    <x-status-badge :label="$copy->condition->label()" :variant="$copy->condition->badgeVariant()" />
                                </td>
                                <td>
                                    <x-status-badge :label="$copy->status->label()" :variant="$copy->status->badgeVariant()" />
                                </td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ $copy->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $copies->firstItem() }}&ndash;{{ $copies->lastItem() }} dari {{ $copies->total() }} eksemplar</span>
                {{ $copies->links() }}
            </div>
        @endif
    </flux:card>
@endsection
