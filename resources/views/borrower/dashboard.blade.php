@extends('layouts.borrower')

@section('title', 'Beranda')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-2 md:flex-row md:items-end">
        <div>
            <flux:heading size="xl" level="1">Selamat datang, {{ auth()->user()->name }}</flux:heading>
            <flux:text class="mt-1">
                {{ auth()->user()->roleLabel() }} &middot; {{ auth()->user()->nomor_induk }}
                @if (auth()->user()->kelas)
                    &middot; {{ auth()->user()->kelas }}
                @elseif (auth()->user()->unit_kerja)
                    &middot; {{ auth()->user()->unit_kerja }}
                @endif
            </flux:text>
        </div>
        <span class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ now()->locale('id')->translatedFormat('l, d F Y') }}
        </span>
    </div>

    <flux:card class="mb-4 border-s-4! border-s-[var(--color-accent)]!">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="lg" level="2">Cari buku favoritmu</flux:heading>
                <flux:text class="mt-1">Telusuri katalog dan periksa ketersediaan eksemplar.</flux:text>
            </div>
            <flux:button variant="primary" icon="magnifying-glass" href="{{ route('catalog.index') }}">
                Cari Buku
            </flux:button>
        </div>
    </flux:card>

    @if ($announcements->isNotEmpty())
        <flux:card class="mb-4 p-0!">
            <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <div class="flex items-center gap-2">
                    <flux:icon.megaphone class="size-5 text-sky-600 dark:text-sky-400" />
                    <flux:heading size="lg" level="2">Pengumuman</flux:heading>
                </div>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $announcements->count() }} terbaru</span>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-white/10">
                @foreach ($announcements as $announcement)
                    <article class="px-5 py-4">
                        <div class="flex flex-col justify-between gap-1 sm:flex-row">
                            <h3 class="font-semibold">{{ $announcement->title }}</h3>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $announcement->published_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}
                            </span>
                        </div>
                        <div class="announcement-body mt-2 text-sm break-words">{{ $announcement->body }}</div>
                        @if ($announcement->creator)
                            <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Dipublikasikan oleh {{ $announcement->creator->name }}</div>
                        @endif
                    </article>
                @endforeach
            </div>
        </flux:card>
    @endif

    <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <x-kpi-card icon="arrow-left-right" :value="$activeLoansCount" label="Pinjaman Aktif" variant="primary" :href="route('borrower.loans.index')" />
        <x-kpi-card icon="hourglass-split" :value="$pendingRequestsCount" label="Menunggu Diproses" variant="warning" :href="route('borrower.loans.index', ['tab' => 'menunggu'])" />
        <x-kpi-card icon="exclamation-triangle" :value="$overdueLoansCount" label="Terlambat" variant="danger" :href="route('borrower.loans.index', ['tab' => 'terlambat'])" />
    </div>

    <div class="grid gap-4 lg:grid-cols-7">
        <flux:card class="p-0! lg:col-span-4">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Buku yang Sedang Dipinjam</flux:heading>
                <div class="flex items-center gap-2">
                    <flux:badge color="zinc" size="sm">{{ $activeLoansCount }} buku</flux:badge>
                    <flux:link href="{{ route('borrower.loans.index') }}" class="text-sm">Lihat semua</flux:link>
                </div>
            </div>

            @if ($activeLoans->isEmpty())
                <x-empty-state
                    icon="book"
                    title="Belum ada pinjaman aktif"
                    message="Pinjaman yang sedang berjalan akan tampil di sini."
                />
            @else
                <ul class="divide-y divide-zinc-100 dark:divide-white/10">
                    @foreach ($activeLoans as $loan)
                        <li class="px-5 py-4">
                            <div class="flex flex-col justify-between gap-2 sm:flex-row">
                                <div>
                                    <div class="font-semibold">{{ $loan->copy->book->title }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $loan->copy->inventory_code }} &middot; {{ $loan->copy->shelf_location ?? 'Lokasi rak belum diatur' }}
                                    </div>
                                </div>
                                <div class="sm:text-end">
                                    <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" :icon="$loan->isOverdue() ? 'exclamation-triangle' : 'arrow-left-right'" />
                                    <div class="mt-1 text-xs {{ $loan->isOverdue() ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                                        Jatuh tempo {{ $loan->due_at->locale('id')->translatedFormat('d F Y') }}
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </flux:card>

        <flux:card class="p-0! lg:col-span-3">
            <div class="flex items-center justify-between gap-2 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Permintaan Terbaru</flux:heading>
                <flux:link href="{{ route('borrower.loans.index', ['tab' => 'menunggu']) }}" class="text-sm">Lihat semua</flux:link>
            </div>

            @if ($pendingRequests->isEmpty())
                <x-empty-state
                    icon="inbox"
                    title="Tidak ada permintaan aktif"
                    message="Permintaan yang menunggu atau disetujui akan tampil di sini."
                />
            @else
                <ul class="divide-y divide-zinc-100 dark:divide-white/10">
                    @foreach ($pendingRequests as $loanRequest)
                        <li class="px-5 py-4">
                            <div class="font-semibold">{{ $loanRequest->book->title }}</div>
                            <div class="mt-1 flex flex-wrap items-center justify-between gap-2">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $loanRequest->requested_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                </span>
                                <x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" />
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </flux:card>
    </div>

    <flux:card class="mt-4 p-0!">
        <div class="flex items-center justify-between gap-2 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Riwayat Terbaru</flux:heading>
            <flux:link href="{{ route('borrower.loans.history') }}" class="text-sm">Lihat semua</flux:link>
        </div>

        @if ($recentHistory->isEmpty())
            <x-empty-state
                icon="clock-history"
                title="Belum ada riwayat"
                message="Riwayat peminjaman akan tampil setelah transaksi pertama."
            />
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Buku</th>
                            <th scope="col">Tanggal Pinjam</th>
                            <th scope="col">Tanggal Kembali</th>
                            <th scope="col">Kondisi</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentHistory as $loan)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $loan->copy->book->title }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->inventory_code }}</div>
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
                                <td>
                                    <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </flux:card>
@endsection
