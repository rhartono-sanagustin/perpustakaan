# UX/UI DESIGN SPECIFICATION

## Sistem Informasi Perpustakaan Sekolah — Flux UI

| Informasi | Keterangan |
|---|---|
| Dokumen | UX/UI Design Guide (`design.md`) |
| Versi | 2.0 |
| Tanggal | 18 November 2026 |
| Acuan kebutuhan | `prd.md` versi 1.0 |
| Framework UI | Flux UI (Flux Free) 2.x + Tailwind CSS 4.x |
| Runtime interaktif | Livewire 4.x dan Alpine.js (bawaan Flux) |
| Bahasa antarmuka | Bahasa Indonesia |
| Jenis aplikasi | Web responsif, sidebar desktop dan mobile-friendly |

---

## 1. Tujuan Desain

Dokumen ini menjelaskan struktur pengalaman pengguna, pola navigasi, sistem visual, komponen antarmuka, perilaku responsif, dan standar implementasi frontend untuk Sistem Informasi Perpustakaan Sekolah.

Antarmuka harus membantu tiga kelompok pengguna menyelesaikan tugas utama dengan cepat:

- **Admin:** memantau operasional, mengelola katalog, memproses peminjaman/pengembalian, dan membuat laporan.
- **Guru:** menemukan buku, mengetahui ketersediaannya, serta mengelola pinjaman pribadi.
- **Siswa:** menemukan buku dan memahami langkah pengajuan serta status peminjaman.

## 2. Prinsip UX

1. **Berorientasi tugas:** aksi utama seperti mencari buku, mengajukan pinjaman, checkout, dan pengembalian mudah ditemukan.
2. **Status transparan:** setiap permintaan dan transaksi memiliki label status beserta penjelasan singkat.
3. **Konsisten:** pola tombol, form, tabel, badge, dan notifikasi digunakan secara seragam.
4. **Responsif:** katalog dan aktivitas pengguna nyaman di ponsel; pekerjaan administratif efisien di layar lebih lebar.
5. **Aman secara visual:** tindakan berisiko seperti menonaktifkan buku atau menolak permintaan meminta konfirmasi.
6. **Aksesibel:** informasi tidak bergantung pada warna saja, form memiliki label, dan seluruh aksi utama bisa diakses dengan keyboard.
7. **Ringkas:** tampilkan informasi yang relevan dengan peran; jangan membebani siswa/guru dengan menu administrasi.

## 3. Arsitektur Informasi dan Navigasi

### 3.1 Navigasi admin

Sidebar desktop dengan grup menu yang dapat dibuka/tutup (accordion):

1. Dashboard
2. Katalog Buku
   - Daftar Buku
   - Kategori
   - Penulis
   - Penerbit
   - Eksemplar/Inventaris
3. Transaksi Peminjaman
   - Permintaan Peminjaman
   - Peminjaman Aktif
   - Pengembalian
   - Riwayat Transaksi
4. Laporan
   - Ringkasan
   - Peminjaman & Pengembalian
   - Keterlambatan
   - Inventaris
   - Buku Populer
5. Pengguna
6. Pengaturan

Topbar admin: pemilih tema Terang/Gelap/Sistem, notifikasi aktivitas, profil admin, dan logout. Tombol buka/tutup sidebar tersedia pada layar kecil.

### 3.2 Navigasi guru dan siswa

Sidebar dengan grup menu yang dapat dibuka/tutup:

- Beranda
- Katalog Buku
- Pinjaman Saya
  - Menunggu Diproses
  - Disetujui
  - Sedang Dipinjam
  - Terlambat
  - Riwayat
- Profil

Pada layar kecil, sidebar berubah menjadi panel geser (offcanvas) yang dikelola komponen `flux:sidebar`; tombol menu berada di header. Guru dan siswa menggunakan struktur yang sama; perbedaan kebijakan pinjam ditampilkan sebagai informasi akun, bukan sebagai navigasi yang berbeda.

### 3.3 Tema tampilan

- Tersedia tiga pilihan tema: **Terang**, **Gelap**, dan **Sistem** (mengikuti preferensi perangkat).
- Pemilih tema diletakkan di dropdown header untuk seluruh peran.
- Halaman profil peminjam menyediakan saklar mode gelap sebagai akses cepat.
- Preferensi disimpan di browser melalui mekanisme `$flux.appearance` sehingga tetap konsisten antarhalaman.
- Halaman kiosk dan cetak selalu terang agar hasil cetak dan barcode tetap terbaca.

