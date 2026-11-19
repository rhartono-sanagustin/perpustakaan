@extends('layouts.admin')

@section('title', 'Permintaan Peminjaman')
@section('breadcrumb', 'Sirkulasi / Permintaan')

@section('content')
    <x-page-header title="Permintaan Peminjaman" subtitle="Proses pengajuan guru dan siswa sebelum buku diserahkan.">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrows-right-left" href="{{ route('admin.loans.index') }}">
                Peminjaman Aktif
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <flux:card class="mb-4">
        <form method="GET" action="{{ route('admin.requests.index') }}" class="grid items-end gap-3 md:grid-cols-2 xl:grid-cols-12">
            <div class="xl:col-span-4">
                <flux:field>
                    <flux:label for="q">Cari</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Nama, nomor induk, judul, atau ISBN" />
                </flux:field>
            </div>

            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label for="status">Status</flux:label>
                    <flux:select id="status" name="status">
                        @foreach ($statuses as $requestStatus)
                            <option value="{{ $requestStatus->value }}" @selected($status === $requestStatus->value)>
                                {{ $requestStatus->label() }} ({{ $counts[$requestStatus->value] ?? 0 }})
                            </option>
                        @endforeach
                        <option value="all" @selected($status === 'all')>Semua status</option>
                    </flux:select>
                </flux:field>
            </div>

            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label for="date_from">Dari tanggal</flux:label>
                    <flux:input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" />
                </flux:field>
            </div>

            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label for="date_to">Sampai tanggal</flux:label>
                    <flux:input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" />
                </flux:field>
            </div>

            <div class="flex gap-2 xl:col-span-2">
                <flux:button type="submit" variant="primary" class="flex-1">Terapkan</flux:button>
                @if ($search !== '' || $status !== 'menunggu' || $dateFrom || $dateTo)
                    <flux:button variant="outline" href="{{ route('admin.requests.index') }}" icon="arrow-path" aria-label="Reset filter" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Antrean Permintaan</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $requests->total() }} data</flux:badge>
        </div>

        @if ($requests->isEmpty())
            <x-empty-state icon="inbox" title="Tidak ada permintaan" message="Tidak ada permintaan yang sesuai dengan filter saat ini." />
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Peminjam</th>
                            <th scope="col">Buku</th>
                            <th scope="col">Diajukan</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $loanRequest)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $loanRequest->user->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $loanRequest->user->roleLabel() }} &middot; {{ $loanRequest->user->nomor_induk }}
                                        @if ($loanRequest->user->kelas)
                                            &middot; {{ $loanRequest->user->kelas }}
                                        @elseif ($loanRequest->user->unit_kerja)
                                            &middot; {{ $loanRequest->user->unit_kerja }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $loanRequest->book->title }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loanRequest->book->authors_label ?: 'Penulis tidak diketahui' }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">#{{ $loanRequest->reference }}</div>
                                </td>
                                <td class="text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400">
                                    {{ $loanRequest->requested_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td>
                                    <x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" />
                                    @if ($loanRequest->isApproved() && ! $loanRequest->isExpired())
                                        <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            Ambil s.d. {{ $loanRequest->expires_at?->locale('id')->translatedFormat('d M Y, H:i') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <flux:button size="sm" variant="outline" href="{{ route('admin.requests.show', $loanRequest) }}">
                                        {{ $loanRequest->isPending() ? 'Proses' : 'Detail' }}
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $requests->firstItem() }}&ndash;{{ $requests->lastItem() }} dari {{ $requests->total() }} permintaan</span>
                {{ $requests->links() }}
            </div>
        @endif
    </flux:card>
@endsection
