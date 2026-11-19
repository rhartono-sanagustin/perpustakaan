@extends('layouts.borrower')

@section('title', 'Pinjaman Saya')

@section('content')
    <x-page-header title="Pinjaman Saya" subtitle="Pantau pengajuan, pinjaman aktif, dan jatuh tempo buku Anda.">
        <x-slot:actions>
            <flux:button variant="primary" icon="magnifying-glass" href="{{ route('catalog.index') }}">
                Cari Buku
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Tindakan belum dapat diproses:" />

    @php
        $tabs = [
            'menunggu' => ['label' => 'Menunggu Diproses', 'icon' => 'hourglass', 'count' => $counts['menunggu']],
            'disetujui' => ['label' => 'Disetujui', 'icon' => 'check-circle', 'count' => $counts['disetujui']],
            'dipinjam' => ['label' => 'Sedang Dipinjam', 'icon' => 'arrows-right-left', 'count' => $counts['dipinjam']],
            'terlambat' => ['label' => 'Terlambat', 'icon' => 'exclamation-triangle', 'count' => $counts['terlambat']],
        ];
    @endphp

    <nav class="mb-4 flex flex-wrap gap-2" aria-label="Filter status pinjaman">
        @foreach ($tabs as $key => $item)
            <a
                href="{{ route('borrower.loans.index', ['tab' => $key]) }}"
                @class([
                    'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium transition',
                    'bg-[var(--color-accent)] text-[var(--color-accent-foreground)] shadow-xs' => $tab === $key,
                    'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10' => $tab !== $key,
                ])
                @if ($tab === $key) aria-current="page" @endif
            >
                <x-icon :name="$item['icon']" variant="mini" class="size-4" />
                {{ $item['label'] }}
                <flux:badge :color="$tab === $key ? 'white' : 'zinc'" size="sm">{{ $item['count'] }}</flux:badge>
            </a>
        @endforeach
    </nav>

    @if ($items->isEmpty())
        <flux:card>
            @if ($tab === 'menunggu')
                <x-empty-state icon="hourglass" title="Tidak ada pengajuan yang menunggu" message="Pengajuan baru akan tampil di sini setelah Anda mengirimkannya dari katalog." />
            @elseif ($tab === 'disetujui')
                <x-empty-state icon="check-circle" title="Tidak ada pengajuan yang disetujui" message="Pengajuan yang disetujui admin akan tampil di sini beserta batas pengambilannya." />
            @elseif ($tab === 'terlambat')
                <x-empty-state icon="exclamation-triangle" title="Tidak ada pinjaman terlambat" message="Pinjaman yang melewati jatuh tempo akan ditandai di sini." />
            @else
                <x-empty-state icon="book-open" title="Anda belum memiliki pinjaman aktif" message="Jelajahi katalog dan ajukan buku yang ingin Anda baca.">
                    <flux:button variant="primary" size="sm" href="{{ route('catalog.index') }}">Jelajahi Katalog</flux:button>
                </x-empty-state>
            @endif
        </flux:card>
    @elseif (in_array($tab, ['menunggu', 'disetujui'], true))
        <div class="space-y-3">
            @foreach ($items as $loanRequest)
                <flux:card>
                    <div class="flex flex-col justify-between gap-3 lg:flex-row">
                        <div class="min-w-0">
                            <flux:heading size="lg" level="2" class="text-base!">{{ $loanRequest->book->title }}</flux:heading>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loanRequest->book->authors_label ?: 'Penulis tidak diketahui' }}</p>
                            <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                Diajukan {{ $loanRequest->requested_at->locale('id')->translatedFormat('d F Y, H:i') }}
                                &middot; <span class="text-zinc-700 dark:text-zinc-200">#{{ $loanRequest->reference }}</span>
                            </div>
                        </div>
                        <div class="lg:text-end">
                            <x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" />
                            @if ($loanRequest->isApproved())
                                <div class="mt-2 text-xs {{ $loanRequest->isExpired() ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                                    @if ($loanRequest->isExpired())
                                        Batas pengambilan sudah lewat
                                    @else
                                        Ambil sebelum {{ $loanRequest->expires_at?->locale('id')->translatedFormat('d F Y, H:i') }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($loanRequest->isApproved() && ! $loanRequest->isExpired())
                        <flux:callout icon="information-circle" class="mt-4">
                            <flux:callout.text>Pengajuan sudah disetujui. Buku belum dipinjam sampai Anda mengambilnya dan admin mencatat checkout.</flux:callout.text>
                        </flux:callout>
                    @endif

                    @if ($loanRequest->isApproved() && $loanRequest->isExpired())
                        <flux:callout variant="warning" icon="exclamation-triangle" class="mt-4">
                            <flux:callout.text>Batas pengambilan sudah lewat. Batalkan pengajuan ini lalu ajukan ulang jika buku masih dibutuhkan.</flux:callout.text>
                        </flux:callout>
                    @endif

                    @if ($loanRequest->notes)
                        <div class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                            <span class="font-semibold">Catatan Anda:</span> {{ $loanRequest->notes }}
                        </div>
                    @endif

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <flux:button size="sm" variant="outline" href="{{ route('catalog.show', $loanRequest->book) }}">
                            Lihat Buku
                        </flux:button>

                        @can('cancel', $loanRequest)
                            <form method="POST" action="{{ route('borrower.requests.cancel', $loanRequest) }}" data-confirm="Batalkan pengajuan untuk buku ini?">
                                @csrf
                                <flux:button type="submit" size="sm" variant="danger">Batalkan Pengajuan</flux:button>
                            </form>
                        @endcan
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $items->firstItem() }}&ndash;{{ $items->lastItem() }} dari {{ $items->total() }} pengajuan</span>
            {{ $items->links() }}
        </div>
    @else
        <div class="space-y-3">
            @foreach ($items as $loan)
                <flux:card>
                    <div class="flex flex-col justify-between gap-3 lg:flex-row">
                        <div class="min-w-0">
                            <flux:heading size="lg" level="2" class="text-base!">{{ $loan->copy->book->title }}</flux:heading>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->book->authors_label ?: 'Penulis tidak diketahui' }}</p>
                            <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $loan->copy->inventory_code }} &middot; {{ $loan->copy->shelf_location ?? 'Lokasi rak belum diatur' }}
                                &middot; <span class="text-zinc-700 dark:text-zinc-200">#{{ $loan->reference }}</span>
                            </div>
                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                Dipinjam {{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}
                            </div>
                        </div>
                        <div class="lg:text-end">
                            <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" :icon="$loan->isOverdue() ? 'exclamation-triangle' : 'arrows-right-left'" />
                            <div class="mt-2 text-xs {{ $loan->isOverdue() ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                                Jatuh tempo {{ $loan->due_at->locale('id')->translatedFormat('d F Y') }}
                            </div>
                            @if ($loan->isOverdue())
                                <div class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $loan->daysOverdue() }} hari terlambat</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <flux:button size="sm" variant="outline" href="{{ route('catalog.show', $loan->copy->book) }}">
                            Lihat Buku
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $items->firstItem() }}&ndash;{{ $items->lastItem() }} dari {{ $items->total() }} pinjaman</span>
            {{ $items->links() }}
        </div>
    @endif
@endsection