### 3.4 Aturan navigasi

- Menu ditampilkan berdasarkan peran dari server.
- Menyembunyikan menu bukan pengganti otorisasi backend.
- Halaman detail dan form memiliki breadcrumb pada area admin.
- Pengguna selalu memiliki jalan kembali ke katalog atau daftar asal.
- Tombol aksi utama ditempatkan konsisten di kanan atas halaman pada desktop dan setelah judul pada mobile.

## 4. Sistem Visual (Design Tokens)

### 4.1 Arah visual

Gunakan gaya **modern, bersih, ramah pendidikan, dan profesional**. Hindari dekorasi berlebihan. Ruang putih, hierarki teks, dan status yang mudah dipindai menjadi prioritas.

### 4.2 Palet warna

| Token | Nilai | Penggunaan |
|---|---|---|
| Primary | `#1F4E79` | Tombol utama, tautan penting, navigasi aktif |
| Primary hover | `#173B5C` | Hover/fokus tombol utama |
| Secondary | `#0F766E` | Aksen sekunder dan elemen pendukung |
| App background | `#F5F7FA` | Latar area aplikasi |
| Surface | `#FFFFFF` | Kartu, tabel, form, modal |
| Text primary | `#243244` | Teks utama |
| Text muted | `#667085` | Teks bantuan dan metadata |
| Border | `#E4E7EC` | Garis pemisah dan batas komponen |
| Success | `#198754` | Tersedia, berhasil, dikembalikan |
| Warning | `#B7791F` | Menunggu, mendekati jatuh tempo |
| Danger | `#DC3545` | Terlambat, ditolak, tindakan destruktif |
| Info | `#0DCAF0` | Informasi/netral yang perlu perhatian |

Warna status perlu dipasangkan dengan teks dan/atau ikon. Jangan menggunakan warna sebagai satu-satunya pembeda.

### 4.3 Tipografi

- Gunakan font sistem: `ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`.
- Ukuran teks isi standar: 1rem (kelas `text-base`).
- Judul halaman: `text-2xl` pada desktop dan `text-xl` pada mobile.
- Label form dan metadata: `text-sm` atau `text-xs`.
- Gunakan `font-semibold` untuk judul kartu dan `text-zinc-500 dark:text-zinc-400` untuk keterangan sekunder.
- Hindari terlalu banyak variasi ukuran dan ketebalan font.

### 4.4 Spasi dan bentuk

- Gunakan skala spacing Tailwind (`1`, `2`, `3`, `4`, `5`, `6`, `8`) secara konsisten.
- Jarak antarbagian halaman: `gap-4` sampai `gap-6`.
- Jarak isi kartu: `p-4` sampai `p-5`.
- Radius kartu: `rounded-xl`; tombol dan input `rounded-lg`.
- Tombol dan input menggunakan tinggi sentuh yang nyaman; gunakan `h-11` untuk CTA utama pada perangkat sentuh.
- Gunakan border tipis (`border-zinc-200 dark:border-white/10`); shadow ringan (`shadow-xs`) hanya untuk elemen yang perlu terangkat.

## 5. Kerangka Layout

### 5.1 Layout admin desktop

Gunakan `flux:sidebar` tetap di sisi kiri, `flux:header` di atas area konten, dan `flux:main` untuk isi halaman. Sidebar memakai grup `flux:sidebar.group expandable` agar menu dapat dilipat. Area utama memakai latar `bg-zinc-50 dark:bg-zinc-950` dan padding responsif.

```text
+----------------------+---------------------------------------------+
| BRAND                | Header: tema / notifikasi / profil          |
|----------------------|---------------------------------------------|
| Dashboard            | Page title                  [Primary action]|
| Katalog Buku     [v] |                                             |
| Sirkulasi        [v] | KPI cards                                   |
| Laporan          [v] |                                             |
| Pengguna             | Main content: table / chart / form          |
| Pengaturan           |                                             |
+----------------------+---------------------------------------------+
```

Pada layar di bawah breakpoint `lg`, sidebar tersembunyi dan dapat dibuka sebagai panel geser melalui tombol menu di header. Konten utama mengambil lebar penuh.

### 5.2 Layout guru/siswa

Guru dan siswa memakai pola yang sama dengan admin: `flux:sidebar`, `flux:header`, dan `flux:main`. Sidebar memuat menu utama serta grup status pinjaman. Dashboard peminjam menempatkan ringkasan pinjaman aktif dan daftar buku terbaru/populer di bawahnya.

