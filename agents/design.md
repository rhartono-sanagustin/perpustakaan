# UX/UI DESIGN SPECIFICATION

## Sistem Informasi Perpustakaan Sekolah — Bootstrap 5

| Informasi | Keterangan |
|---|---|
| Dokumen | UX/UI Design Guide (`design.md`) |
| Versi | 1.0 |
| Tanggal | 7 November 2026 |
| Acuan kebutuhan | `prd.md` versi 1.0 |
| Framework UI | Bootstrap 5.x |
| Bahasa antarmuka | Bahasa Indonesia |
| Jenis aplikasi | Web responsif, desktop-first untuk admin dan mobile-friendly untuk peminjam |

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

Sidebar desktop:

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

Topbar admin: tombol menu (mobile), breadcrumb/judul halaman, pencarian opsional, notifikasi aktivitas (bila diimplementasikan), profil admin, dan logout.

### 3.2 Navigasi guru dan siswa

Navbar utama:

- Beranda
- Katalog Buku
- Pinjaman Saya
- Riwayat
- Profil

Pada layar kecil, menu dapat berubah menjadi offcanvas Bootstrap. Guru dan siswa menggunakan struktur yang sama; perbedaan kebijakan pinjam ditampilkan sebagai informasi akun, bukan sebagai navigasi yang berbeda.

### 3.3 Aturan navigasi

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

- Gunakan font sistem Bootstrap: `system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`.
- Ukuran teks isi standar: 1rem.
- Judul halaman: sekitar 1.5–1.75rem pada desktop dan 1.25–1.5rem pada mobile.
- Label form dan metadata: 0.875–0.95rem.
- Gunakan `fw-semibold` untuk judul kartu dan `text-muted` untuk keterangan sekunder.
- Hindari terlalu banyak variasi ukuran dan ketebalan font.

### 4.4 Spasi dan bentuk

- Gunakan skala spacing Bootstrap (`1` sampai `5`) secara konsisten.
- Jarak antarbagian halaman: 1.5rem–2rem.
- Jarak isi kartu: 1rem–1.25rem.
- Radius kartu: sekitar 0.75rem.
- Tombol dan input menggunakan tinggi sentuh yang nyaman; targetkan minimal sekitar 44px untuk kontrol utama pada perangkat sentuh.
- Gunakan border tipis; shadow ringan hanya untuk elemen yang perlu terangkat.

## 5. Kerangka Layout

### 5.1 Layout admin desktop

Gunakan sidebar tetap di sisi kiri dan area konten di kanan. Lebar sidebar awal sekitar 250px dan dapat dipersempit bila diperlukan. Area utama menggunakan `container-fluid`, latar app background, serta konten dengan lebar yang nyaman dibaca.

```text
+----------------------+---------------------------------------------+
| BRAND                | Topbar: breadcrumb / title / profile        |
|----------------------|---------------------------------------------|
| Dashboard            | Page title                  [Primary action]|
| Katalog Buku         |                                             |
| Sirkulasi            | KPI cards                                  |
| Laporan              |                                             |
| Pengguna             | Main content: table / chart / form          |
| Pengaturan           |                                             |
+----------------------+---------------------------------------------+
```

Pada layar di bawah breakpoint `lg`, sidebar berubah menjadi offcanvas. Konten utama mengambil lebar penuh.

### 5.2 Layout guru/siswa

Gunakan navbar di atas, konten utama di tengah dengan lebar maksimum yang wajar, dan footer sederhana. Dashboard peminjam menempatkan ringkasan pinjaman aktif dan daftar buku terbaru/populer di bawahnya.

```text
+-------------------------------------------------------------+
| Logo | Beranda | Katalog | Pinjaman Saya | Profil           |
+-------------------------------------------------------------+
| Sapaan + ringkasan akun                                     |
| [Pinjaman aktif] [Menunggu] [Terlambat]                     |
| Buku yang sedang dipinjam                                   |
| Rekomendasi / buku terbaru                                  |
+-------------------------------------------------------------+
| Footer                                                      |
+-------------------------------------------------------------+
```

### 5.3 Grid responsif Bootstrap

- Desktop: gunakan `container-fluid` untuk halaman admin dan `container-xl` untuk halaman katalog publik/peminjam.
- Kartu katalog: `row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3` sesuai kepadatan konten.
- Dashboard KPI: `row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3`.
- Form panjang: gunakan satu kolom pada mobile dan dua kolom pada desktop untuk field yang saling berkaitan.
- Tabel admin dibungkus `.table-responsive` agar tidak memaksa layout melebar pada mobile.

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

