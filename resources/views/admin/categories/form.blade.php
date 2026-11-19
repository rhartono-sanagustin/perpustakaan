@extends('layouts.admin')

@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('breadcrumb', 'Kategori / Form')

@section('content')
    <x-page-header
        :title="$category->exists ? 'Edit Kategori' : 'Tambah Kategori'"
        :subtitle="$category->exists ? 'Perbarui nama dan deskripsi kategori.' : 'Buat kategori baru untuk koleksi perpustakaan.'"
    >
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.categories.index') }}">
                Kembali
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Periksa kembali data berikut:" />

    <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" data-loading>
        @csrf
        @method($category->exists ? 'PUT' : 'POST')

        <div class="grid gap-4 xl:grid-cols-3">
            <flux:card class="p-0! xl:col-span-2">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Data Kategori</flux:heading>
                </div>
                <div class="space-y-5 p-5">
                    <flux:field>
                        <flux:label for="name">Nama kategori <span class="text-rose-600">*</span></flux:label>
                        <flux:input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" maxlength="100" required autofocus />
                        @if ($category->exists)
                            <flux:description>Slug saat ini: <code class="rounded bg-zinc-100 px-1.5 py-0.5 text-xs dark:bg-white/10">{{ $category->slug }}</code></flux:description>
                        @endif
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="description">Deskripsi</flux:label>
                        <flux:textarea id="description" name="description" rows="5" maxlength="1000">{{ old('description', $category->description) }}</flux:textarea>
                        <flux:error name="description" />
                    </flux:field>
                </div>
            </flux:card>

            <div class="space-y-4">
                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Status</flux:heading>
                    </div>
                    <div class="p-5">
                        <input type="hidden" name="is_active" value="0">
                        <flux:switch
                            id="is_active"
                            name="is_active"
                            value="1"
                            :checked="(bool) old('is_active', $category->is_active ?? true)"
                            label="Aktif"
                            description="Kategori nonaktif tidak muncul sebagai pilihan filter katalog publik."
                        />
                        <flux:error name="is_active" />
                    </div>
                </flux:card>

                <flux:card>
                    <div class="flex flex-col gap-2">
                        <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                            Simpan
                        </flux:button>
                        <flux:button variant="outline" href="{{ route('admin.categories.index') }}">
                            Batal
                        </flux:button>
                    </div>
                </flux:card>
            </div>
        </div>
    </form>
@endsection
