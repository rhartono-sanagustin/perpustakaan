# Checklist UAT — Sistem Informasi Perpustakaan Sekolah

**Versi dokumen:** 1.2  
**Terakhir diperbarui:** 19 November 2026  
**Cakupan:** MVP Fase 1–9 (autentikasi, katalog, sirkulasi, laporan, pengguna, pengaturan, pengumuman, notifikasi, kartu anggota, kunjungan, kiosk kunjungan mandiri, Flux UI, sidebar accordion, tema terang/gelap/sistem)  
**Lingkungan acuan:** Laravel 12.71, PHP 8.2.26, Flux UI + Livewire 4 + Tailwind CSS 4, MySQL `perpustakaan` (dev) / `perpustakaan_test` (test)

---

## 1. Tujuan

Dokumen ini dipakai untuk memverifikasi bahwa seluruh kebutuhan **Must** dan **Should** pada `agents/prd.md` berjalan sesuai harapan sebelum sistem dinyatakan siap digunakan secara terbatas di sekolah. UAT dilakukan oleh admin/pustakawan, perwakilan guru, perwakilan siswa, dan tim IT.

**Kriteria lulus:**

1. Seluruh skenario berprioritas **Must** berstatus **Lulus**.
2. Tidak ada defect **Critical** atau **High** yang terbuka.
3. Defect **Medium/Low** yang tersisa memiliki catatan workaround dan disetujui pemilik produk.
4. Lembar tanda tangan pada bagian 6 telah diisi.

---

## 2. Persiapan

### 2.1 Menyiapkan aplikasi

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Buka `http://localhost:8000`. Untuk pengujian cetak gunakan Chrome/Edge terbaru.

### 2.2 Akun demo

Semua akun memakai kata sandi **`password`**. Login dapat memakai email **atau** nomor induk.

| Peran | Nama | Email | Nomor induk | Status |
|---|---|---|---|---|
| Admin | Administrator Perpustakaan | `admin@perpus.test` | `ADM001` | Aktif |
| Guru | Budi Santoso | `guru@perpus.test` | `NIP001` | Aktif |
| Siswa | Siti Aminah | `siswa@perpus.test` | `NIS001` | Aktif |
| Siswa | Akun Nonaktif | `nonaktif@perpus.test` | `NIS002` | Nonaktif |

### 2.3 Perangkat dan data uji

- Browser desktop: Chrome/Edge/Firefox versi terbaru.
- Emulasi mobile: lebar 360 px dan 390 px.
- Scanner barcode USB (mode keyboard + Enter) atau aplikasi pemindai barcode pada ponsel.
- Data contoh dari `php artisan migrate --seed`; jangan memakai database produksi.
- Untuk pengujian kunjungan, siapkan minimal satu kartu anggota tercetak atau nomor induk anggota aktif.
- Untuk pengujian kiosk, buka `http://localhost:8000/kunjungan` di browser desktop dan mobile; pastikan kiosk aktif di **Pengaturan → Kunjungan**.

### 2.4 Cara mengisi checklist

Isi kolom **Status** dengan salah satu nilai:

- **Lulus** — hasil sesuai harapan.
- **Gagal** — hasil tidak sesuai; catat ID defect di bagian 7.
- **N/A** — skenario tidak relevan untuk sekolah; beri alasan di kolom Catatan.

---

## 3. Ringkasan verifikasi otomatis

Verifikasi berikut sudah dijalankan pada 16 November 2026 dan menjadi bukti pendukung UAT manual.

