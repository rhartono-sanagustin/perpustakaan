@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <x-page-header title="Dashboard" subtitle="Ringkasan operasional perpustakaan.">
        <x-slot:actions>
            <flux:badge color="zinc" icon="calendar-days">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</flux:badge>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi-card icon="journal-bookmark" :value="$totalBooks" label="Total Judul Aktif" variant="primary" :href="route('admin.books.index')" />
        <x-kpi-card icon="collection" :value="$availableCopies" label="Eksemplar Tersedia" variant="success" :href="route('admin.copies.index', ['status' => 'tersedia'])" />
        <x-kpi-card icon="arrow-left-right" :value="$activeLoans" label="Peminjaman Aktif" variant="info" :href="route('admin.loans.index')" />
        <x-kpi-card icon="exclamation-triangle" :value="$overdueLoansCount" label="Terlambat" variant="danger" :href="route('admin.loans.index', ['overdue' => 1])" />
    </div>

    <div class="grid gap-4 xl:grid-cols-3">
        <flux:card class="p-0! xl:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Permintaan Peminjaman Terbaru</flux:heading>
                <div class="flex items-center gap-2">
                    <flux:badge color="zinc" size="sm">{{ $pendingRequestsCount }} menunggu</flux:badge>
                    <flux:link href="{{ route('admin.requests.index') }}" class="text-sm">Lihat semua</flux:link>
                </div>
            </div>

            @if ($recentRequests->isEmpty())
                <x-empty-state
                    icon="inbox"
                    title="Belum ada permintaan"
                    message="Permintaan dari guru dan siswa akan tampil di sini."
                />
            @else
                <div class="overflow-x-auto" data-scrollable-region>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th scope="col">Peminjam</th>
                                <th scope="col">Buku</th>
                                <th scope="col">Diajukan</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentRequests as $loanRequest)
                                <tr>
                                    <td>
                                        <div class="font-semibold">{{ $loanRequest->user->name }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loanRequest->user->roleLabel() }} &middot; {{ $loanRequest->user->nomor_induk }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $loanRequest->book->title }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loanRequest->book->authors_label }}</div>
                                    </td>
                                    <td class="text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400">
                                        {{ $loanRequest->requested_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td>
                                        <x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </flux:card>

        <flux:card class="p-0!">
            <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Perlu Ditindaklanjuti</flux:heading>
            </div>

            <div class="space-y-4 p-5">
                <div class="flex items-center justify-between gap-3 border-b border-zinc-100 pb-4 dark:border-white/10">
                    <div>
                        <div class="font-semibold">Permintaan menunggu</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Belum diproses admin</div>
                    </div>
                    <flux:badge color="amber">{{ $pendingRequestsCount }}</flux:badge>
                </div>

                <div class="flex items-center justify-between gap-3 border-b border-zinc-100 pb-4 dark:border-white/10">
                    <div>
                        <div class="font-semibold">Pinjaman terlambat</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Melewati tanggal jatuh tempo</div>
                    </div>
                    <flux:badge color="red">{{ $overdueLoansCount }}</flux:badge>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="font-semibold">Eksemplar rusak/hilang</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Perlu pemeriksaan inventaris</div>
                    </div>
                    <flux:badge color="zinc">{{ $damagedCopiesCount }}</flux:badge>
                </div>

                @if ($overdueLoans->isNotEmpty())
                    <flux:separator />
                    <div>
                        <flux:heading size="sm" level="3">Terlambat teratas</flux:heading>
                        <ul class="mt-3 space-y-3">
                            @foreach ($overdueLoans as $loan)
                                <li>
                                    <div class="text-sm font-semibold">{{ $loan->user->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->book->title }}</div>
                                    <div class="mt-1 flex items-center gap-1 text-xs text-rose-600 dark:text-rose-400">
                                        <flux:icon.exclamation-triangle variant="mini" class="size-4" />
                                        {{ $loan->daysOverdue() }} hari terlambat
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </flux:card>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        <flux:card>
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <flux:heading size="lg" level="2">Aktivitas Peminjaman</flux:heading>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">7 hari terakhir &middot; {{ $activityTotal }} peminjaman</span>
            </div>

            @if ($activityTotal === 0)
                <x-empty-state
                    icon="bar-chart"
                    title="Belum ada aktivitas"
                    message="Grafik akan terisi setelah admin mencatat peminjaman buku."
                />
            @else
                <div class="activity-chart" aria-hidden="true">
                    @foreach ($loanActivity as $day)
                        <div class="activity-chart-bar">
                            <span class="activity-chart-value">{{ $day['count'] }}</span>
                            <div class="activity-chart-track">
                                <div
                                    class="activity-chart-fill {{ $day['count'] === 0 ? 'activity-chart-fill--empty' : '' }}"
                                    style="height: {{ $day['count'] === 0 ? 0 : max(6, round($day['count'] / $activityMax * 100)) }}%"
                                ></div>
                            </div>
                            <span class="activity-chart-label">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <table class="sr-only">
                    <caption>Aktivitas peminjaman 7 hari terakhir</caption>
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Jumlah peminjaman</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loanActivity as $day)
                            <tr>
                                <th scope="row">{{ $day['full_label'] }}</th>
                                <td>{{ $day['count'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </flux:card>

        <flux:card>
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <flux:heading size="lg" level="2">Kunjungan Perpustakaan</flux:heading>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">7 hari terakhir &middot; {{ $visitTotal }} kunjungan &middot; hari ini {{ $todayVisits }}</span>
            </div>

            @if ($visitTotal === 0)
                <x-empty-state
                    icon="person-check"
                    title="Belum ada kunjungan"
                    message="Grafik akan terisi setelah admin mencatat kunjungan anggota."
                />
            @else
                <div class="activity-chart" aria-hidden="true">
                    @foreach ($visitActivity as $day)
                        <div class="activity-chart-bar">
                            <span class="activity-chart-value">{{ $day['count'] }}</span>
                            <div class="activity-chart-track">
                                <div
                                    class="activity-chart-fill activity-chart-fill--visits {{ $day['count'] === 0 ? 'activity-chart-fill--empty' : '' }}"
                                    style="height: {{ $day['count'] === 0 ? 0 : max(6, round($day['count'] / $visitMax * 100)) }}%"
                                ></div>
                            </div>
                            <span class="activity-chart-label">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <table class="sr-only">
                    <caption>Kunjungan perpustakaan 7 hari terakhir</caption>
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Jumlah kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visitActivity as $day)
                            <tr>
                                <th scope="row">{{ $day['full_label'] }}</th>
                                <td>{{ $day['count'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </flux:card>
    </div>

    <flux:card class="mt-4">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="lg" level="2">Akses cepat</flux:heading>
                <flux:text class="mt-1">Kelola koleksi dan pantau katalog publik.</flux:text>
            </div>
            <div class="flex flex-wrap gap-2">
                <flux:button variant="primary" size="sm" icon="plus" href="{{ route('admin.books.create') }}">Tambah Buku</flux:button>
                <flux:button variant="outline" size="sm" icon="inbox" href="{{ route('admin.requests.index') }}">Permintaan</flux:button>
                <flux:button variant="outline" size="sm" icon="arrow-uturn-left" href="{{ route('admin.returns.index') }}">Pengembalian</flux:button>
                <flux:button variant="outline" size="sm" icon="chart-bar" href="{{ route('admin.reports.index') }}">Laporan</flux:button>
                <flux:button variant="outline" size="sm" icon="rectangle-stack" href="{{ route('admin.copies.index') }}">Inventaris</flux:button>
                <flux:button variant="subtle" size="sm" icon="arrow-top-right-on-square" href="{{ route('catalog.index') }}">Katalog Publik</flux:button>
            </div>
        </div>
    </flux:card>
@endsection