```text
+----------------------+---------------------------------------------+
| BRAND                | Header: tema / notifikasi / profil          |
|----------------------|---------------------------------------------|
| Beranda              | Sapaan + ringkasan akun                     |
| Katalog Buku         | [Pinjaman aktif] [Menunggu] [Terlambat]     |
| Pinjaman Saya    [v] | Buku yang sedang dipinjam                   |
| Profil               | Rekomendasi / buku terbaru                  |
+----------------------+---------------------------------------------+
```

### 5.3 Grid responsif Tailwind

- Konten admin dibungkus `mx-auto w-full max-w-7xl px-4 lg:px-8`; halaman publik memakai pembungkus yang sama.
- Kartu katalog: `grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5`.
- Dashboard KPI: `grid gap-3 sm:grid-cols-2 xl:grid-cols-4`.
- Form panjang: `grid gap-5 sm:grid-cols-2` untuk field yang saling berkaitan.
- Tabel admin dibungkus `div.overflow-x-auto` dengan atribut `data-scrollable-region` agar tidak memaksa layout melebar pada mobile.

## 6. Spesifikasi Halaman

### 6.1 Login

**Tujuan:** memberi akses sesuai peran.

Elemen:
- Logo/nama perpustakaan dan nama sekolah.
- Judul “Masuk ke Perpustakaan”.
- Input identitas akun.
- Input kata sandi dengan opsi tampil/sembunyikan.
- Checkbox “Ingat saya” hanya jika mekanisme sesi mendukungnya dengan aman.
- Tombol “Masuk”.
- Pesan bantuan untuk menghubungi admin ketika akun bermasalah.

Perilaku:
- Validasi wajib di sisi klien dan server.
- Pesan login gagal bersifat umum dan tidak membocorkan apakah username terdaftar.
- Tombol menampilkan state loading ketika proses berlangsung.
- Setelah berhasil, arahkan pengguna ke dashboard sesuai peran.

### 6.2 Dashboard admin

**Tujuan:** memberi gambaran keadaan perpustakaan dan pekerjaan yang perlu ditangani.

Susunan konten:
1. Judul “Dashboard” dan tanggal/periode ringkasan.
2. Empat KPI utama: Total Judul, Eksemplar Tersedia, Peminjaman Aktif, Terlambat.
3. Panel “Permintaan Peminjaman Terbaru” berisi peminjam, buku, waktu pengajuan, dan tombol tindakan.
4. Panel “Aktivitas Peminjaman” berupa grafik sederhana per periode jika data memadai.
5. Panel “Perlu Ditindaklanjuti” untuk pinjaman terlambat atau permintaan yang menunggu.
6. Tautan cepat: Tambah Buku, Proses Peminjaman, Catat Pengembalian, Lihat Laporan.

Aturan:
- KPI dapat diklik menuju daftar yang sudah difilter.
- Angka harus berasal dari agregasi data nyata, bukan angka statis.
- Gunakan empty state bila belum ada transaksi.

### 6.3 Dashboard guru/siswa

**Tujuan:** menampilkan pinjaman pribadi dan akses cepat ke katalog.

Susunan konten:
1. Sapaan singkat dan nama pengguna.
2. Tombol utama “Cari Buku”.
3. Ringkasan: Pinjaman Aktif, Menunggu Diproses, Terlambat.
4. Daftar buku yang sedang dipinjam dengan tanggal jatuh tempo.
5. Riwayat terbaru.
6. Koleksi pilihan/buku terbaru (opsional berdasarkan data katalog).

Tampilkan pemberitahuan jatuh tempo secara jelas, misalnya “Jatuh tempo 10 November 2026”. Jangan menampilkan transaksi pengguna lain.

### 6.4 Katalog buku

**Tujuan:** membantu pengguna menemukan buku dengan cepat.

Elemen:
- Judul halaman “Katalog Buku”.
- Search input lebar dengan placeholder “Cari judul, penulis, ISBN, atau kata kunci”.
- Filter kategori dan ketersediaan.
- Chip/filter aktif dan tombol “Reset Filter”.
- Jumlah hasil pencarian.
- Pilihan tampilan grid/list bila dibutuhkan.
- Kartu buku dengan sampul, judul, penulis, kategori, dan status ketersediaan.
- Pagination.

Kartu katalog:
- Rasio sampul konsisten, misalnya 3:4.
- Judul maksimal dua baris dengan truncation yang tetap memungkinkan judul lengkap dibaca di detail.
- Tampilkan “Tersedia 3 eksemplar” atau “Sedang tidak tersedia”.
- Tombol/tautan “Detail”.

