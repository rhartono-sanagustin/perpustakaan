# Sistem Informasi Perpustakaan Sekolah

Aplikasi perpustakaan sekolah berbasis Laravel 12, Flux UI, Livewire, dan Tailwind CSS 4 untuk mengelola katalog, pengajuan pinjaman, sirkulasi, laporan, kartu anggota, dan kunjungan perpustakaan. Seluruh antarmuka menggunakan Bahasa Indonesia, sidebar dengan grup menu yang dapat dilipat, serta pilihan tema terang/gelap/sistem.

## Fitur utama

- **Publik** — beranda dengan carousel, katalog buku, detail buku, pencarian/filter, tombol bagikan, dan kiosk kunjungan mandiri tanpa login.
- **Siswa dan guru** — login dengan email atau nomor induk, mengajukan pinjaman per judul, membatalkan pengajuan, memantau pinjaman aktif/terlambat, melihat riwayat, dan memperbarui profil.
- **Admin** — dashboard dengan grafik peminjaman dan kunjungan, CRUD buku/eksemplar/kategori/penulis/penerbit, antrean pengajuan, checkout, pengembalian, laporan, CSV, halaman cetak, pengguna, pengaturan, pengumuman, dan audit log.
- **Kartu anggota** — kartu ID-1 (85,6 × 54 mm) untuk guru dan siswa aktif dengan barcode Code 128 dari nomor induk; cetak tunggal atau massal hingga 8 kartu per lembar A4.
- **Kunjungan** — scan barcode kartu anggota, ringkasan pinjaman anggota, pencatatan kunjungan hari ini, pembatalan kunjungan hari ini, grafik kunjungan 7 hari, dan kiosk mandiri untuk pengunjung.
- **Tampilan** — Flux UI dengan sidebar accordion untuk semua peran, tema terang/gelap/sistem, komponen Blade yang konsisten, dan halaman kiosk/cetak yang selalu terang.
- **Keamanan** — middleware role/status aktif, policy kepemilikan transaksi, CSRF, rate limit login, security headers, validasi unggahan, dan audit log tindakan admin.

## Kebutuhan

- PHP 8.2 atau lebih baru dengan ekstensi `pdo_mysql` (atau `pdo_sqlite`) dan `mbstring`.
- Composer 2.
- Node.js 20+ dan npm.
- MySQL 8 (disarankan) atau SQLite.
- Browser modern (Chrome, Edge, Firefox, Safari).

## Instalasi

```bash
composer install
npm install
copy .env.example .env        # Windows
# cp .env.example .env        # Linux/macOS
php artisan key:generate
```

### Opsi A — MySQL (konfigurasi lokal proyek)

Buat database `perpustakaan`, lalu sesuaikan `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan
DB_USERNAME=root
DB_PASSWORD=
```

### Opsi B — SQLite

`.env.example` sudah memakai SQLite. Buat file database bila belum ada:

```bash
type nul > database/database.sqlite   # Windows
# touch database/database.sqlite      # Linux/macOS
```

### Migrasi, seed, dan aset

```bash
php artisan storage:link
php artisan migrate --seed
npm run build
```

`php artisan migrate --seed` membuat akun demo, data master, dan transaksi contoh.

## Akun demo

Semua akun memakai kata sandi **`password`**.

| Peran | Nama | Email | Nomor induk | Status |
|---|---|---|---|---|
| Admin | Administrator Perpustakaan | `admin@perpus.test` | `ADM001` | Aktif |
| Guru | Budi Santoso | `guru@perpus.test` | `NIP001` | Aktif |
| Siswa | Siti Aminah | `siswa@perpus.test` | `NIS001` | Aktif |
| Siswa | Akun Nonaktif | `nonaktif@perpus.test` | `NIS002` | Nonaktif |

Login dapat memakai email **atau** nomor induk. Akun nonaktif tidak dapat login dan tidak dapat mengajukan pinjaman.

## Menjalankan aplikasi

Jalankan tiga proses berikut di terminal terpisah:

```bash
php artisan serve
npm run dev
php artisan queue:listen
```

Buka `http://localhost:8000`.

> **Windows:** jangan memakai `composer dev` karena perintah tersebut menjalankan `php artisan pail`, yang membutuhkan ekstensi `pcntl` dan gagal di Windows. Gunakan `Get-Content storage/logs/laravel.log -Wait` untuk memantau log.

## Pengujian

```bash
php artisan test
php artisan test --filter=MvpFlowTest
php vendor/bin/pint --dirty
npm run build
php artisan view:cache
```

Test memakai database MySQL khusus **`perpustakaan_test`** yang dikonfigurasi di `phpunit.xml`. Jangan mengarahkan `RefreshDatabase` ke database dev. Untuk menjalankan satu file:

