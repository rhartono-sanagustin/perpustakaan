@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('breadcrumb', 'Manajemen / Pengaturan')

@section('content')
    <x-page-header
        title="Pengaturan Sirkulasi"
        subtitle="Atur kuota, durasi peminjaman, dan batas pengambilan buku."
    />

    @include('admin.settings._nav')

    <flux:callout icon="information-circle" class="mb-4">
        <flux:callout.heading>Perubahan berlaku untuk transaksi baru</flux:callout.heading>
        <flux:callout.text>Pinjaman yang sudah berjalan tetap memakai durasi dan kuota yang tersimpan pada transaksi tersebut.</flux:callout.text>
    </flux:callout>

    <x-form-errors />

    <form method="POST" action="{{ route('admin.settings.update') }}" data-loading>
        @csrf
        @method('PUT')

        @php($groups = collect($definitions)->groupBy('group'))

        <div class="grid gap-3 xl:grid-cols-2">
            @foreach ($groups as $group => $items)
                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">{{ $group }}</flux:heading>
                    </div>
                    <div class="space-y-5 p-5">
                        @foreach ($items as $key => $definition)
                            @php($setting = $settings->get($key))
                            <div>
                                <flux:field>
                                    <flux:label for="{{ $key }}">{{ $definition['label'] }} <span class="text-rose-600">*</span></flux:label>
                                    <div class="flex items-center gap-2">
                                        <flux:input
                                            type="number"
                                            id="{{ $key }}"
                                            name="{{ $key }}"
                                            value="{{ old($key, $setting?->value ?? config('library.loan_defaults.'.$key)) }}"
                                            min="{{ $definition['min'] }}"
                                            max="{{ $definition['max'] }}"
                                            step="1"
                                            required
                                            class="max-w-32"
                                        />
                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $definition['suffix'] }}</span>
                                    </div>
                                    <flux:description>
                                        {{ $definition['description'] }} Rentang {{ $definition['min'] }}&ndash;{{ $definition['max'] }} {{ $definition['suffix'] }}.
                                    </flux:description>
                                    <flux:error name="{{ $key }}" />
                                </flux:field>

                                @if ($setting)
                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        Terakhir diperbarui {{ $setting->updated_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}
                                        @if ($setting->editor)
                                            oleh {{ $setting->editor->name }}
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Masih menggunakan nilai awal.</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @endforeach
        </div>

        <flux:card class="mt-4">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <flux:text>Pengaturan denda belum tersedia karena belum diterapkan pada MVP.</flux:text>
                <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                    Simpan Pengaturan
                </flux:button>
            </div>
        </flux:card>
    </form>
@endsection