| Komponen | Bootstrap 5 | Pedoman penggunaan |
|---|---|---|
| Navbar peminjam | `.navbar`, `.navbar-expand-lg`, `.offcanvas` | Navigasi utama guru/siswa |
| Sidebar admin | `.offcanvas-lg` atau sidebar custom berbasis grid | Offcanvas pada layar kecil; panel tetap pada desktop |
| KPI card | `.card`, `.card-body` | Angka besar, label singkat, ikon dekoratif seperlunya |
| Kartu buku | `.card`, `.card-img-top`, `.card-body` | Sampul rasio konsisten dan CTA jelas |
| Tabel | `.table`, `.table-hover`, `.table-responsive` | Header jelas, aksi tidak terlalu padat |
| Form | `.form-label`, `.form-control`, `.form-select` | Selalu gunakan label eksplisit dan pesan validasi |
| Status | `.badge` | Teks status wajib terlihat; warna hanya pendukung |
| Tabs | `.nav`, `.nav-tabs` atau `.nav-pills` | Memisahkan status pinjaman |
| Modal | `.modal` | Konfirmasi singkat atau tindakan kontekstual |
| Toast/alert | `.toast`, `.alert` | Feedback sukses/gagal/informasi |
| Pagination | `.pagination` | Tampilkan halaman aktif dan navigasi yang mudah |
| Empty state | `.text-center`, ilustrasi/icon sederhana | Jelaskan keadaan dan langkah selanjutnya |
| Loading | spinner atau skeleton ringan | Cegah klik ganda ketika permintaan berjalan |

### 7.1 Label status standar

| Status | Label tampilan | Gaya Bootstrap yang disarankan |
|---|---|---|
| Menunggu Diproses | Menunggu | `text-bg-warning` dengan teks gelap bila perlu |
| Disetujui | Disetujui | `text-bg-info` |
| Ditolak | Ditolak | `text-bg-danger` |
| Dipinjam | Dipinjam | `text-bg-primary` |
| Terlambat | Terlambat | `text-bg-danger` |
| Dikembalikan | Dikembalikan | `text-bg-success` |
| Dibatalkan | Dibatalkan | `text-bg-secondary` |
| Tersedia | Tersedia | `text-bg-success` |
| Rusak | Rusak | `text-bg-warning` |
| Hilang | Hilang | `text-bg-danger` |

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
| Mobile, <576px | Navbar offcanvas; KPI satu kolom; katalog dua kolom; tabel menjadi scroll horizontal atau card; form satu kolom |
| Small tablet, ≥576px | KPI dua kolom; katalog dua/tiga kolom; beberapa form dapat dua kolom |
| Tablet/desktop, ≥768px | Area konten lebih luas; filter dapat sejajar; tabel mulai menjadi pola utama |
| Desktop, ≥992px | Sidebar admin tampil; katalog tiga/empat kolom sesuai lebar |
| Wide desktop, ≥1200px | Maksimalisasi keterbacaan; tabel dan dashboard dapat menggunakan beberapa panel |

Ketentuan tambahan:
- Jangan menetapkan lebar tetap pada area konten utama.
- Tombol penting tidak boleh terpotong atau tertutup oleh elemen sticky.
- Modal panjang harus dapat discroll di dalam modal.
- Filter pada mobile dapat ditempatkan di offcanvas atau collapse agar area katalog tetap luas.
- Uji pada ukuran 360px, 390px, 768px, 1024px, dan 1440px.

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

## 10. Panduan Implementasi Bootstrap 5

### 10.1 Dependensi

Gunakan Bootstrap 5 melalui package manager atau file yang dipasang secara lokal. Untuk proyek produksi, versi dependency sebaiknya dikunci di package manifest/lockfile dan diperbarui melalui proses pengujian.

Komponen interaktif Bootstrap seperti offcanvas, dropdown, modal, dan toast memerlukan JavaScript Bootstrap. Bootstrap 5 tidak mewajibkan jQuery. Bootstrap Icons dapat digunakan sebagai pustaka ikon terpisah bila proyek memerlukannya.

### 10.2 CSS custom properties

Tempatkan token desain di satu file, misalnya `resources/css/app.css`.

