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

    /*
    |--------------------------------------------------------------------------
    | Nilai Awal Kebijakan Sirkulasi
    |--------------------------------------------------------------------------
    |
    | Nilai ini dipakai sebagai fallback apabila baris library_settings belum
    | tersedia. Admin dapat mengubah kebijakan aktif melalui tabel pengaturan.
    |
    */

    'loan_defaults' => [
        'max_loans_siswa' => 2,
        'max_loans_guru' => 5,
        'loan_duration_siswa' => 7,
        'loan_duration_guru' => 14,
        'pickup_deadline_days' => 1,
    ],
];