| Pemeriksaan | Perintah/metode | Hasil |
|---|---|---|
| Test suite lengkap | `php artisan test` | **185 test lulus, 1178 assertion** |
| Test E2E alur MVP | `php artisan test tests/Feature/MvpFlowTest.php` | 1 test, **102 assertion** (katalog → pengajuan → setujui → checkout → kembali → laporan/CSV → kartu → kunjungan → audit) |
| Test kiosk kunjungan | `php artisan test tests/Feature/VisitKioskTest.php tests/Feature/Admin/VisitSettingTest.php` | 14 test, menangkap alur publik, duplikat, kode tidak valid, toggle, audit, dan rate limit |
| Build produksi | `npm run build` | Berhasil, 64 modul |
| Kompilasi Blade | `php artisan view:cache` | Seluruh template berhasil |
| Format kode | `php vendor/bin/pint --dirty` | Lulus |
| QA responsif | Chrome DevTools Protocol, 5 halaman × 5 lebar (360/390/768/1024/1440) | **25 kombinasi, 0 horizontal overflow** |
| QA aksesibilitas | axe-core WCAG 2.1 A/AA pada 4 halaman × desktop/mobile | **0 pelanggaran** |
| QA kiosk | Chrome headless, autofocus, jam, submit `NIS001`, panel konfirmasi | Fokus kembali ke input; nama/kelas/waktu tampil; tanpa error console |
| Cetak kartu massal | PDF 10 kartu | **2 halaman A4 portrait**, 8 kartu/lembar |
| Keterbacaan barcode | Dekode PDF dengan zxing-cpp | **10/10 barcode terbaca sebagai Code 128** |
| Cetak laporan | PDF laporan peminjaman | **A4 landscape**, footer tanda tangan tampil |

---

## 4. Skenario UAT

### 4.1 Publik dan katalog

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-PUB-01 | Beranda dan identitas sekolah | Buka `/` tanpa login | Nama sekolah, nama perpustakaan, tagline, logo, carousel (bila ada), dan tautan katalog tampil; tidak ada error console | ⬜ | |
| UAT-PUB-02 | Carousel beranda | Amati perpindahan slide, klik jeda/putar, lalu buka slide bertautan eksternal | Hanya slide aktif yang tampil berurutan; jeda/putar bekerja; tautan eksternal terbuka di tab baru | ⬜ | |
| UAT-PUB-03 | Pencarian katalog | Cari judul, nama penulis, dan ISBN | Hasil sesuai kata kunci, tidak membedakan huruf besar/kecil; empty state tampil bila tidak ada hasil | ⬜ | |
| UAT-PUB-04 | Filter katalog | Pilih kategori dan ketersediaan, lalu reset | Filter dapat digabung; hasil berubah otomatis; tombol reset mengembalikan seluruh hasil | ⬜ | |
| UAT-PUB-05 | Detail buku dan bagikan | Buka detail buku, klik **Bagikan** | Metadata, ringkasan, lokasi rak, dan ketersediaan tampil; tautan tersalin atau dialog share muncul; toast sukses tampil | ⬜ | |
| UAT-PUB-06 | Proteksi halaman internal | Buka `/admin/dashboard` dan `/pinjaman` tanpa login | Dialihkan ke halaman login; setelah login diarahkan ke dashboard sesuai peran | ⬜ | |

### 4.2 Autentikasi dan keamanan akun

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-AUTH-01 | Login admin dengan email | Login `admin@perpus.test` / `password` | Masuk ke dashboard admin; menu admin tampil | ⬜ | |
| UAT-AUTH-02 | Login siswa dengan nomor induk | Login `NIS001` / `password` | Masuk ke dashboard peminjam; menu admin tidak tampil | ⬜ | |
| UAT-AUTH-03 | Password salah dan rate limit | Coba password salah 5 kali berturut-turut | Pesan gagal tampil; percobaan berikutnya dibatasi sementara | ⬜ | |
| UAT-AUTH-04 | Akun nonaktif | Login `nonaktif@perpus.test` / `password` | Login ditolak dengan pesan akun tidak aktif | ⬜ | |
| UAT-AUTH-05 | Logout | Klik logout, lalu tekan tombol back browser | Sesi berakhir; halaman terlindungi tidak dapat dibuka kembali tanpa login | ⬜ | |
| UAT-AUTH-06 | Siswa membuka URL admin | Login siswa, buka `/admin/users` langsung | Ditolak (403) dan tidak ada data admin yang tampil | ⬜ | |
| UAT-AUTH-07 | Admin membuka halaman peminjam | Login admin, buka `/pinjaman` | Ditolak/dialihkan; admin tidak memakai tampilan peminjam | ⬜ | |
| UAT-AUTH-08 | Perbarui profil peminjam | Ubah nama, telepon, dan kata sandi; login ulang | Perubahan tersimpan; kata sandi lama tidak berlaku; email/nomor induk tetap tervalidasi | ⬜ | |

