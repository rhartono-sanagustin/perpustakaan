@extends('layouts.borrower')

@section('title', 'Ajukan Peminjaman')

@section('content')
    <flux:breadcrumbs class="mb-4">
        <flux:breadcrumbs.item href="{{ route('borrower.dashboard') }}">Beranda</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('catalog.index') }}">Katalog</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('catalog.show', $book) }}">Detail Buku</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Ajukan Peminjaman</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <x-page-header title="Ajukan Peminjaman" subtitle="Periksa ringkasan buku dan ketentuan peminjaman sebelum mengirim pengajuan.">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('catalog.show', $book) }}">
                Kembali ke Detail Buku
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Pengajuan belum dapat dikirim:" />

    <div class="grid gap-4 xl:grid-cols-5">
        <div class="xl:col-span-3">
            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Ringkasan Buku</flux:heading>
                </div>
                <div class="p-5">
                    <div class="flex gap-3">
                        <img src="{{ $book->cover_url }}" alt="Sampul {{ $book->title }}" class="h-[120px] w-[88px] shrink-0 rounded-md object-cover">
                        <div class="min-w-0">
                            <flux:heading size="lg" level="3" class="text-lg!">{{ $book->title }}</flux:heading>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $book->authors_label ?: 'Penulis tidak diketahui' }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <flux:badge color="sky">{{ $book->category?->name ?? 'Tanpa kategori' }}</flux:badge>
                                <x-status-badge
                                    :label="$eligibility['available_copies'].' eksemplar tersedia'"
                                    :variant="$eligibility['available_copies'] > 0 ? 'success' : 'secondary'"
                                    :icon="$eligibility['available_copies'] > 0 ? 'check-circle' : 'x-circle'"
                                />
                            </div>
                        </div>
                    </div>

                    <dl class="mt-5 grid gap-x-4 gap-y-2 text-sm sm:grid-cols-4">
                        <dt class="text-zinc-500 dark:text-zinc-400">Penerbit</dt>
                        <dd class="sm:col-span-3">{{ $book->publisher?->name ?? '-' }}</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Tahun terbit</dt>
                        <dd class="sm:col-span-3">{{ $book->publication_year ?? '-' }}</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">ISBN</dt>
                        <dd class="sm:col-span-3">{{ $book->isbn ?? 'Tidak tersedia' }}</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Bahasa</dt>
                        <dd class="sm:col-span-3">{{ $book->language }}</dd>
                    </dl>
                </div>
            </flux:card>

            <flux:callout icon="information-circle" class="mt-4">
                <flux:callout.heading>Pengajuan belum berarti buku dipinjam.</flux:callout.heading>
                <flux:callout.text>Buku baru resmi dipinjam setelah admin mencatat penyerahan (checkout) dan menetapkan tanggal jatuh tempo.</flux:callout.text>
            </flux:callout>
        </div>

        <div class="space-y-4 xl:col-span-2">
            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Ketentuan Peminjaman</flux:heading>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-3 gap-x-3 gap-y-2 text-sm">
                        <dt class="text-zinc-500 dark:text-zinc-400">Kuota terpakai</dt>
                        <dd class="col-span-2 text-end">{{ $eligibility['quota_used'] }} / {{ $eligibility['quota_max'] }}</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Sisa kuota</dt>
                        <dd class="col-span-2 text-end">{{ $eligibility['remaining_quota'] }} eksemplar</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Durasi pinjam</dt>
                        <dd class="col-span-2 text-end">{{ $eligibility['duration_days'] }} hari</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Batas pengambilan</dt>
                        <dd class="col-span-2 text-end">{{ $eligibility['pickup_deadline_days'] }} hari setelah disetujui</dd>
                    </dl>
                </div>
            </flux:card>

            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Form Pengajuan</flux:heading>
                </div>
                <div class="p-5">
                    @if ($eligibility['allowed'])
                        <form method="POST" action="{{ route('borrower.requests.store', $book) }}" data-loading>
                            @csrf

                            <flux:field class="mb-4">
                                <flux:label for="notes">Catatan untuk admin <span class="font-normal text-zinc-500">(opsional)</span></flux:label>
                                <flux:textarea id="notes" name="notes" rows="3" maxlength="500" placeholder="Contoh: dibutuhkan untuk tugas Bahasa Indonesia.">{{ old('notes') }}</flux:textarea>
                                <flux:error name="notes" />
                            </flux:field>

                            <flux:button type="submit" variant="primary" icon="paper-airplane" class="w-full" data-loading-text="Mengirim pengajuan...">
                                Kirim Pengajuan
                            </flux:button>
                            <flux:button variant="outline" href="{{ route('catalog.show', $book) }}" class="mt-2 w-full">
                                Batal
                            </flux:button>
                        </form>
                    @else
                        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-4">
                            <flux:callout.heading>Pengajuan tidak dapat dikirim.</flux:callout.heading>
                            <flux:callout.text>{{ $eligibility['reason'] }}</flux:callout.text>
                        </flux:callout>

                        @if ($eligibility['open_request'])
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <flux:button variant="outline" href="{{ route('borrower.loans.index', ['tab' => $eligibility['open_request']->isApproved() ? 'disetujui' : 'menunggu']) }}">
                                    Lihat Pengajuan
                                </flux:button>
                                @can('cancel', $eligibility['open_request'])
                                    <form method="POST" action="{{ route('borrower.requests.cancel', $eligibility['open_request']) }}" data-confirm="Batalkan pengajuan untuk buku ini?">
                                        @csrf
                                        <flux:button type="submit" variant="danger" class="w-full">Batalkan Pengajuan</flux:button>
                                    </form>
                                @endcan
                            </div>
                        @endif

                        <flux:button variant="outline" href="{{ route('catalog.index') }}" class="mt-2 w-full">
                            Kembali ke Katalog
                        </flux:button>
                    @endif
                </div>
            </flux:card>
        </div>
    </div>
@endsection
