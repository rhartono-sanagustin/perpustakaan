@extends('layouts.admin')

@section('title', 'Impor Buku')
@section('breadcrumb', 'Buku / Impor')

@section('content')
    <x-page-header title="Impor Buku" subtitle="Tambahkan atau perbarui data bibliografi buku secara massal dari CSV atau Excel.">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.books.index') }}">
                Kembali ke Buku
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid items-start gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <flux:card>
                <flux:heading size="lg" level="2">Unggah Berkas</flux:heading>
                <flux:text class="mt-1">
                    Baris yang valid tetap diimpor meskipun ada baris lain yang gagal. Impor ini hanya membuat data buku, bukan eksemplar.
                </flux:text>

                <form method="POST" action="{{ route('admin.books.import.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf

                    <flux:field>
                        <flux:label for="file">Berkas CSV atau Excel</flux:label>
                        <flux:input type="file" id="file" name="file" accept=".csv,.xlsx" required />
                        <flux:description>
                            Maksimal {{ number_format($maxFileKb / 1024, 1, ',', '.') }} MB dan
                            {{ number_format($maxRows, 0, ',', '.') }} baris data.
                            Untuk Excel, hanya sheet pertama yang dibaca.
                        </flux:description>
                        <flux:error name="file" />
                    </flux:field>

                    <input type="hidden" name="create_missing" value="0">
                    <flux:checkbox
                        name="create_missing"
                        value="1"
                        :checked="(bool) old('create_missing', true)"
                        label="Buat kategori, penulis, dan penerbit yang belum ada"
                        description="Jika dimatikan, baris dengan data master baru akan gagal dan dilaporkan."
                    />

                    <flux:button type="submit" variant="primary" icon="arrow-up-tray" data-loading-text="Mengimpor...">
                        Impor Sekarang
                    </flux:button>
                </form>
            </flux:card>

            @include('admin._import-report', ['report' => $report])
        </div>

        <div class="space-y-4">
            <flux:card>
                <flux:heading size="lg" level="2">Template</flux:heading>
                <flux:text class="mt-1">Unduh template, isi datanya, lalu unggah kembali.</flux:text>
                <div class="mt-3 flex flex-col gap-2">
                    <flux:button variant="outline" icon="arrow-down-tray" href="{{ route('admin.books.import.template', ['format' => 'csv']) }}">
                        Template CSV
                    </flux:button>
                    <flux:button variant="outline" icon="arrow-down-tray" href="{{ route('admin.books.import.template', ['format' => 'xlsx']) }}">
                        Template Excel
                    </flux:button>
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" level="2">Aturan Impor</flux:heading>
                <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-zinc-600 dark:text-zinc-300">
                    <li>Jika <strong>ID</strong> diisi, buku harus sudah ada dan akan diperbarui.</li>
                    <li>Jika ID kosong dan <strong>ISBN</strong> ditemukan, buku akan diperbarui.</li>
                    <li>Jika ID dan ISBN kosong, sistem selalu membuat buku baru.</li>
                    <li>Beberapa penulis dipisahkan dengan tanda <code>|</code>, misalnya <code>Andrea Hirata|Ahmad Fuadi</code>.</li>
                    <li>Kolom opsional yang kosong tidak menimpa nilai lama.</li>
                    <li>Buku baru belum memiliki eksemplar; tambahkan eksemplar agar dapat dipinjam.</li>
                </ul>
            </flux:card>

            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Kolom Berkas</flux:heading>
                </div>
                <div class="overflow-x-auto" data-scrollable-region>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th scope="col">Kolom</th>
                                <th scope="col">Wajib</th>
                                <th scope="col">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>ID</td><td>Tidak</td><td>Dipakai untuk memperbarui buku yang sudah ada.</td></tr>
                            <tr><td>Judul</td><td>Ya</td><td>Maksimal 255 karakter.</td></tr>
                            <tr><td>ISBN</td><td>Tidak</td><td>Unik; menjadi kunci pembaruan jika ID kosong.</td></tr>
                            <tr><td>Kategori</td><td>Ya</td><td>Dicocokkan berdasarkan nama; dapat dibuat otomatis.</td></tr>
                            <tr><td>Penulis</td><td>Ya</td><td>Dipisahkan <code>|</code>; minimal satu; dapat dibuat otomatis.</td></tr>
                            <tr><td>Penerbit</td><td>Tidak</td><td>Dicocokkan berdasarkan nama; dapat dibuat otomatis.</td></tr>
                            <tr><td>Tahun Terbit</td><td>Tidak</td><td>Antara 1000 dan tahun berjalan + 1.</td></tr>
                            <tr><td>Edisi</td><td>Tidak</td><td>Maksimal 100 karakter.</td></tr>
                            <tr><td>Bahasa</td><td>Tidak</td><td>Default <code>Indonesia</code> untuk buku baru.</td></tr>
                            <tr><td>Ringkasan</td><td>Tidak</td><td>Maksimal 5.000 karakter.</td></tr>
                            <tr><td>Status Katalog</td><td>Tidak</td><td><code>Aktif</code> atau <code>Arsip</code>; default Aktif.</td></tr>
                        </tbody>
                    </table>
                </div>
            </flux:card>
        </div>
    </div>
@endsection