### 4.3 Peminjam (siswa dan guru)

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-BRW-01 | Dashboard peminjam | Login siswa | Pengumuman aktif (maks. 3), ringkasan pinjaman, jatuh tempo, dan status terlambat tampil | ⬜ | |
| UAT-BRW-02 | Ajukan peminjaman | Dari detail buku klik **Ajukan Pinjam**, isi catatan, kirim | Pengajuan berstatus **Menunggu**; belum ada transaksi pinjaman; eksemplar belum ditetapkan | ⬜ | |
| UAT-BRW-03 | Kuota dan pengajuan ganda | Ajukan buku yang sama dua kali, lalu ajukan melebihi kuota | Pengajuan ganda dan pengajuan melebihi kuota ditolak dengan pesan jelas | ⬜ | |
| UAT-BRW-04 | Batalkan pengajuan | Batalkan pengajuan berstatus Menunggu | Status menjadi **Dibatalkan**; kuota terbebas; pengajuan milik orang lain tidak dapat dibatalkan | ⬜ | |
| UAT-BRW-05 | Pinjaman aktif dan terlambat | Lihat halaman pinjaman setelah checkout; periksa pinjaman yang melewati jatuh tempo | Tanggal pinjam, jatuh tempo, sisa hari, dan penanda **Terlambat** tampil benar | ⬜ | |
| UAT-BRW-06 | Riwayat milik sendiri | Buka riwayat; coba akses transaksi pengguna lain lewat URL | Hanya transaksi milik akun sendiri yang tampil; akses transaksi lain ditolak | ⬜ | |
| UAT-BRW-07 | Kebijakan guru | Checkout untuk akun guru | Kuota 5 eksemplar dan durasi 14 hari berlaku; nilai tersimpan sebagai snapshot transaksi | ⬜ | |

### 4.4 Admin — katalog dan master data

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-CAT-01 | Tambah buku dan sampul | Tambah buku lengkap dengan sampul JPG/PNG/WEBP | Buku tersimpan dan tampil di katalog; sampul tampil atau placeholder bila kosong | ⬜ | |
| UAT-CAT-02 | Validasi buku | Kosongkan field wajib, pakai ISBN duplikat, unggah file >2 MB | Validasi menolak dengan pesan Bahasa Indonesia; tidak ada data tersimpan | ⬜ | |
| UAT-CAT-03 | Kelola eksemplar | Tambah beberapa eksemplar dengan kode inventaris berbeda | Setiap eksemplar memiliki kode unik, rak, kondisi, dan status sendiri; jumlah tersedia bertambah | ⬜ | |
| UAT-CAT-04 | Proteksi status eksemplar | Coba set status **Dipinjam** manual; ubah eksemplar yang sedang dipinjam | Ditolak dengan pesan jelas; status hanya berubah melalui checkout/pengembalian | ⬜ | |
| UAT-CAT-05 | Arsip dan nonaktifkan | Arsipkan buku; hapus eksemplar yang memiliki riwayat | Buku menghilang dari katalog tetapi riwayat tetap ada; eksemplar bertransaksi dinonaktifkan, bukan dihapus | ⬜ | |
| UAT-CAT-06 | Master data | Tambah/edit kategori, penulis, penerbit; coba hapus data yang dipakai buku | Data dapat dipakai ulang; data yang masih terhubung tidak dihapus permanen | ⬜ | |