Perilaku:
- Pencarian dapat dikirim melalui Enter atau tombol cari.
- Filter mempertahankan nilai saat berpindah halaman.
- Saat tidak ada hasil, berikan saran untuk mengubah kata kunci/filter.
- Pengguna yang belum login tetap boleh melihat katalog jika kebijakan sekolah mengizinkan; tombol pengajuan meminta login.

### 6.5 Detail buku

**Tujuan:** memberi informasi cukup sebelum pengguna mengajukan pinjaman.

Susunan:
- Breadcrumb: Beranda / Katalog / Detail Buku.
- Sampul buku di sisi kiri pada desktop; metadata di sisi kanan.
- Judul, penulis, kategori, penerbit, tahun, ISBN, bahasa.
- Ringkasan/deskripsi.
- Ketersediaan: total eksemplar, tersedia, dipinjam (hindari membuka identitas peminjam).
- Lokasi/rak ditampilkan secara umum; detail kode inventaris dapat dibatasi untuk admin.
- Tombol “Ajukan Peminjaman” untuk guru/siswa yang login.

State tombol:
- Aktif ketika tersedia dan pengguna memenuhi aturan awal.
- Nonaktif ketika tidak tersedia, disertai penjelasan.
- Pengguna belum login diarahkan ke login dan kembali ke detail buku setelah berhasil.

### 6.6 Form pengajuan peminjaman

Bentuk form ringkas atau modal konfirmasi dari halaman detail.

Elemen:
- Ringkasan buku yang diminta.
- Informasi batas pinjaman dan durasi sesuai peran.
- Checkbox persetujuan ketentuan peminjaman bila diperlukan sekolah.
- Tombol “Kirim Pengajuan” dan “Batal”.

Setelah sukses, tampilkan notifikasi “Pengajuan berhasil dikirim” serta status “Menunggu Diproses”. Tegaskan bahwa buku belum resmi dipinjam sampai diserahkan admin.

### 6.7 Pinjaman Saya dan riwayat

**Tujuan:** memungkinkan guru/siswa memantau transaksi pribadinya.

Gunakan tab atau segmented navigation:
- Menunggu Diproses
- Disetujui
- Sedang Dipinjam
- Terlambat
- Riwayat

Kolom desktop: Buku, Tanggal Pengajuan/Pinjam, Jatuh Tempo, Status, Aksi. Pada mobile tampilkan item sebagai card/stacked row.

Detail pinjaman memuat nomor transaksi, informasi buku, tanggal, status, dan alasan penolakan jika ada.

### 6.8 Manajemen buku (admin)

Halaman daftar menggunakan tabel dengan:
- Pencarian judul/ISBN/penulis.
- Filter kategori dan status.
- Tombol “Tambah Buku”.
- Kolom sampul mini, judul/ISBN, kategori, total eksemplar, tersedia, status, aksi.
- Aksi: Detail, Edit, Kelola Eksemplar, Arsipkan.

Form tambah/edit buku dibagi menjadi bagian:
1. Informasi utama: judul, ISBN, kategori, penulis.
2. Informasi penerbitan: penerbit, tahun, edisi, bahasa.
3. Deskripsi dan sampul.
4. Status katalog.

Kelola eksemplar dilakukan pada panel/halaman tersendiri agar data bibliografi tidak tercampur dengan inventaris fisik. Setiap eksemplar memiliki kode inventaris unik, lokasi rak, kondisi, dan status.

### 6.9 Sirkulasi admin

**Permintaan peminjaman:** tabel/daftar antrean dengan nama peminjam, identitas, judul buku, waktu pengajuan, dan status. Admin dapat membuka detail lalu menyetujui atau menolak. Penolakan mewajibkan alasan.

**Peminjaman aktif:** tampilkan peminjam, buku, kode inventaris, tanggal pinjam, jatuh tempo, dan indikator keterlambatan. Aksi cepat “Catat Pengembalian”.

**Checkout:** admin memilih eksemplar tersedia, memverifikasi peminjam, melihat ringkasan kebijakan, lalu mengonfirmasi penyerahan buku. Sistem menampilkan nomor transaksi dan tanggal jatuh tempo.

**Pengembalian:** admin menemukan pinjaman, memeriksa data buku, memilih kondisi saat kembali (Baik/Rusak/Hilang sesuai SOP), menambahkan catatan opsional, lalu menyimpan. Minta konfirmasi sebelum menutup transaksi.

