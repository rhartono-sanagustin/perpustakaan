@extends('layouts.admin')

@section('title', 'Identitas Sekolah')
@section('breadcrumb', 'Manajemen / Pengaturan / Identitas Sekolah')

@section('content')
    <x-page-header
        title="Identitas Sekolah"
        subtitle="Atur nama dan logo yang tampil di sidebar, halaman login, dan halaman publik."
    />

    @include('admin.settings._nav')

    <flux:callout icon="information-circle" class="mb-4">
        <flux:callout.heading>Perubahan langsung tampil</flux:callout.heading>
        <flux:callout.text>Nama sekolah, nama perpustakaan, tagline, dan logo dipakai di seluruh halaman aplikasi.</flux:callout.text>
    </flux:callout>

    <x-form-errors />

    <form method="POST" action="{{ route('admin.settings.identity.update') }}" enctype="multipart/form-data" data-loading>
        @csrf
        @method('PUT')

        <div class="grid gap-3 xl:grid-cols-12">
            <flux:card class="p-0! xl:col-span-7">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Nama &amp; Tagline</flux:heading>
                </div>
                <div class="space-y-5 p-5">
                    <div>
                        <flux:field>
                            <flux:label for="school_name">Nama sekolah <span class="text-rose-600">*</span></flux:label>
                            <flux:input
                                type="text"
                                id="school_name"
                                name="school_name"
                                value="{{ old('school_name', $identity->schoolName()) }}"
                                maxlength="{{ config('library.identity_definitions.school_name.max', 120) }}"
                                required
                            />
                            <flux:description>{{ config('library.identity_definitions.school_name.description') }}</flux:description>
                            <flux:error name="school_name" />
                        </flux:field>
                        @include('admin.settings._updated-at', ['settingKey' => 'school_name'])
                    </div>

                    <div>
                        <flux:field>
                            <flux:label for="library_name">Nama perpustakaan <span class="text-rose-600">*</span></flux:label>
                            <flux:input
                                type="text"
                                id="library_name"
                                name="library_name"
                                value="{{ old('library_name', $identity->libraryName()) }}"
                                maxlength="{{ config('library.identity_definitions.library_name.max', 100) }}"
                                required
                            />
                            <flux:description>{{ config('library.identity_definitions.library_name.description') }}</flux:description>
                            <flux:error name="library_name" />
                        </flux:field>
                        @include('admin.settings._updated-at', ['settingKey' => 'library_name'])
                    </div>

                    <div>
                        <flux:field>
                            <flux:label for="tagline">Tagline</flux:label>
                            <flux:input
                                type="text"
                                id="tagline"
                                name="tagline"
                                value="{{ old('tagline', $identity->tagline()) }}"
                                maxlength="{{ config('library.identity_definitions.tagline.max', 160) }}"
                            />
                            <flux:description>{{ config('library.identity_definitions.tagline.description') }}</flux:description>
                            <flux:error name="tagline" />
                        </flux:field>
                        @include('admin.settings._updated-at', ['settingKey' => 'tagline'])
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-0! xl:col-span-5">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Logo Sekolah</flux:heading>
                </div>
                <div class="p-5">
                    <div class="mb-4 flex min-h-40 items-center justify-center rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700">
                        <img
                            src="{{ $identity->logoUrl() }}"
                            alt="Pratinjau logo {{ $identity->schoolName() }}"
                            class="max-h-[120px] max-w-full object-contain"
                        >
                    </div>

                    <div class="mb-4">
                        <flux:field>
                            <flux:label for="logo">Ganti logo</flux:label>
                            <flux:input
                                type="file"
                                id="logo"
                                name="logo"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            />
                            <flux:description>JPG, PNG, atau WEBP. Maksimal 1 MB. Disarankan gambar persegi atau transparan.</flux:description>
                            <flux:error name="logo" />
                        </flux:field>
                    </div>

                    @if ($identity->hasCustomLogo())
                        <flux:checkbox
                            id="remove_logo"
                            name="remove_logo"
                            value="1"
                            :checked="(bool) old('remove_logo')"
                            label="Hapus logo kustom dan gunakan logo bawaan"
                        />
                    @endif

                    @include('admin.settings._updated-at', ['settingKey' => 'logo_path'])
                </div>
            </flux:card>
        </div>

        <flux:card class="mt-4">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <flux:text>Perubahan identitas juga tercatat pada audit log.</flux:text>
                <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                    Simpan Identitas
                </flux:button>
            </div>
        </flux:card>
    </form>
@endsection
