@extends('layouts.admin')

@section('title', $announcement->exists ? 'Edit Pengumuman' : 'Tambah Pengumuman')
@section('breadcrumb', 'Pengumuman / Form')

@section('content')
    <x-page-header
        :title="$announcement->exists ? 'Edit Pengumuman' : 'Tambah Pengumuman'"
        :subtitle="$announcement->exists ? 'Perbarui isi, audiens, atau jadwal terbit pengumuman.' : 'Buat informasi baru untuk dashboard guru dan siswa.'"
    >
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.announcements.index') }}">
                Kembali
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Periksa kembali data berikut:" />

    <form method="POST" action="{{ $announcement->exists ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}" data-loading>
        @csrf
        @method($announcement->exists ? 'PUT' : 'POST')

        <div class="grid gap-4 xl:grid-cols-3">
            <div class="space-y-4 xl:col-span-2">
                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Isi Pengumuman</flux:heading>
                    </div>
                    <div class="space-y-5 p-5">
                        <flux:field>
                            <flux:label for="title">Judul <span class="text-rose-600">*</span></flux:label>
                            <flux:input type="text" id="title" name="title" value="{{ old('title', $announcement->title) }}" maxlength="150" required autofocus />
                            <flux:error name="title" />
                        </flux:field>

                        <flux:field>
                            <flux:label for="body">Isi pengumuman <span class="text-rose-600">*</span></flux:label>
                            <flux:textarea id="body" name="body" rows="8" maxlength="5000" required>{{ old('body', $announcement->body) }}</flux:textarea>
                            <flux:description>Gunakan teks biasa. Baris baru akan ditampilkan apa adanya; HTML tidak dijalankan.</flux:description>
                            <flux:error name="body" />
                        </flux:field>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <flux:field>
                                <flux:label for="audience">Audiens <span class="text-rose-600">*</span></flux:label>
                                <flux:select id="audience" name="audience" required>
                                    @foreach ($audiences as $audienceOption)
                                        <option value="{{ $audienceOption->value }}" @selected(old('audience', $announcement->audience?->value) === $audienceOption->value)>{{ $audienceOption->label() }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="audience" />
                            </flux:field>

                            <flux:field>
                                <flux:label for="published_at">Tanggal terbit</flux:label>
                                <flux:input type="datetime-local" id="published_at" name="published_at" value="{{ old('published_at', $announcement->published_at?->format('Y-m-d\TH:i')) }}" />
                                <flux:description>Kosongkan untuk menerbitkan segera saat status aktif.</flux:description>
                                <flux:error name="published_at" />
                            </flux:field>
                        </div>
                    </div>
                </flux:card>

                @if ($announcement->exists)
                    <flux:card>
                        <flux:text>
                            Dibuat oleh {{ $announcement->creator?->name ?? '-' }} pada
                            {{ $announcement->created_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}.
                        </flux:text>
                    </flux:card>
                @endif
            </div>

            <flux:card class="h-fit p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Publikasi</flux:heading>
                </div>
                <div class="p-5">
                    <input type="hidden" name="is_active" value="0">
                    <flux:switch
                        id="is_active"
                        name="is_active"
                        value="1"
                        :checked="(bool) old('is_active', $announcement->is_active ?? true)"
                        label="Aktif"
                        description="Pengumuman aktif tampil di dashboard pengguna sesuai audiens, maksimal tiga pengumuman terbaru. Pengumuman dengan tanggal terbit di masa depan baru tampil setelah tanggal tersebut."
                    />
                    <flux:error name="is_active" />

                    <div class="mt-5 flex flex-col gap-2">
                        <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                            Simpan
                        </flux:button>
                        <flux:button variant="outline" href="{{ route('admin.announcements.index') }}">
                            Batal
                        </flux:button>
                    </div>
                </div>
            </flux:card>
        </div>
    </form>
@endsection
