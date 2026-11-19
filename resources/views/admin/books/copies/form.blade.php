@extends('layouts.admin')

@section('title', $copy->exists ? 'Edit Eksemplar' : 'Tambah Eksemplar')
@section('breadcrumb', 'Buku / Eksemplar / Form')

@section('content')
    <x-page-header
        :title="$copy->exists ? 'Edit Eksemplar' : 'Tambah Eksemplar'"
        :subtitle="$book->title"
    >
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.books.copies.index', $book) }}">
                Kembali
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Periksa kembali data berikut:" />

    <form method="POST" action="{{ $copy->exists ? route('admin.books.copies.update', [$book, $copy]) : route('admin.books.copies.store', $book) }}" data-loading>
        @csrf
        @method($copy->exists ? 'PUT' : 'POST')

        <div class="grid gap-4 xl:grid-cols-3">
            <flux:card class="p-0! xl:col-span-2">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Data Eksemplar</flux:heading>
                </div>
                <div class="grid gap-5 p-5 sm:grid-cols-2">
                    <flux:field>
                        <flux:label for="inventory_code">Kode inventaris <span class="text-rose-600">*</span></flux:label>
                        <flux:input type="text" id="inventory_code" name="inventory_code" value="{{ old('inventory_code', $copy->inventory_code) }}" maxlength="50" required autofocus />
                        <flux:description>Kode harus unik untuk setiap eksemplar fisik.</flux:description>
                        <flux:error name="inventory_code" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="shelf_location">Lokasi rak</flux:label>
                        <flux:input type="text" id="shelf_location" name="shelf_location" value="{{ old('shelf_location', $copy->shelf_location) }}" maxlength="100" placeholder="Contoh: Rak A-1" />
                        <flux:error name="shelf_location" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="condition">Kondisi <span class="text-rose-600">*</span></flux:label>
                        <flux:select id="condition" name="condition" required>
                            @foreach ($conditions as $condition)
                                <option value="{{ $condition->value }}" @selected(old('condition', $copy->condition?->value) === $condition->value)>{{ $condition->label() }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="condition" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="status">Status <span class="text-rose-600">*</span></flux:label>
                        <flux:select id="status" name="status" required>
                            @foreach ($statuses as $copyStatus)
                                <option value="{{ $copyStatus->value }}" @selected(old('status', $copy->status?->value) === $copyStatus->value)>{{ $copyStatus->label() }}</option>
                            @endforeach
                        </flux:select>
                        <flux:description>Status Dipinjam hanya diatur otomatis melalui checkout pada fase sirkulasi.</flux:description>
                        <flux:error name="status" />
                    </flux:field>

                    <div class="sm:col-span-2">
                        <flux:field>
                            <flux:label for="notes">Catatan</flux:label>
                            <flux:textarea id="notes" name="notes" rows="4" maxlength="1000">{{ old('notes', $copy->notes) }}</flux:textarea>
                            <flux:error name="notes" />
                        </flux:field>
                    </div>
                </div>
            </flux:card>

            <div class="space-y-4">
                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Ringkasan Buku</flux:heading>
                    </div>
                    <div class="flex gap-3 p-5">
                        <img src="{{ $book->cover_url }}" alt="" class="h-[96px] w-[72px] shrink-0 rounded-md object-cover">
                        <div class="min-w-0">
                            <div class="font-semibold">{{ $book->title }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $book->authors_label }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $book->category?->name }}</div>
                        </div>
                    </div>
                </flux:card>

                <flux:card>
                    <div class="flex flex-col gap-2">
                        <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                            {{ $copy->exists ? 'Simpan Perubahan' : 'Simpan Eksemplar' }}
                        </flux:button>
                        <flux:button variant="outline" href="{{ route('admin.books.copies.index', $book) }}">
                            Batal
                        </flux:button>
                    </div>
                </flux:card>
            </div>
        </div>
    </form>
@endsection