```bash
php artisan test tests/Feature/MvpFlowTest.php
```

## Cetak kartu anggota

1. Buka **Manajemen → Kartu Anggota**.
2. Pilih filter peran/kelas, lalu **Cetak Massal** (maksimal 200 anggota per proses) atau **Cetak** pada baris anggota.
3. Gunakan pengaturan cetak **A4 portrait, skala 100%**, dan aktifkan **Background graphics** bila browser meminta.
4. Setiap lembar memuat maksimal 8 kartu (2 × 4). Halaman berikutnya dimulai otomatis.
5. Barcode memakai Code 128 dan dapat dipindai dengan scanner USB yang mengirim Enter. Nomor induk yang dicetak sebagai barcode dibatasi 20 karakter agar modul barcode tetap terbaca; anggota dengan nomor induk lebih panjang akan dilewati dengan peringatan.

Kunjungan dicatat di **Transaksi → Kunjungan**. Scan kartu pada kolom nomor induk. Scan berulang dalam 30 menit untuk anggota yang sama diabaikan. Hanya kunjungan hari ini yang dapat dibatalkan.

## Kiosk kunjungan mandiri

Kiosk publik tersedia di `http://localhost:8000/kunjungan` dan dapat dibuka dari tautan **Kunjungan** atau kartu ajakan di beranda.

1. Buka **Manajemen → Pengaturan → Kunjungan**.
2. Aktifkan atau nonaktifkan **Kiosk kunjungan mandiri**.
3. Saat aktif, pengunjung dapat memindai barcode kartu anggota atau mengetik nomor induk tanpa login.
4. Konfirmasi menampilkan nama, peran, kelas/unit, dan waktu kunjungan. Data tersimpan dengan sumber **Mandiri** dan `recorded_by` kosong.
5. Hanya guru/siswa aktif yang diterima; akun admin dan akun nonaktif ditolak. Scan berulang dalam 30 menit dianggap kunjungan yang sama.
6. Halaman kiosk memakai `Cache-Control: no-store`, `X-Robots-Tag: noindex, nofollow`, dan rate limit 60 permintaan per menit.

Saat kiosk dinonaktifkan, halaman `/kunjungan` mengembalikan 404 dan tautan di beranda disembunyikan. Perubahan pengaturan dicatat di audit log sebagai `library_setting.updated`.

## Laporan dan cetak

- Enam laporan: ringkasan, peminjaman, pengembalian, keterlambatan, inventaris, dan buku populer.
- Filter periode default adalah bulan berjalan.
- Ekspor CSV memakai BOM UTF-8, delimiter `;`, dan perlindungan formula injection.
- Halaman cetak laporan memakai A4 landscape dengan identitas sekolah dan tanda tangan petugas.

## Tugas terjadwal

```bash
php artisan library:cleanup --dry-run
php artisan library:cleanup
php artisan schedule:work
```

`library:cleanup` membatalkan pengajuan disetujui yang melewati batas pengambilan. Schedule harian berjalan pukul 01.00 sesuai `routes/console.php`.

## Struktur dokumentasi

- `agents/prd.md` — kebutuhan produk, peran, aturan bisnis, dan cakupan MVP.
- `agents/design.md` — pedoman UI Flux, token tema, dan pola halaman.
- `agents/task-instruction.md` — alur kerja pengerjaan dan kategori checklist.
- `agents/tasklist.md` — status pengerjaan per fase.
- `agents/uat-checklist.md` — skenario UAT akhir dan lembar tanda tangan.

## Pemecahan masalah

| Gejala | Penyebab umum | Solusi |
|---|---|---|
| `No application encryption key` | `APP_KEY` kosong | `php artisan key:generate` |
| Logo hasil unggahan tidak tampil | Symlink storage belum dibuat | `php artisan storage:link` |
| Halaman memakai aset lama | Build Vite belum diperbarui | `npm run build` atau jalankan `npm run dev` |
| Test menyentuh database dev | `phpunit.xml` diubah | Pastikan `DB_DATABASE=perpustakaan_test` |
| `composer dev` gagal di Windows | `php artisan pail` membutuhkan `pcntl` | Jalankan `php artisan serve`, `npm run dev`, dan `php artisan queue:listen` terpisah |
| Barcode tidak terbaca | Cetak tidak 100% atau nomor induk terlalu panjang | Cetak A4 100%, gunakan nomor induk maksimal 20 karakter |
| Halaman `/kunjungan` 404 | Kiosk kunjungan dinonaktifkan admin | Buka **Pengaturan → Kunjungan**, aktifkan kiosk |

## Lisensi

Aplikasi ini dibangun di atas Laravel, Livewire, Flux UI, dan Tailwind CSS. Laravel, Livewire, Flux UI, dan Tailwind CSS berlisensi MIT.