### 4.5 Admin — sirkulasi

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-CIR-01 | Antrean pengajuan | Buka antrean, filter status/tanggal/pengguna | Daftar dan filter bekerja; detail pengajuan menampilkan peminjam dan buku | ⬜ | |
| UAT-CIR-02 | Setujui pengajuan | Setujui pengajuan Menunggu | Status menjadi **Disetujui**; stok tertahan; belum ada pinjaman dan eksemplar belum berubah | ⬜ | |
| UAT-CIR-03 | Tolak pengajuan | Tolak tanpa alasan, lalu dengan alasan | Penolakan tanpa alasan ditolak validasi; dengan alasan tersimpan dan status menjadi **Ditolak** | ⬜ | |
| UAT-CIR-04 | Checkout | Pilih eksemplar tersedia dan centang konfirmasi penyerahan | Pinjaman dibuat; eksemplar menjadi **Dipinjam**; jatuh tempo, durasi, dan kuota tersimpan | ⬜ | |
| UAT-CIR-05 | Pengajuan kedaluwarsa | Lewati batas pengambilan, lalu coba checkout | Checkout diblokir; pengajuan ditandai kedaluwarsa/dibatalkan | ⬜ | |
| UAT-CIR-06 | Pengembalian baik | Catat pengembalian dengan kondisi **Baik** | Transaksi menjadi **Dikembalikan**; eksemplar kembali **Tersedia**; riwayat diperbarui | ⬜ | |
| UAT-CIR-07 | Pengembalian rusak/hilang | Catat pengembalian kondisi **Rusak Ringan**, **Rusak Berat**, atau **Hilang** | Status/kondisi eksemplar berubah sesuai pilihan; eksemplar tidak dihitung tersedia | ⬜ | |
| UAT-CIR-08 | Pengembalian ganda | Buka transaksi yang sudah dikembalikan, coba kembalikan lagi | Aksi ditolak; tidak ada perubahan status ganda | ⬜ | |
| UAT-CIR-09 | Riwayat transaksi | Buka riwayat, cari peminjam/judul, filter | Riwayat lengkap dengan tanggal pinjam, kembali, kondisi, dan pemroses | ⬜ | |

### 4.6 Admin — laporan dan dashboard

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-REP-01 | Enam laporan | Buka ringkasan, peminjaman, pengembalian, keterlambatan, inventaris, buku populer | Semua laporan terbuka; periode default bulan berjalan | ⬜ | |
| UAT-REP-02 | Filter laporan | Ubah periode, kategori, peran, status/kondisi, lalu terapkan | Angka dan tabel mengikuti filter; filter tetap aktif saat pindah halaman laporan | ⬜ | |
| UAT-REP-03 | Ekspor CSV | Ekspor laporan yang sudah difilter, buka di Excel | File terunduh; BOM UTF-8 dan delimiter `;` benar; data sesuai filter; sel berbahaya tidak dieksekusi | ⬜ | |
| UAT-REP-04 | Cetak laporan | Klik cetak, simpan sebagai PDF | A4 landscape; judul, periode, tanggal cetak, identitas sekolah, tabel, dan tanda tangan tampil | ⬜ | |
| UAT-REP-05 | Dashboard admin | Buka dashboard | KPI judul/eksemplar/pinjaman/keterlambatan, grafik pinjaman 7 hari, dan grafik kunjungan 7 hari tampil | ⬜ | |

### 4.7 Admin — pengguna dan pengaturan

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-USR-01 | Buat pengguna | Tambah admin/guru/siswa dengan nomor induk unik | Akun tersimpan; kata sandi ter-hash; nomor induk duplikat ditolak | ⬜ | |
| UAT-USR-02 | Edit dan reset kata sandi | Ubah data pengguna; reset kata sandi | Perubahan tersimpan dan tercatat; kata sandi baru tidak ditampilkan di antarmuka | ⬜ | |
| UAT-USR-03 | Aktif/nonaktif | Nonaktifkan akun, lalu coba login | Akun nonaktif tidak dapat login dan tidak dapat mengajukan pinjaman | ⬜ | |
| UAT-USR-04 | Hapus akun | Hapus akun tanpa riwayat; hapus akun dengan pinjaman/kunjungan/audit | Akun tanpa riwayat terhapus; akun dengan riwayat dinonaktifkan agar audit tetap utuh | ⬜ | |
| UAT-USR-05 | Akun sendiri | Coba edit/reset/nonaktifkan/hapus akun admin yang sedang login | Aksi ditolak; admin tidak dapat mengunci dirinya sendiri | ⬜ | |
| UAT-SET-01 | Kebijakan sirkulasi | Ubah kuota/durasi siswa-guru dan batas pengambilan | Nilai baru berlaku untuk transaksi baru; transaksi lama tetap memakai snapshot | ⬜ | |
| UAT-SET-02 | Identitas sekolah | Ubah nama sekolah, nama perpustakaan, tagline, unggah logo | Perubahan langsung tampil di sidebar, login, navbar, footer, dan halaman cetak; logo lama dibersihkan | ⬜ | |
| UAT-SET-03 | Carousel | Tambah, edit, aktif/nonaktif, ubah urutan, hapus slide | Maksimal 5 slide aktif; urutan sesuai; slide nonaktif tidak tampil di beranda | ⬜ | |
| UAT-SET-04 | Pengumuman | Buat pengumuman dengan audiens dan jadwal terbit | Hanya pengumuman aktif, sudah terbit, dan sesuai audiens yang tampil di dashboard (maks. 3) | ⬜ | |
| UAT-SET-05 | Audit log | Lakukan perubahan data master, pengguna, dan transaksi | Setiap aksi penting mencatat aktor, waktu, entitas, dan nilai sebelum-sesudah | ⬜ | |

