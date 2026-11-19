@extends('layouts.admin')

@section('title', 'Checkout Peminjaman')
@section('breadcrumb', 'Sirkulasi / Permintaan / Checkout')

@section('content')
    <x-page-header title="Checkout Peminjaman" :subtitle="'#'.$loanRequest->reference.' · '.$loanRequest->user->name">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.requests.show', $loanRequest) }}">
                Kembali ke Detail
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Checkout belum dapat dicatat:" />

    <div class="grid gap-4 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <form method="POST" action="{{ route('admin.requests.checkout.store', $loanRequest) }}" data-confirm="Catat penyerahan buku ini? Status eksemplar akan berubah menjadi Dipinjam dan tanggal jatuh tempo ditetapkan." data-loading>
                @csrf

                <flux:card class="mb-4 p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Pilih Eksemplar yang Diserahkan</flux:heading>
                    </div>
                    <div class="p-5">
                        @if ($copies->isEmpty())
                            <flux:callout variant="warning" icon="exclamation-triangle">
                                <flux:callout.heading>Tidak ada eksemplar tersedia.</flux:callout.heading>
                                <flux:callout.text>Semua eksemplar buku ini sedang dipinjam, rusak, hilang, atau belum tersedia. Periksa inventaris terlebih dahulu.</flux:callout.text>
                            </flux:callout>
                        @else
                            <flux:text>
                                Pilih eksemplar fisik yang benar-benar diserahkan. Satu eksemplar hanya dapat memiliki satu pinjaman aktif.
                            </flux:text>

                            <div class="mt-4 flex flex-col gap-2">
                                @foreach ($copies as $copy)
                                    <label for="copy-{{ $copy->id }}" class="flex cursor-pointer items-start gap-3 rounded-lg border border-zinc-200 p-3 transition hover:border-[var(--color-accent)] has-checked:border-[var(--color-accent)] has-checked:bg-[color-mix(in_oklab,var(--color-accent),transparent_94%)] dark:border-zinc-700">
                                        <input
                                            type="radio"
                                            id="copy-{{ $copy->id }}"
                                            name="book_copy_id"
                                            value="{{ $copy->id }}"
                                            class="mt-1 size-4 accent-[var(--color-accent)]"
                                            @checked((string) old('book_copy_id') === (string) $copy->id)
                                            required
                                        >
                                        <span class="min-w-0 flex-1">
                                            <span class="flex flex-col justify-between gap-1 sm:flex-row">
                                                <span class="font-semibold">{{ $copy->inventory_code }}</span>
                                                <x-status-badge :label="$copy->condition->label()" variant="secondary" />
                                            </span>
                                            <span class="mt-1 block text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $copy->shelf_location ?? 'Lokasi rak belum diatur' }}
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <flux:error name="book_copy_id" class="mt-2" />
                        @endif
                    </div>
                </flux:card>

                <flux:card class="mb-4 p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Konfirmasi Penyerahan</flux:heading>
                    </div>
                    <div class="p-5">
                        <dl class="mb-4 grid gap-x-4 gap-y-2 text-sm sm:grid-cols-5">
                            <dt class="text-zinc-500 dark:text-zinc-400">Tanggal pinjam</dt>
                            <dd class="sm:col-span-4">{{ today()->locale('id')->translatedFormat('d F Y') }}</dd>

                            <dt class="text-zinc-500 dark:text-zinc-400">Jatuh tempo</dt>
                            <dd class="font-semibold sm:col-span-4">{{ $dueDate->locale('id')->translatedFormat('d F Y') }}</dd>

                            <dt class="text-zinc-500 dark:text-zinc-400">Durasi pinjam</dt>
                            <dd class="sm:col-span-4">{{ $quota['duration_days'] }} hari</dd>

                            <dt class="text-zinc-500 dark:text-zinc-400">Kuota peminjam</dt>
                            <dd class="sm:col-span-4">{{ $quota['used'] }} / {{ $quota['max'] }} terpakai</dd>
                        </dl>

                        <flux:error name="confirm" class="mb-2" />

                        <flux:checkbox
                            id="confirm"
                            name="confirm"
                            value="1"
                            :checked="(bool) old('confirm')"
                            required
                            label="Saya mengonfirmasi buku telah diserahkan kepada peminjam dan data eksemplar serta jatuh tempo sudah benar."
                        />
                    </div>
                </flux:card>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <flux:button type="submit" variant="primary" icon="arrow-right-start-on-rectangle" data-loading-text="Mencatat checkout..." :disabled="$copies->isEmpty()">
                        Catat Checkout
                    </flux:button>
                    <flux:button variant="outline" href="{{ route('admin.requests.show', $loanRequest) }}">Batal</flux:button>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Ringkasan Peminjam</flux:heading>
                </div>
                <div class="p-5">
                    <div class="font-semibold">{{ $loanRequest->user->name }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $loanRequest->user->roleLabel() }} &middot; {{ $loanRequest->user->nomor_induk }}
                    </div>
                    @if ($loanRequest->user->kelas || $loanRequest->user->unit_kerja)
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loanRequest->user->kelas ?? $loanRequest->user->unit_kerja }}</div>
                    @endif

                    <flux:separator class="my-4" />

                    <div class="flex gap-3">
                        <img src="{{ $loanRequest->book->cover_url }}" alt="Sampul {{ $loanRequest->book->title }}" class="h-[90px] w-16 rounded-md object-cover">
                        <div class="min-w-0">
                            <div class="font-semibold">{{ $loanRequest->book->title }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loanRequest->book->authors_label ?: 'Penulis tidak diketahui' }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loanRequest->book->category?->name ?? 'Tanpa kategori' }}</div>
                        </div>
                    </div>
                </div>
            </flux:card>

            <flux:callout icon="information-circle">
                <flux:callout.heading>Sebelum mencatat checkout</flux:callout.heading>
                <flux:callout.text>
                    Pastikan buku sudah benar-benar diterima peminjam. Setelah checkout, status pengajuan menjadi Dipinjam dan eksemplar tidak lagi tersedia untuk pengajuan lain.
                </flux:callout.text>
            </flux:callout>
        </div>
    </div>
@endsection