```css
:root {
  --library-primary: #1f4e79;
  --library-primary-hover: #173b5c;
  --library-secondary: #0f766e;
  --library-app-bg: #f5f7fa;
  --library-surface: #ffffff;
  --library-text: #243244;
  --library-muted: #667085;
  --library-border: #e4e7ec;
  --library-radius: 0.75rem;
}

body {
  color: var(--library-text);
  background: var(--library-app-bg);
}

.card {
  border-color: var(--library-border);
  border-radius: var(--library-radius);
}

.btn-primary {
  --bs-btn-bg: var(--library-primary);
  --bs-btn-border-color: var(--library-primary);
  --bs-btn-hover-bg: var(--library-primary-hover);
  --bs-btn-hover-border-color: var(--library-primary-hover);
}
```

### 10.3 Contoh layout katalog

```html
<main class="container-xl py-4">
  <div class="d-flex flex-column flex-md-row gap-3 align-items-md-end justify-content-between mb-4">
    <div>
      <h1 class="h3 mb-1">Katalog Buku</h1>
      <p class="text-muted mb-0">Temukan buku yang ingin Anda baca.</p>
    </div>
    <form class="d-flex gap-2" role="search">
      <label class="visually-hidden" for="book-search">Cari buku</label>
      <input id="book-search" class="form-control" type="search"
             placeholder="Judul, penulis, ISBN...">
      <button class="btn btn-primary" type="submit">Cari</button>
    </form>
  </div>

  <div class="row g-3">
    <aside class="col-12 col-lg-3">
      <div class="card card-body">
        <h2 class="h6">Filter katalog</h2>
        <label class="form-label" for="category">Kategori</label>
        <select id="category" class="form-select mb-3">
          <option value="">Semua kategori</option>
        </select>
        <label class="form-label" for="availability">Ketersediaan</label>
        <select id="availability" class="form-select">
          <option value="">Semua</option>
          <option value="available">Tersedia</option>
          <option value="unavailable">Tidak tersedia</option>
        </select>
      </div>
    </aside>

    <section class="col-12 col-lg-9" aria-label="Hasil katalog buku">
      <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
        <!-- Render kartu buku dari data aplikasi. -->
      </div>
    </section>
  </div>
</main>
```

### 10.4 Contoh kartu buku

```html
<article class="card h-100">
  <img src="/images/book-placeholder.png"
       class="card-img-top object-fit-cover"
       alt="Sampul buku" loading="lazy">
  <div class="card-body d-flex flex-column">
    <p class="small text-muted mb-1">Kategori</p>
    <h2 class="h6 card-title">Judul Buku</h2>
    <p class="small text-muted mb-3">Nama Penulis</p>
    <div class="mt-auto">
      <span class="badge text-bg-success">Tersedia 3</span>
      <a href="#" class="btn btn-outline-primary btn-sm w-100 mt-3">
        Lihat Detail
      </a>
    </div>
  </div>
</article>
```

### 10.5 Contoh tabel admin

```html
<div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <caption class="visually-hidden">Daftar transaksi peminjaman aktif</caption>
    <thead class="table-light">
      <tr>
        <th scope="col">Peminjam</th>
        <th scope="col">Buku</th>
        <th scope="col">Tanggal Pinjam</th>
        <th scope="col">Jatuh Tempo</th>
        <th scope="col">Status</th>
        <th scope="col"><span class="visually-hidden">Aksi</span></th>
      </tr>
    </thead>
    <tbody>
      <!-- Render baris berdasarkan data dari server. -->
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
│   └── app.css
├── js/
│   └── app.js
└── views/
    ├── layouts/
    │   ├── app.blade.php
    │   ├── admin.blade.php
    │   └── auth.blade.php
    ├── components/
    │   ├── status-badge.blade.php
    │   ├── book-card.blade.php
    │   ├── empty-state.blade.php
    │   ├── page-header.blade.php
    │   └── confirm-modal.blade.php
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
    │   ├── catalog/
    │   ├── loans/
    │   └── profile.blade.php
    └── auth/
        └── login.blade.php
```

Pisahkan komponen yang berulang (badge status, kartu buku, header halaman, empty state) agar perubahan visual dapat diterapkan konsisten. Hindari menaruh seluruh markup aplikasi dalam satu file view besar.

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

### Alur pengguna
- [ ] Guru/siswa dapat mencapai katalog dari beranda dalam satu aksi yang jelas.
- [ ] Informasi ketersediaan tampak sebelum pengguna mengajukan peminjaman.
- [ ] Pengguna memahami bahwa pengajuan belum berarti buku telah dipinjam.
- [ ] Admin dapat menemukan permintaan, checkout, dan pengembalian tanpa berpindah menu berlebihan.
- [ ] Pengguna dapat melihat tanggal jatuh tempo dan status pinjaman pribadi.

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
