<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas Perpustakaan
    |--------------------------------------------------------------------------
    |
    | Nilai ini dipakai sebagai fallback pada layout, halaman login, laporan,
    | dan halaman cetak. Admin dapat menimpanya melalui pengaturan identitas.
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

    /*
    |--------------------------------------------------------------------------
    | Definisi Pengaturan yang Dapat Diubah Admin
    |--------------------------------------------------------------------------
    |
    | Kunci di sini dipakai oleh halaman pengaturan, validasi, dan tampilan.
    | Pengaturan denda belum ditampilkan karena belum diterapkan pada MVP.
    |
    */

    'setting_definitions' => [
        'max_loans_siswa' => [
            'label' => 'Maksimal pinjaman siswa',
            'description' => 'Jumlah maksimal eksemplar yang dapat dipinjam siswa pada waktu bersamaan.',
            'group' => 'Kuota Peminjaman',
            'min' => 1,
            'max' => 20,
            'suffix' => 'eksemplar',
        ],
        'max_loans_guru' => [
            'label' => 'Maksimal pinjaman guru',
            'description' => 'Jumlah maksimal eksemplar yang dapat dipinjam guru pada waktu bersamaan.',
            'group' => 'Kuota Peminjaman',
            'min' => 1,
            'max' => 50,
            'suffix' => 'eksemplar',
        ],
        'loan_duration_siswa' => [
            'label' => 'Durasi pinjaman siswa',
            'description' => 'Lama peminjaman siswa dihitung sejak tanggal checkout.',
            'group' => 'Durasi Peminjaman',
            'min' => 1,
            'max' => 90,
            'suffix' => 'hari',
        ],
        'loan_duration_guru' => [
            'label' => 'Durasi pinjaman guru',
            'description' => 'Lama peminjaman guru dihitung sejak tanggal checkout.',
            'group' => 'Durasi Peminjaman',
            'min' => 1,
            'max' => 90,
            'suffix' => 'hari',
        ],
        'pickup_deadline_days' => [
            'label' => 'Batas pengambilan permintaan',
            'description' => 'Tenggat pengambilan buku setelah pengajuan disetujui. Pengajuan yang terlewat dibatalkan otomatis.',
            'group' => 'Pengambilan Buku',
            'min' => 1,
            'max' => 30,
            'suffix' => 'hari',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Identitas Sekolah yang Dapat Diubah Admin
    |--------------------------------------------------------------------------
    |
    | Nilai awal tetap memakai kunci identitas di atas. Baris library_settings
    | dengan key yang sama akan menimpa nilai awal tersebut.
    |
    */

    'identity_definitions' => [
        'school_name' => [
            'label' => 'Nama sekolah',
            'description' => 'Nama sekolah yang tampil di sidebar, halaman login, dan halaman publik.',
            'max' => 120,
        ],
        'library_name' => [
            'label' => 'Nama perpustakaan',
            'description' => 'Nama perpustakaan yang tampil sebagai judul utama.',
            'max' => 100,
        ],
        'tagline' => [
            'label' => 'Tagline',
            'description' => 'Kalimat singkat yang tampil di landing page dan header cetak.',
            'max' => 160,
        ],
        'logo_path' => [
            'label' => 'Logo sekolah',
            'description' => 'Unggah logo JPG, PNG, atau WEBP. Kosongkan untuk memakai logo bawaan.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Penyimpanan Logo
    |--------------------------------------------------------------------------
    */

    'branding' => [
        'logo_max_kb' => 1024,
        'disk' => 'public',
        'path' => 'branding',
    ],

    /*
    |--------------------------------------------------------------------------
    | Carousel Landing Page
    |--------------------------------------------------------------------------
    */

    'carousel' => [
        'max_active' => 5,
        'image_max_kb' => 2048,
        'disk' => 'public',
        'path' => 'carousel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ekspor dan Impor Data
    |--------------------------------------------------------------------------
    |
    | Batas ini melindungi server dari berkas impor yang terlalu besar.
    | Format yang didukung: CSV (pemisah ; atau ,) dan Excel .xlsx.
    |
    */

    'imports' => [
        'max_rows' => 1000,
        'max_file_kb' => 5120,
        'error_limit' => 100,
    ],
];
