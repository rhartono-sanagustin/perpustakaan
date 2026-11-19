@extends('layouts.admin')

@section('title', $author->exists ? 'Edit Penulis' : 'Tambah Penulis')
@section('breadcrumb', 'Penulis / Form')

@section('content')
    <x-page-header
        :title="$author->exists ? 'Edit Penulis' : 'Tambah Penulis'"
        :subtitle="$author->exists ? 'Perbarui data penulis.' : 'Tambahkan penulis baru ke basis data perpustakaan.'"
    >
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.authors.index') }}">
                Kembali
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Periksa kembali data berikut:" />

    <form method="POST" action="{{ $author->exists ? route('admin.authors.update', $author) : route('admin.authors.store') }}" data-loading>
        @csrf
        @method($author->exists ? 'PUT' : 'POST')

        <div class="grid gap-4 xl:grid-cols-3">
            <flux:card class="p-0! xl:col-span-2">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Data Penulis</flux:heading>
                </div>
                <div class="space-y-5 p-5">
                    <flux:field>
                        <flux:label for="name">Nama penulis <span class="text-rose-600">*</span></flux:label>
                        <flux:input type="text" id="name" name="name" value="{{ old('name', $author->name) }}" maxlength="150" required autofocus />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="bio">Biografi</flux:label>
                        <flux:textarea id="bio" name="bio" rows="6" maxlength="2000">{{ old('bio', $author->bio) }}</flux:textarea>
                        <flux:description>Ringkasan latar belakang penulis, maksimal 2.000 karakter.</flux:description>
                        <flux:error name="bio" />
                    </flux:field>
                </div>
            </flux:card>

            <flux:card class="h-fit">
                <div class="flex flex-col gap-2">
                    <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                        Simpan
                    </flux:button>
                    <flux:button variant="outline" href="{{ route('admin.authors.index') }}">
                        Batal
                    </flux:button>
                </div>
            </flux:card>
        </div>
    </form>
@endsection
