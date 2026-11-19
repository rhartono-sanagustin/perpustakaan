@extends('layouts.admin')

@section('title', $slide->exists ? 'Edit Slide Carousel' : 'Tambah Slide Carousel')
@section('breadcrumb', 'Manajemen / Pengaturan / Carousel Beranda / ' . ($slide->exists ? 'Edit' : 'Tambah'))

@section('content')
    <x-page-header
        :title="$slide->exists ? 'Edit Slide Carousel' : 'Tambah Slide Carousel'"
        subtitle="Atur gambar, teks, tautan, dan status tampil slide di landing page."
    />

    @include('admin.settings._nav')

    <x-form-errors />

    <form
        method="POST"
        action="{{ $slide->exists ? route('admin.settings.carousel.update', $slide) : route('admin.settings.carousel.store') }}"
        enctype="multipart/form-data"
        data-loading
    >
        @csrf
        @if ($slide->exists)
            @method('PUT')
        @endif

        <div class="grid gap-4 xl:grid-cols-2">
            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Gambar Slide</flux:heading>
                </div>
                <div class="p-5">
                    @if ($slide->exists)
                        <div class="mb-4 flex min-h-48 items-center justify-center rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700">
                            <img
                                src="{{ $slide->imageUrl() }}"
                                alt="Pratinjau slide {{ $slide->title ?: 'carousel' }}"
                                class="max-h-[260px] max-w-full object-contain"
                            >
                        </div>
                    @endif

                    <flux:field>
                        <flux:label for="image">Gambar <span class="text-rose-600">*</span></flux:label>
                        <flux:input
                            type="file"
                            id="image"
                            name="image"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            :required="! $slide->exists"
                        />
                        <flux:description>
                            JPG, PNG, atau WEBP. Maksimal 2 MB. Rasio yang disarankan 16:9, misalnya 1600 &times; 900 piksel.
                            @if ($slide->exists)
                                Kosongkan jika tidak ingin mengganti gambar.
                            @endif
                        </flux:description>
                        <flux:error name="image" />
                    </flux:field>

                    <div class="mt-5">
                        <flux:checkbox
                            id="is_active"
                            name="is_active"
                            value="1"
                            :checked="(bool) old('is_active', $slide->is_active)"
                            label="Tampilkan slide di beranda"
                            description="Maksimal {{ config('library.carousel.max_active', 5) }} slide dapat aktif pada waktu bersamaan."
                        />
                        <flux:error name="is_active" />
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Detail Slide</flux:heading>
                </div>
                <div class="space-y-5 p-5">
                    <flux:field>
                        <flux:label for="title">Judul</flux:label>
                        <flux:input type="text" id="title" name="title" value="{{ old('title', $slide->title) }}" maxlength="150" />
                        <flux:error name="title" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="caption">Keterangan</flux:label>
                        <flux:textarea id="caption" name="caption" rows="3" maxlength="500">{{ old('caption', $slide->caption) }}</flux:textarea>
                        <flux:error name="caption" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="alt_text">Teks alternatif gambar</flux:label>
                        <flux:input type="text" id="alt_text" name="alt_text" value="{{ old('alt_text', $slide->alt_text) }}" maxlength="255" />
                        <flux:description>Dibacakan pembaca layar. Kosongkan jika gambar dekoratif.</flux:description>
                        <flux:error name="alt_text" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="link_url">Tautan</flux:label>
                        <flux:input type="text" id="link_url" name="link_url" value="{{ old('link_url', $slide->link_url) }}" maxlength="2048" placeholder="/katalog atau https://contoh.sch.id" />
                        <flux:description>Gunakan path internal yang diawali <code>/</code> atau URL <code>http/https</code>.</flux:description>
                        <flux:error name="link_url" />
                    </flux:field>
                </div>
            </flux:card>
        </div>

        <flux:card class="mt-4">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.settings.carousel.index') }}">
                    Kembali
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                    Simpan Slide
                </flux:button>
            </div>
        </flux:card>
    </form>
@endsection