### 4.8 Kartu anggota dan kunjungan (Fase 7)

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-CARD-01 | Daftar kartu anggota | Buka **Manajemen → Kartu Anggota**; filter peran, kelas, cari nama/nomor induk | Hanya guru/siswa aktif dengan nomor induk yang tampil; filter dan pagination bekerja | ⬜ | |
| UAT-CARD-02 | Cetak kartu tunggal | Klik **Cetak** pada anggota guru dan siswa | Kartu ID-1 (85,6 × 54 mm) memuat identitas, peran, kelas/unit kerja, dan barcode; data sesuai anggota | ⬜ | |
| UAT-CARD-03 | Cetak massal | Pilih filter, klik **Cetak Massal** untuk >8 anggota | Maksimal 200 kartu per proses; 8 kartu per lembar A4 portrait; halaman berikutnya otomatis | ⬜ | |
| UAT-CARD-04 | Barcode terbaca | Pindai barcode kartu dengan scanner USB | Nomor induk terbaca persis; kunjungan dapat dicatat dari hasil scan | ⬜ | |
| UAT-CARD-05 | Anggota dilewati | Cetak massal yang mencakup akun nonaktif, tanpa nomor induk, atau nomor >20 karakter | Anggota tidak valid dilewati; peringatan jumlah kartu yang dilewati tampil; proses tidak gagal total | ⬜ | |
| UAT-VIS-01 | Catat kunjungan | Buka **Transaksi → Kunjungan**, scan/paste nomor induk, submit | Kunjungan hari ini tercatat; ringkasan anggota (pinjaman aktif/terlambat) tampil; audit `visit.recorded` tersimpan | ⬜ | |
| UAT-VIS-02 | Scan berulang | Scan anggota yang sama dua kali dalam <30 menit | Scan kedua diabaikan dengan pesan informasi; tidak ada kunjungan ganda | ⬜ | |
| UAT-VIS-03 | Scan setelah 30 menit | Scan anggota yang sama setelah >30 menit | Kunjungan baru tercatat; daftar menampilkan dua kunjungan | ⬜ | |
| UAT-VIS-04 | Batalkan kunjungan | Batalkan kunjungan hari ini; coba batalkan kunjungan kemarin | Kunjungan hari ini terhapus dan tercatat di audit; kunjungan lama tidak dapat dibatalkan | ⬜ | |
| UAT-VIS-05 | Grafik kunjungan | Buka dashboard admin setelah beberapa kunjungan | Grafik 7 hari dan angka kunjungan hari ini sesuai data; tabel alternatif tersedia | ⬜ | |
| UAT-VIS-06 | Otorisasi kunjungan | Login siswa/guru, buka `/admin/kunjungan` langsung | Ditolak (403); tidak ada data kunjungan yang tampil | ⬜ | |