### 6.10 Laporan admin

Gunakan pola halaman laporan yang seragam:
- Judul laporan.
- Filter tanggal awal–akhir dan filter tambahan yang sesuai.
- Tombol “Terapkan”, “Reset”, “Ekspor CSV”, dan “Cetak”.
- Ringkasan angka di atas tabel bila relevan.
- Tabel hasil dengan pagination dan total data.
- Keterangan periode dan waktu pembuatan.

Pada laporan inventaris, sediakan ringkasan per status. Pada laporan keterlambatan, tampilkan jumlah hari terlambat dan tautan ke detail transaksi.

### 6.11 Pengguna dan pengaturan

**Pengguna:** tabel nama, identitas sekolah, peran, kelas/unit (jika ada), status akun, dan aksi. Form tambah/edit mencakup data minimum. Status nonaktif harus mencegah login dan pengajuan baru.

**Pengaturan:** kelompokkan kebijakan berdasarkan peran (kuota dan durasi), batas waktu pengambilan permintaan yang disetujui, serta kebijakan denda bila kelak digunakan. Beri teks bantuan bahwa perubahan kebijakan hanya berlaku untuk transaksi baru.

## 7. Komponen UI dan Pola Interaksi

| Komponen | Flux UI | Pedoman penggunaan |
|---|---|---|
| Sidebar | `flux:sidebar`, `flux:sidebar.group expandable` | Navigasi utama admin, guru, dan siswa; grup menu dapat dilipat |
| Header | `flux:header` | Pemilih tema, notifikasi, profil, dan tombol sidebar mobile |
| KPI card | `flux:card` + komponen `kpi-card` | Angka besar, label singkat, ikon dekoratif seperlunya |
| Kartu buku | `flux:card` + komponen `book-card` | Sampul rasio konsisten dan CTA jelas |
| Tabel | `table.data-table` dalam `div.overflow-x-auto` | Header jelas, aksi tidak terlalu padat |
| Form | `flux:field`, `flux:label`, `flux:input`, `flux:select`, `flux:textarea`, `flux:error` | Selalu gunakan label eksplisit dan pesan validasi |
| Status | `flux:badge` melalui komponen `status-badge` | Teks status wajib terlihat; warna hanya pendukung |
| Tabs | Tautan berbasis `flux:badge`/segmented nav | Memisahkan status pinjaman tanpa komponen Tabs Pro |
| Modal | `flux:modal` | Konfirmasi singkat atau tindakan kontekstual |
| Toast/alert | `flux:toast`, `flux:callout` | Feedback sukses/gagal/informasi |
| Pagination | `{{ $paginator->links() }}` | Tampilkan halaman aktif dan navigasi yang mudah |
| Empty state | komponen `empty-state` | Jelaskan keadaan dan langkah selanjutnya |
| Loading | state tombol `data-loading-text` | Cegah klik ganda ketika permintaan berjalan |
| Tema | `partials.theme-switcher` dengan `$flux.appearance` | Terang, Gelap, atau Sistem |

### 7.1 Label status standar

| Status | Label tampilan | Varian badge Flux |
|---|---|---|
| Menunggu Diproses | Menunggu | `warning` (amber) |
| Disetujui | Disetujui | `info` (cyan) |
| Ditolak | Ditolak | `danger` (red) |
| Dipinjam | Dipinjam | `primary` (sky) |
| Terlambat | Terlambat | `danger` (red) |
| Dikembalikan | Dikembalikan | `success` (green) |
| Dibatalkan | Dibatalkan | `secondary` (zinc) |
| Tersedia | Tersedia | `success` (green) |
| Rusak | Rusak | `warning` (amber) |
| Hilang | Hilang | `danger` (red) |

Teks badge harus tetap terbaca dan tidak hanya mengandalkan warna. Pastikan kombinasi kontras diperiksa ketika tema diubah.

### 7.2 Notifikasi

- **Sukses:** tindakan berhasil disimpan.
- **Peringatan:** stok hampir habis, jatuh tempo dekat, atau ada field yang perlu diperiksa.
- **Gagal:** proses tidak berhasil; jelaskan tindakan yang dapat dilakukan pengguna.
- **Konfirmasi:** wajib untuk arsip, penolakan, checkout, dan pengembalian.

Hindari pesan teknis seperti stack trace atau query database pada UI.

## 8. Responsif dan Perilaku Perangkat

