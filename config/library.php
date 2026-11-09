<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas Perpustakaan
    |--------------------------------------------------------------------------
    |
    | Nilai ini dipakai pada layout, halaman login, laporan, dan halaman cetak.
    | Ubah melalui .env tanpa perlu mengubah kode Blade.
    |
    */

    'name' => env('LIBRARY_NAME', 'Perpustakaan Sekolah'),

    'school_name' => env('SCHOOL_NAME', 'SMA Negeri 1'),

    'tagline' => env('LIBRARY_TAGLINE', 'Membaca hari ini, berilmu sepanjang masa.'),

    'default_cover' => 'images/book-placeholder.svg',

    'logo' => 'images/logo.svg',
];