### 4.9 Kiosk kunjungan mandiri (Fase 8)

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-KIOS-01 | Akses kiosk tanpa login | Buka beranda, klik **Kunjungan** atau **Buka Kiosk Kunjungan**; buka `/kunjungan` langsung | Halaman kiosk tampil tanpa login, memuat identitas perpustakaan, jam sekolah yang berjalan, dan kolom nomor induk yang otomatis fokus | ⬜ | |
| UAT-KIOS-02 | Catat kunjungan via scan | Scan kartu siswa/guru aktif atau ketik nomor induk, submit | Panel konfirmasi menampilkan nama, peran, kelas/unit, dan waktu; kunjungan tersimpan dengan sumber **Mandiri**, `recorded_by` kosong, dan audit `visit.recorded` | ⬜ | |
| UAT-KIOS-03 | Scan berulang | Scan anggota yang sama dua kali dalam <30 menit | Panel informasi **Sudah Tercatat** tampil; tidak ada kunjungan ganda | ⬜ | |
| UAT-KIOS-04 | Kode tidak valid | Coba nomor induk tidak dikenal, akun nonaktif, dan nomor induk admin | Masing-masing menampilkan pesan yang sesuai; tidak ada kunjungan yang tersimpan | ⬜ | |
| UAT-KIOS-05 | Toggle kiosk | Nonaktifkan kiosk di **Pengaturan → Kunjungan**, lalu aktifkan kembali | Saat nonaktif `/kunjungan` 404 dan tautan beranda hilang; saat aktif halaman dan tautan kembali tersedia; perubahan tercatat di audit | ⬜ | |
| UAT-KIOS-06 | Sumber kunjungan admin | Catat kunjungan dari admin dan kiosk, lalu buka **Transaksi → Kunjungan** | Badge sumber **Admin**/**Mandiri** tampil; filter sumber hanya menampilkan data yang dipilih | ⬜ | |
| UAT-KIOS-07 | Responsif dan aksesibilitas | Buka kiosk, beranda, pengaturan, dan daftar kunjungan pada 360/390/768/1024/1440 px; jalankan axe | Tidak ada horizontal overflow; input tetap fokus setelah konfirmasi; 0 pelanggaran WCAG 2.1 A/AA | ⬜ | |
| UAT-KIOS-08 | Cache, indeks, dan rate limit | Periksa header respons `/kunjungan`, meta robots, dan kirim >60 permintaan per menit | `Cache-Control: no-store`, `X-Robots-Tag: noindex, nofollow`, meta robots noindex, dan permintaan ke-61 mendapat 429 | ⬜ | |

### 4.10 Realtime, UX, dan responsif

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-RT-01 | Notifikasi admin | Buat pengajuan baru dari akun siswa; amati dropdown admin | Badge dan ringkasan diperbarui dalam ±30 detik; suara berbunyi sekali; tombol mute berfungsi dan tersimpan setelah reload | ⬜ | |
| UAT-RT-02 | Notifikasi peminjam | Setujui pengajuan saat peminjam membuka dashboard | Badge/notifikasi diperbarui; toast perubahan status muncul; hanya data milik sendiri | ⬜ | |
| UAT-RT-03 | Toast, loading, konfirmasi | Simpan form, klik aksi destruktif, batalkan konfirmasi | Toast sukses/gagal tampil; tombol menampilkan loading dan tidak terklik ganda; pembatalan konfirmasi tidak mengunci tombol | ⬜ | |
| UAT-RT-04 | Share fallback | Nonaktifkan Web Share API (atau gunakan browser desktop), klik Bagikan | Tautan tersalin ke clipboard dan toast sukses tampil | ⬜ | |
| UAT-RT-05 | Kurangi gerak | Aktifkan **prefers-reduced-motion** di OS/browser | Animasi, transisi, smooth scroll, dan auto-play carousel dimatikan/dikendalikan | ⬜ | |
| UAT-UI-01 | Sidebar accordion | Buka dashboard admin, guru, dan siswa; buka/tutup setiap grup menu | Grup menu dapat dilipat; grup halaman aktif terbuka otomatis; menu aktif ditandai; sidebar menjadi panel geser di mobile | ⬜ | |
| UAT-UI-02 | Tema terang/gelap/sistem | Pilih Terang, Gelap, lalu Ikuti Sistem dari menu header; reload halaman | Seluruh halaman mengikuti tema; pilihan tersimpan; teks, tabel, form, badge, modal, dan toast tetap terbaca | ⬜ | |
| UAT-UI-03 | Saklar mode gelap profil | Buka profil peminjam, aktifkan saklar mode gelap, lalu buka halaman lain | Tema berubah tanpa reload; preferensi sama dengan menu header; halaman kiosk dan cetak tetap terang | ⬜ | |
| UAT-QA-01 | Responsif | Buka halaman utama (termasuk kiosk kunjungan) pada 360, 390, 768, 1024, dan 1440 px | Tidak ada scroll horizontal; tabel dapat digeser; sidebar menjadi panel geser di mobile | ⬜ | |
| UAT-QA-02 | Keyboard dan fokus | Tab melalui navigasi, form, tabel, dan tombol; gunakan Enter/Space | Urutan fokus logis; fokus terlihat; tabel/region yang dapat digeser dapat difokuskan; modal dapat ditutup keyboard | ⬜ | |
| UAT-QA-03 | Kontras dan status non-warna | Periksa teks, tombol outline, badge status | Kontras teks memenuhi WCAG AA; status selalu disertai teks/label, bukan hanya warna | ⬜ | |
| UAT-QA-04 | Cetak di perangkat nyata | Cetak satu lembar kartu dan satu laporan di printer sekolah | Skala 100%; barcode tajam dan terbaca; footer kartu/tanda tangan laporan tidak terpotong | ⬜ | |

### 4.11 Operasional

| ID | Skenario | Langkah singkat | Hasil yang diharapkan | Status | Catatan |
|---|---|---|---|---|---|
| UAT-OPS-01 | Cleanup pengajuan kedaluwarsa | Jalankan `php artisan library:cleanup --dry-run`, lalu tanpa flag | Dry-run hanya menghitung; eksekusi membatalkan pengajuan disetujui yang melewati batas dan mencatat audit | ⬜ | |
| UAT-OPS-02 | Scheduler | Jalankan `php artisan schedule:list` | Command cleanup terjadwal harian pukul 01.00 sesuai zona waktu sekolah | ⬜ | |
| UAT-OPS-03 | Reset data demo | Jalankan `php artisan migrate:fresh --seed` di database uji | Data demo dan akun contoh dibuat ulang; aplikasi dapat langsung dipakai | ⬜ | |

---

## 5. Ringkasan hasil

| Kategori | Jumlah skenario | Lulus | Gagal | N/A |
|---|---:|---:|---:|---:|
| Publik dan katalog | 6 | | | |
| Autentikasi dan keamanan | 8 | | | |
| Peminjam | 7 | | | |
| Admin — katalog | 6 | | | |
| Admin — sirkulasi | 9 | | | |
| Admin — laporan | 5 | | | |
| Admin — pengguna & pengaturan | 10 | | | |
| Kartu anggota & kunjungan | 11 | | | |
| Kiosk kunjungan mandiri | 8 | | | |
| Realtime, UX, responsif | 12 | | | |
| Operasional | 3 | | | |
| **Total** | **85** | | | |

---

## 6. Lembar tanda tangan

Dengan menandatangani dokumen ini, pihak terkait menyatakan bahwa hasil UAT telah diperiksa dan sistem dinyatakan **layak digunakan secara terbatas** sesuai catatan yang tercantum.

| No | Peran | Nama | Jabatan | Tanda tangan | Tanggal |
|---|---|---|---|---|---|
| 1 | Pemilik produk | | Kepala Sekolah | | |
| 2 | Pengguna utama | | Kepala Perpustakaan/Pustakawan | | |
| 3 | Admin sistem | | Administrator Perpustakaan | | |
| 4 | Perwakilan guru | | Guru | | |
| 5 | Perwakilan siswa | | Siswa | | |
| 6 | Tim IT | | Teknisi/Pengembang | | |

**Keputusan akhir:**

- ⬜ Disetujui tanpa catatan
- ⬜ Disetujui dengan catatan (defect Medium/Low tercatat)
- ⬜ Belum disetujui (defect Critical/High harus diperbaiki lebih dahulu)

---

## 7. Catatan defect

| ID | Tanggal | Halaman/Fitur | Deskripsi | Tingkat | Status | Perbaikan/Retest |
|---|---|---|---|---|---|---|
| DEF-001 | | | | Critical/High/Medium/Low | Buka/Selesai | |
| DEF-002 | | | | | | |
| DEF-003 | | | | | | |

**Ambang tingkat defect:**

- **Critical** — data hilang/rusak, kebocoran akses, atau aplikasi tidak dapat dipakai.
- **High** — alur utama (login, pengajuan, checkout, pengembalian, cetak) gagal.
- **Medium** — fitur sekunder terganggu, masih ada workaround.
- **Low** — kosmetik, teks, atau peningkatan kenyamanan.
