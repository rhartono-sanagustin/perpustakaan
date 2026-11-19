@extends('layouts.borrower')

@section('title', 'Riwayat')

@section('content')
    <x-page-header title="Riwayat" subtitle="Lihat riwayat peminjaman yang sudah dikembalikan dan pengajuan yang selesai.">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrows-right-left" href="{{ route('borrower.loans.index') }}">
                Pinjaman Saya
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <nav class="mb-4 flex flex-wrap gap-2" aria-label="Jenis riwayat">
        <a
            href="{{ route('borrower.loans.history', ['tab' => 'pinjaman']) }}"
            @class([
                'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium transition',
                'bg-[var(--color-accent)] text-[var(--color-accent-foreground)] shadow-xs' => $tab === 'pinjaman',
                'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10' => $tab !== 'pinjaman',
            ])
            @if ($tab === 'pinjaman') aria-current="page" @endif
        >
            <flux:icon.clock variant="mini" class="size-4" />
            Riwayat Peminjaman
            <flux:badge :color="$tab === 'pinjaman' ? 'white' : 'zinc'" size="sm">{{ $returnedCount }}</flux:badge>
        </a>
        <a
            href="{{ route('borrower.loans.history', ['tab' => 'pengajuan']) }}"
            @class([
                'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium transition',
                'bg-[var(--color-accent)] text-[var(--color-accent-foreground)] shadow-xs' => $tab === 'pengajuan',
                'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10' => $tab !== 'pengajuan',
            ])
            @if ($tab === 'pengajuan') aria-current="page" @endif
        >
            <flux:icon.inbox variant="mini" class="size-4" />
            Riwayat Pengajuan
            <flux:badge :color="$tab === 'pengajuan' ? 'white' : 'zinc'" size="sm">{{ $finishedRequestCount }}</flux:badge>
        </a>
    </nav>

    @if ($tab === 'pengajuan')
        <flux:card class="p-0!">
            <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Pengajuan Ditolak atau Dibatalkan</flux:heading>
                <flux:badge color="zinc" size="sm">{{ $requestHistory->total() }} data</flux:badge>
            </div>

            @if ($requestHistory->isEmpty())
                <x-empty-state icon="inbox" title="Belum ada riwayat pengajuan" message="Pengajuan yang ditolak atau dibatalkan akan tampil di sini." />
            @else
                <div class="overflow-x-auto" data-scrollable-region>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th scope="col">Buku</th>
                                <th scope="col">Diajukan</th>
                                <th scope="col">Status</th>
                                <th scope="col">Alasan / Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requestHistory as $loanRequest)
                                <tr>
                                    <td>
                                        <a href="{{ route('catalog.show', $loanRequest->book) }}" class="font-semibold hover:underline">{{ $loanRequest->book->title }}</a>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">#{{ $loanRequest->reference }}</div>
                                    </td>
                                    <td class="text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $loanRequest->requested_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                    <td><x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" /></td>
                                    <td class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $loanRequest->rejection_reason ?: ($loanRequest->notes ?: '-') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $requestHistory->firstItem() }}&ndash;{{ $requestHistory->lastItem() }} dari {{ $requestHistory->total() }} pengajuan</span>
                    {{ $requestHistory->links() }}
                </div>
            @endif
        </flux:card>
    @else
        <flux:card class="p-0!">
            <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Peminjaman yang Sudah Dikembalikan</flux:heading>
                <flux:badge color="zinc" size="sm">{{ $loanHistory->total() }} data</flux:badge>
            </div>

            @if ($loanHistory->isEmpty())
                <x-empty-state icon="clock" title="Belum ada riwayat peminjaman" message="Peminjaman yang sudah dikembalikan akan tampil di sini.">
                    <flux:button variant="primary" size="sm" href="{{ route('catalog.index') }}">Jelajahi Katalog</flux:button>
                </x-empty-state>
            @else
                <div class="overflow-x-auto" data-scrollable-region>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th scope="col">Buku</th>
                                <th scope="col">Dipinjam</th>
                                <th scope="col">Dikembalikan</th>
                                <th scope="col">Kondisi</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loanHistory as $loan)
                                <tr>
                                    <td>
                                        <div class="font-semibold">{{ $loan->copy->book->title }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->inventory_code }} &middot; #{{ $loan->reference }}</div>
                                    </td>
                                    <td class="text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}</td>
                                    <td class="text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $loan->returned_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}</td>
                                    <td>
                                        @if ($loan->return_condition)
                                            <x-status-badge :label="$loan->return_condition->label()" :variant="$loan->return_condition->badgeVariant()" />
                                        @else
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">-</span>
                                        @endif
                                    </td>
                                    <td><x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $loanHistory->firstItem() }}&ndash;{{ $loanHistory->lastItem() }} dari {{ $loanHistory->total() }} peminjaman</span>
                    {{ $loanHistory->links() }}
                </div>
            @endif
        </flux:card>
    @endif
@endsection
