@extends('layouts.admin')

@section('title', $publisher->exists ? 'Edit Penerbit' : 'Tambah Penerbit')
@section('breadcrumb', 'Penerbit / Form')

@section('content')
    <x-page-header
        :title="$publisher->exists ? 'Edit Penerbit' : 'Tambah Penerbit'"
        :subtitle="$publisher->exists ? 'Perbarui data penerbit.' : 'Tambahkan penerbit baru ke basis data perpustakaan.'"
    >
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.publishers.index') }}">
                Kembali
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Periksa kembali data berikut:" />

    <form method="POST" action="{{ $publisher->exists ? route('admin.publishers.update', $publisher) : route('admin.publishers.store') }}" data-loading>
        @csrf
        @method($publisher->exists ? 'PUT' : 'POST')

        <div class="grid gap-4 xl:grid-cols-3">
            <flux:card class="p-0! xl:col-span-2">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Data Penerbit</flux:heading>
                </div>
                <div class="grid gap-5 p-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <flux:field>
                            <flux:label for="name">Nama penerbit <span class="text-rose-600">*</span></flux:label>
                            <flux:input type="text" id="name" name="name" value="{{ old('name', $publisher->name) }}" maxlength="150" required autofocus />
                            <flux:error name="name" />
                        </flux:field>
                    </div>

                    <div class="sm:col-span-2">
                        <flux:field>
                            <flux:label for="address">Alamat</flux:label>
                            <flux:input type="text" id="address" name="address" value="{{ old('address', $publisher->address) }}" maxlength="255" />
                            <flux:error name="address" />
                        </flux:field>
                    </div>

                    <div>
                        <flux:field>
                            <flux:label for="phone">Telepon</flux:label>
                            <flux:input type="text" id="phone" name="phone" value="{{ old('phone', $publisher->phone) }}" maxlength="30" />
                            <flux:error name="phone" />
                        </flux:field>
                    </div>

                    <div>
                        <flux:field>
                            <flux:label for="email">Email</flux:label>
                            <flux:input type="email" id="email" name="email" value="{{ old('email', $publisher->email) }}" maxlength="255" />
                            <flux:error name="email" />
                        </flux:field>
                    </div>
                </div>
            </flux:card>

            <flux:card class="h-fit">
                <div class="flex flex-col gap-2">
                    <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                        Simpan
                    </flux:button>
                    <flux:button variant="outline" href="{{ route('admin.publishers.index') }}">
                        Batal
                    </flux:button>
                </div>
            </flux:card>
        </div>
    </form>
@endsection