| Ukuran | Perilaku yang diharapkan |
|---|---|
| Mobile, <576px | Sidebar menjadi panel geser; KPI satu kolom; katalog dua kolom; tabel scroll horizontal atau card; form satu kolom |
| Small tablet, ≥576px | KPI dua kolom; katalog dua/tiga kolom; beberapa form dapat dua kolom |
| Tablet/desktop, ≥768px | Area konten lebih luas; filter dapat sejajar; tabel mulai menjadi pola utama |
| Desktop, ≥992px | Sidebar tampil; katalog tiga/empat kolom sesuai lebar |
| Wide desktop, ≥1200px | Maksimalisasi keterbacaan; tabel dan dashboard dapat menggunakan beberapa panel |

Ketentuan tambahan:
- Jangan menetapkan lebar tetap pada area konten utama.
- Tombol penting tidak boleh terpotong atau tertutup oleh elemen sticky.
- Modal panjang harus dapat discroll di dalam modal.
- Filter pada mobile dapat ditempatkan pada panel/dropdown agar area katalog tetap luas.
- Uji pada ukuran 360px, 390px, 768px, 1024px, dan 1440px, pada tema terang dan gelap.

## 9. Aksesibilitas dan Usability

- Setiap input memiliki `<label>` yang terhubung melalui `for` dan `id`.
- Gunakan struktur heading berurutan (`h1`, `h2`, `h3`).
- Fokus keyboard harus terlihat; jangan menghapus outline tanpa pengganti.
- Tombol memiliki nama aksi yang jelas, misalnya “Catat Pengembalian”, bukan hanya ikon.
- Gambar sampul memiliki alt text yang sesuai; gambar dekoratif memakai alt kosong.
- Error form dihubungkan dengan field terkait dan dirangkum bila form panjang.
- Tabel memiliki header kolom yang bermakna.
- Status terlambat disampaikan melalui teks dan ikon/label, bukan merah saja.
- Gunakan ukuran teks dan target sentuh yang nyaman.
- Hormati preferensi reduced motion bila menambahkan animasi.

## 10. Panduan Implementasi Flux UI

### 10.1 Dependensi

- **Flux Free** dipasang melalui Composer (`livewire/flux`) bersama `livewire/livewire`.
- **Tailwind CSS 4** dipasang melalui npm bersama `@tailwindcss/vite`; Vite memuat `resources/css/app.css` dan `resources/js/app.js`.
- CSS Flux diimpor dari `vendor/livewire/flux/dist/flux.css` di dalam `app.css`.
- Bootstrap dan Bootstrap Icons tidak digunakan lagi. Ikon memakai Heroicons melalui `flux:icon` atau komponen `x-icon`.
- Setiap layout aplikasi wajib memuat `@fluxAppearance` di `<head>` dan `@fluxScripts` sebelum `</body>`. Layout kiosk dan cetak tidak memuat `@fluxAppearance` agar selalu terang.
- Komponen interaktif memakai Livewire/Alpine: konfirmasi melalui `flux:modal`, notifikasi melalui `Flux.toast`, dan dropdown melalui `flux:dropdown`/`flux:menu`.

Flux Free tidak menyediakan komponen Tabs, Date Picker, dan Chart. Gunakan navigasi tautan untuk tab, input tanggal native, dan markup HTML/CSS yang sudah ada untuk grafik.

### 10.2 Token tema

Token desain ditempatkan di `resources/css/app.css` menggunakan direktif Tailwind 4.

```css
@import 'tailwindcss';
@import '../../vendor/livewire/flux/dist/flux.css';

@custom-variant dark (&:where(.dark, .dark *));

@theme {
    --font-sans: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
    --color-accent: #1f4e79;
    --color-accent-content: #173b5c;
    --color-accent-foreground: #ffffff;
}

@layer theme {
    .dark {
        --color-accent: #3b82c4;
        --color-accent-content: #93c5fd;
        --color-accent-foreground: #ffffff;
    }
}
```

Gunakan `bg-[var(--color-accent)]`, `text-[var(--color-accent-content)]`, dan varian `dark:` untuk mempertahankan konsistensi tema terang/gelap.

### 10.3 Kerangka layout

