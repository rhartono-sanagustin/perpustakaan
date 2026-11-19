@extends('layouts.admin')

@section('title', 'Catat Pengembalian')
@section('breadcrumb', 'Sirkulasi / Pengembalian / Form')

@section('content')
    <x-page-header title="Catat Pengembalian" :subtitle="'#'.$loan->reference.' · '.$loan->copy->book->title">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.returns.index') }}">
                Kembali
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Pengembalian belum dapat dicatat:" />

    <div class="grid gap-4 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <form method="POST" action="{{ route('admin.loans.return.store', $loan) }}" data-confirm="Catat pengembalian buku ini? Status eksemplar akan diperbarui sesuai kondisi yang dipilih." data-loading>
                @csrf

                <flux:card class="mb-4 p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Kondisi Buku Saat Dikembalikan</flux:heading>
                    </div>
                    <div class="p-5">
                        <fieldset>
                            <legend class="mb-3 text-sm font-medium">Pilih kondisi <span class="text-rose-600">*</span></legend>

                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach ($conditions as $condition)
                                    <label for="condition-{{ $condition->value }}" class="flex h-full cursor-pointer items-start gap-3 rounded-lg border border-zinc-200 p-3 transition hover:border-[var(--color-accent)] has-checked:border-[var(--color-accent)] has-checked:bg-[color-mix(in_oklab,var(--color-accent),transparent_94%)] dark:border-zinc-700">
                                        <input
                                            type="radio"
                                            id="condition-{{ $condition->value }}"
                                            name="return_condition"
                                            value="{{ $condition->value }}"
                                            class="mt-1 size-4 accent-[var(--color-accent)]"
                                            @checked(old('return_condition', \App\Enums\ReturnCondition::Baik->value) === $condition->value)
                                            required
                                        >
                                        <span>
                                            <span class="font-semibold">{{ $condition->label() }}</span>
                                            <span class="mt-1 block text-xs text-zinc-500 dark:text-zinc-400">
                                                @switch($condition)
                                                    @case(\App\Enums\ReturnCondition::Baik)
                                                        Eksemplar kembali tersedia dan dapat dipinjam lagi.
                                                        @break
                                                    @case(\App\Enums\ReturnCondition::RusakRingan)
                                                        Eksemplar ditandai rusak ringan dan tidak otomatis tersedia.
                                                        @break
                                                    @case(\App\Enums\ReturnCondition::RusakBerat)
                                                        Eksemplar ditandai rusak berat dan perlu pemeriksaan.
                                                        @break
                                                    @case(\App\Enums\ReturnCondition::Hilang)
                                                        Eksemplar ditandai hilang dan tidak dapat dipinjam.
                                                        @break
                                                @endswitch
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <flux:error name="return_condition" class="mt-2" />
                        </fieldset>

                        <div class="mt-4">
                            <flux:field>
                                <flux:label for="return_notes">Catatan pengembalian</flux:label>
                                <flux:textarea id="return_notes" name="return_notes" rows="3" maxlength="1000" placeholder="Contoh: sampul lepas pada bagian belakang.">{{ old('return_notes') }}</flux:textarea>
                                <flux:description>Catatan tidak wajib, tetapi sangat membantu petugas inventaris saat menindaklanjuti kerusakan.</flux:description>
                                <flux:error name="return_notes" />
                            </flux:field>
                        </div>
                    </div>
                </flux:card>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <flux:button type="submit" variant="primary" icon="check-circle" data-loading-text="Menyimpan pengembalian...">
                        Simpan Pengembalian
                    </flux:button>
                    <flux:button variant="outline" href="{{ route('admin.returns.index') }}">Batal</flux:button>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Ringkasan Pinjaman</flux:heading>
                </div>
                <div class="p-5">
                    <div class="mb-4 flex gap-3">
                        <img src="{{ $loan->copy->book->cover_url }}" alt="Sampul {{ $loan->copy->book->title }}" class="h-[90px] w-16 rounded-md object-cover">
                        <div class="min-w-0">
                            <div class="font-semibold">{{ $loan->copy->book->title }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->book->authors_label ?: 'Penulis tidak diketahui' }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->inventory_code }}</div>
                        </div>
                    </div>

                    <dl class="grid gap-2 text-sm">
                        <div class="flex justify-between gap-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">Peminjam</dt>
                            <dd class="text-end">{{ $loan->user->name }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">Nomor induk</dt>
                            <dd class="text-end">{{ $loan->user->nomor_induk }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">Dipinjam</dt>
                            <dd class="text-end">{{ $loan->borrowed_at->locale('id')->translatedFormat('d M Y') }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">Jatuh tempo</dt>
                            <dd class="text-end">{{ $loan->due_at->locale('id')->translatedFormat('d M Y') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">Status</dt>
                            <dd class="text-end">
                                <x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" :icon="$loan->isOverdue() ? 'exclamation-triangle' : 'arrow-left-right'" />
                            </dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">Kondisi eksemplar</dt>
                            <dd class="text-end">{{ $loan->copy->condition->label() }}</dd>
                        </div>
                    </dl>

                    @if ($loan->isOverdue())
                        <flux:callout variant="warning" icon="exclamation-triangle" class="mt-3">
                            <flux:callout.text>Pinjaman ini terlambat {{ $loan->daysOverdue() }} hari.</flux:callout.text>
                        </flux:callout>
                    @endif
                </div>
            </flux:card>

            <flux:callout icon="information-circle">
                <flux:callout.heading>Setelah pengembalian</flux:callout.heading>
                <flux:callout.text>
                    Transaksi ditutup dan tidak dapat dikembalikan lagi. Eksemplar dengan kondisi Baik menjadi Tersedia; kondisi rusak atau hilang akan ditandai pada inventaris.
                </flux:callout.text>
            </flux:callout>
        </div>
    </div>
@endsection
