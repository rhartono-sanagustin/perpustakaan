@extends('layouts.admin')

@section('title', 'Impor Pengguna')
@section('breadcrumb', 'Manajemen / Pengguna / Impor')

@section('content')
    <x-page-header title="Impor Pengguna" subtitle="Tambahkan atau perbarui akun secara massal dari berkas CSV atau Excel.">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.users.index') }}">
                Kembali ke Pengguna
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid items-start gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <flux:card>
                <flux:heading size="lg" level="2">Unggah Berkas</flux:heading>
                <flux:text class="mt-1">
                    Gunakan template agar nama kolom sesuai. Baris yang valid tetap diimpor meskipun ada baris lain yang gagal.
                </flux:text>

                <form method="POST" action="{{ route('admin.users.import.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
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
                    <flux:button variant="outline" icon="arrow-down-tray" href="{{ route('admin.users.import.template', ['format' => 'csv']) }}">
                        Template CSV
                    </flux:button>
                    <flux:button variant="outline" icon="arrow-down-tray" href="{{ route('admin.users.import.template', ['format' => 'xlsx']) }}">
                        Template Excel
                    </flux:button>
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" level="2">Aturan Impor</flux:heading>
                <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-zinc-600 dark:text-zinc-300">
                    <li><strong>Nomor Induk</strong> menjadi kunci: data yang sudah ada akan diperbarui.</li>
                    <li>Kata sandi wajib diisi untuk akun baru, minimal 8 karakter.</li>
                    <li>Untuk akun lama, kolom kata sandi boleh kosong agar sandi lama tetap berlaku.</li>
                    <li>Kolom opsional yang kosong tidak menimpa nilai lama.</li>
                    <li>Akun admin yang sedang digunakan tidak dapat diubah melalui impor.</li>
                    <li>Gunakan format Teks di Excel untuk nomor induk agar nol di depan tidak hilang.</li>
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
                            <tr><td>Nama</td><td>Ya</td><td>Maksimal 255 karakter.</td></tr>
                            <tr><td>Nomor Induk</td><td>Ya</td><td>Unik; menjadi kunci pembaruan.</td></tr>
                            <tr><td>Peran</td><td>Ya</td><td><code>admin</code>, <code>guru</code>, atau <code>siswa</code>.</td></tr>
                            <tr><td>Status</td><td>Tidak</td><td><code>aktif</code> atau <code>nonaktif</code>; default aktif.</td></tr>
                            <tr><td>Email</td><td>Tidak</td><td>Harus valid dan unik.</td></tr>
                            <tr><td>Kelas</td><td>Tidak</td><td>Maksimal 50 karakter.</td></tr>
                            <tr><td>Unit Kerja</td><td>Tidak</td><td>Maksimal 100 karakter.</td></tr>
                            <tr><td>Nomor Telepon</td><td>Tidak</td><td>Maksimal 30 karakter.</td></tr>
                            <tr><td>Kata Sandi</td><td>Akun baru</td><td>Minimal 8 karakter; kosongkan untuk mempertahankan sandi lama.</td></tr>
                        </tbody>
                    </table>
                </div>
            </flux:card>
        </div>
    </div>
@endsection