```blade
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <flux:sidebar stashable sticky class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-library-logo size="sm" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="{{ route('admin.dashboard') }}">Dashboard</flux:sidebar.item>

            <flux:sidebar.group expandable heading="Katalog Buku" icon="book-open" :expanded="request()->routeIs('admin.books.*', 'admin.categories.*')">
                <flux:sidebar.item href="{{ route('admin.books.index') }}">Daftar Buku</flux:sidebar.item>
                <flux:sidebar.item href="{{ route('admin.categories.index') }}">Kategori</flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>
    </flux:sidebar>

    <flux:header class="border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle icon="bars-3" />
        <flux:spacer />
        @include('partials.theme-switcher')
        @include('partials.notifications-menu')
    </flux:header>

    <flux:main class="mx-auto w-full max-w-7xl">
        @yield('content')
    </flux:main>

    @fluxScripts
</body>
```

`flux:sidebar` dan `flux:header` harus menjadi anak langsung `<body>` agar perilaku sticky dan panel geser bekerja benar.

### 10.4 Contoh form filter katalog

```blade
<flux:card>
    <form method="GET" action="{{ route('catalog.index') }}" role="search" class="grid items-end gap-3 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <flux:field>
                <flux:label for="q">Cari buku</flux:label>
                <flux:input id="q" name="q" type="search" icon="magnifying-glass"
                            placeholder="Cari judul, penulis, ISBN, atau kata kunci" />
            </flux:field>
        </div>

        <div class="lg:col-span-3" data-auto-submit>
            <flux:field>
                <flux:label for="category">Kategori</flux:label>
                <flux:select id="category" name="category">
                    <option value="">Semua kategori</option>
                </flux:select>
            </flux:field>
        </div>

        <div class="lg:col-span-2">
            <flux:button type="submit" variant="primary" icon="funnel" class="w-full">Terapkan</flux:button>
        </div>
    </form>
</flux:card>
```

Letakkan `data-auto-submit` pada elemen pembungkus, bukan langsung pada `flux:select`, karena Flux meneruskan atribut ke wrapper dan kontrol sekaligus.

### 10.5 Contoh kartu buku dan tabel admin

```blade
<div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
    @foreach ($books as $book)
        <x-book-card :book="$book" />
    @endforeach
</div>

<div class="overflow-x-auto" data-scrollable-region>
    <table class="data-table">
        <thead>
            <tr>
                <th scope="col">Peminjam</th>
                <th scope="col">Buku</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $loan)
                <tr>
                    <td>{{ $loan->user->name }}</td>
                    <td>{{ $loan->copy->book->title }}</td>
                    <td><x-status-badge :label="$loan->statusLabel()" :variant="$loan->statusVariant()" /></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

Contoh di atas adalah pola markup, bukan data transaksi sebenarnya. Isi tabel dan kartu harus dihasilkan dari data aplikasi dan tetap menjalankan otorisasi di backend.

## 11. Struktur Komponen Frontend yang Disarankan

Contoh struktur yang dapat disesuaikan dengan arsitektur Laravel/Blade:

```text
resources/
├── css/
│   └── app.css                 # Tailwind + Flux + token tema
├── js/
│   ├── app.js                  # konfirmasi, loading, print, auto-submit
│   ├── kiosk.js
│   ├── notifications.js
│   ├── share.js
│   └── toast.js
└── views/
    ├── layouts/
    │   ├── admin.blade.php     # flux:sidebar + flux:header + flux:main
    │   ├── borrower.blade.php  # sidebar guru/siswa
    │   ├── auth.blade.php
    │   ├── public.blade.php
    │   ├── kiosk.blade.php     # terang saja
    │   ├── print.blade.php     # A4 landscape, terang saja
    │   └── print-card.blade.php # A4 portrait, terang saja
    ├── partials/
    │   ├── theme-switcher.blade.php
    │   ├── notifications-menu.blade.php
    │   ├── confirm-modal.blade.php
    │   ├── flash.blade.php
    │   └── landing-carousel.blade.php
    ├── components/
    │   ├── status-badge.blade.php
    │   ├── book-card.blade.php
    │   ├── empty-state.blade.php
    │   ├── page-header.blade.php
    │   ├── kpi-card.blade.php
    │   ├── form-errors.blade.php
    │   ├── icon.blade.php
    │   └── library-logo.blade.php
    ├── admin/
    │   ├── dashboard/
    │   ├── books/
    │   ├── copies/
    │   ├── circulation/
    │   ├── reports/
    │   ├── users/
    │   └── settings/
    ├── borrower/
    │   ├── dashboard.blade.php
    │   ├── loans/
    │   └── profile.blade.php
    ├── catalog/
    └── auth/
        └── login.blade.php
```

Pisahkan komponen yang berulang (badge status, kartu buku, header halaman, empty state) agar perubahan visual dapat diterapkan konsisten. Hindari menaruh seluruh markup aplikasi dalam satu file view besar. Halaman cetak memakai komponen `member-card` dan CSS cetak khusus agar ukuran A4 serta barcode tidak berubah.

## 12. State dan Microcopy

### 12.1 Empty state

- Katalog kosong: “Belum ada buku yang sesuai. Coba ubah kata kunci atau filter pencarian.”
- Pinjaman aktif kosong: “Anda belum memiliki pinjaman aktif.” CTA: “Jelajahi Katalog”.
- Permintaan kosong (admin): “Tidak ada permintaan peminjaman yang menunggu diproses.”
- Laporan kosong: “Tidak ada data untuk filter periode ini.” CTA: “Ubah Periode”.

### 12.2 Pesan konfirmasi

- Checkout: “Konfirmasi bahwa buku telah diserahkan kepada peminjam?”
- Pengembalian: “Simpan pengembalian dan tutup transaksi ini?”
- Penolakan: “Tolak permintaan peminjaman ini? Tambahkan alasan agar peminjam memahami keputusan.”
- Arsip buku: “Buku tidak akan tampil pada katalog aktif. Riwayat transaksi tetap tersimpan.”

### 12.3 Pesan keberhasilan

- “Data buku berhasil disimpan.”
- “Pengajuan peminjaman berhasil dikirim.”
- “Peminjaman berhasil dicatat. Jatuh tempo: [tanggal].”
- “Pengembalian berhasil dicatat.”
- “Laporan berhasil diekspor.”

## 13. UX Acceptance Checklist

### Konsistensi
- [ ] Semua halaman memakai layout, tipografi, warna, dan spacing yang sama.
- [ ] Label status konsisten di katalog, dashboard, sirkulasi, dan laporan.
- [ ] Tombol utama dan tombol berbahaya memiliki gaya berbeda dan konsisten.

### Tema
- [ ] Pemilih tema Terang/Gelap/Sistem berfungsi di header admin, guru, dan siswa.
- [ ] Saklar mode gelap pada profil peminjam menyimpan preferensi yang sama.
- [ ] Seluruh halaman aplikasi terbaca pada tema terang dan gelap.
- [ ] Halaman kiosk dan cetak tetap terang serta tidak terpengaruh preferensi gelap.

### Alur pengguna
- [ ] Guru/siswa dapat mencapai katalog dari beranda dalam satu aksi yang jelas.
- [ ] Informasi ketersediaan tampak sebelum pengguna mengajukan peminjaman.
- [ ] Pengguna memahami bahwa pengajuan belum berarti buku telah dipinjam.
- [ ] Admin dapat menemukan permintaan, checkout, dan pengembalian tanpa berpindah menu berlebihan.
- [ ] Pengguna dapat melihat tanggal jatuh tempo dan status pinjaman pribadi.
- [ ] Grup menu sidebar dapat dibuka/tutup dan status aktif mengikuti halaman.

### Responsif dan aksesibilitas
- [ ] Tidak ada horizontal overflow halaman pada lebar 360px, kecuali area tabel yang memang dibuat scrollable.
- [ ] Form dapat digunakan pada mobile tanpa zoom atau field terpotong.
- [ ] Semua tombol dan input dapat dioperasikan dengan keyboard.
- [ ] Fokus terlihat dan kontras teks memadai.
- [ ] Status transaksi dapat dipahami tanpa melihat warna.

### State sistem
- [ ] Loading, sukses, gagal, kosong, dan validasi ditampilkan.
- [ ] Tombol submit dicegah dari klik ganda saat proses berjalan.
- [ ] Konfirmasi tersedia untuk tindakan yang berdampak pada transaksi atau data.

## 14. Keputusan Desain yang Masih Perlu Dikonfirmasi

1. Nama dan logo sekolah/perpustakaan.
2. Apakah katalog dapat diakses tanpa login.
3. Apakah sekolah menghendaki gaya visual formal (warna institusi) atau gaya ramah siswa.
4. Apakah foto sampul disimpan pada server lokal atau menggunakan sumber eksternal yang sah.
5. Apakah notifikasi dalam aplikasi cukup untuk MVP atau diperlukan integrasi email/WhatsApp.
6. Apakah admin membutuhkan pencetakan bukti peminjaman/pengembalian.

---

**Akhir dokumen design.md.** Implementasi akhir harus mengikuti aturan akses, status, dan aturan bisnis pada `prd.md`; desain antarmuka tidak boleh menggantikan validasi dan otorisasi di sisi server.
