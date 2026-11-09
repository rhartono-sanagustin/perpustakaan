# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## Sistem Informasi Perpustakaan Sekolah

| Informasi | Keterangan |
|---|---|
| Nama produk | Sistem Informasi Perpustakaan Sekolah |
| Dokumen | Product Requirements Document (`prd.md`) |
| Versi | 1.0 |
| Tanggal | 7 November 2026 |
| Status | Draft kebutuhan awal untuk ditinjau |
| Platform | Aplikasi web responsif |
| Bahasa antarmuka | Bahasa Indonesia |
| Pengguna utama | Admin, Guru, dan Siswa |

---

## 1. Ringkasan Produk

Sistem Informasi Perpustakaan Sekolah adalah aplikasi berbasis web untuk membantu sekolah mengelola koleksi buku, proses peminjaman dan pengembalian, serta pelaporan aktivitas perpustakaan. Aplikasi menyediakan katalog buku yang dapat ditelusuri oleh guru dan siswa, fasilitas pengajuan peminjaman, pengelolaan transaksi oleh admin, dan laporan operasional untuk mendukung pengambilan keputusan.

Produk dirancang dengan tiga jenis peran:

1. **Admin** — mengelola data master, koleksi dan eksemplar buku, akun pengguna, permintaan peminjaman, transaksi peminjaman/pengembalian, konfigurasi, serta laporan.
2. **Guru** — menelusuri katalog, melihat ketersediaan buku, mengajukan peminjaman, serta memantau pinjaman dan riwayat miliknya.
3. **Siswa** — menelusuri katalog, melihat ketersediaan, mengajukan peminjaman, serta memantau pinjaman dan riwayat miliknya.

Dokumen ini menjadi acuan bagi pemilik produk, analis, UI/UX designer, developer, dan tester. Detail yang belum ditetapkan sekolah diberi tanda sebagai asumsi atau konfigurasi agar dapat disesuaikan sebelum implementasi.

## 2. Latar Belakang dan Permasalahan

Pengelolaan perpustakaan yang dilakukan secara manual atau melalui beberapa berkas terpisah berpotensi menimbulkan masalah berikut:

- Informasi koleksi buku dan jumlah eksemplar tidak selalu mutakhir.
- Guru dan siswa kesulitan mengetahui buku yang tersedia tanpa bertanya langsung kepada petugas.
- Pencatatan peminjaman dan pengembalian membutuhkan waktu serta rentan terhadap kesalahan.
- Petugas kesulitan mengidentifikasi keterlambatan pengembalian dan riwayat transaksi.
- Pembuatan laporan koleksi dan aktivitas peminjaman membutuhkan rekapitulasi manual.

Aplikasi diharapkan menyediakan satu sumber data yang konsisten untuk katalog, inventaris eksemplar, transaksi, dan laporan perpustakaan.

## 3. Tujuan Produk

### 3.1 Tujuan utama

1. Menyediakan katalog buku yang mudah dicari dan menampilkan ketersediaan secara informatif.
2. Mendigitalisasi proses pengajuan, pencatatan peminjaman, dan pengembalian buku.
3. Membantu admin memantau transaksi aktif, keterlambatan, dan kondisi koleksi.
4. Menyediakan laporan yang dapat difilter dan diekspor untuk kebutuhan sekolah.
5. Menyediakan pengalaman yang mudah digunakan melalui komputer maupun perangkat seluler.

### 3.2 Indikator keberhasilan

| Indikator | Ukuran keberhasilan awal |
|---|---|
| Kelengkapan katalog | Seluruh koleksi aktif yang dikelola perpustakaan tercatat di aplikasi |
| Akurasi ketersediaan | Jumlah tersedia di aplikasi sesuai dengan inventaris fisik yang telah direkonsiliasi |
| Pencatatan transaksi | Seluruh transaksi pinjam dan kembali dicatat melalui aplikasi |
| Keterlacakan pinjaman | Admin dapat menemukan peminjam, buku, tanggal pinjam, dan jatuh tempo dari setiap transaksi |
| Pelaporan | Laporan utama dapat difilter berdasarkan periode dan diekspor |
| Kemudahan penggunaan | Guru dan siswa dapat menemukan buku dan mengajukan pinjaman tanpa bantuan teknis khusus |

Target numerik seperti waktu respons atau tingkat keberhasilan tugas dapat ditentukan setelah uji coba awal.

## 4. Ruang Lingkup

### 4.1 Termasuk dalam MVP

- Login dan logout.
- Hak akses berbasis peran: admin, guru, siswa.
- Dashboard sesuai peran.
- Katalog buku, pencarian, filter, detail buku, dan informasi ketersediaan.
- Pengelolaan data buku, kategori, penulis, penerbit, dan eksemplar oleh admin.
- Pengajuan peminjaman oleh guru dan siswa.
- Pemeriksaan dan pemrosesan permintaan peminjaman oleh admin.
- Pencatatan penyerahan buku dan pengembalian buku oleh admin.
- Daftar pinjaman aktif, riwayat transaksi, dan daftar keterlambatan.
- Pengelolaan akun dan status pengguna oleh admin.
- Laporan katalog/inventaris dan transaksi peminjaman/pengembalian.
- Ekspor laporan ke CSV dan tampilan siap cetak.
- Pengaturan kebijakan peminjaman dasar.

### 4.2 Tidak termasuk dalam MVP

Fitur berikut dapat dipertimbangkan sebagai pengembangan lanjutan:

- Integrasi kartu anggota atau kartu pelajar dengan RFID/NFC.
- Pemindaian barcode/QR untuk checkout dan pengembalian otomatis.
- Notifikasi WhatsApp, SMS, atau email otomatis.
- Pembayaran denda secara daring.
- Integrasi dengan sistem akademik, SSO sekolah, atau aplikasi pemerintah.
- Rekomendasi buku berbasis AI.
- Aplikasi mobile native.
- Pengadaan buku, akuntansi, dan manajemen anggaran perpustakaan.

## 5. Pengguna, Peran, dan Hak Akses

### 5.1 Deskripsi pengguna

| Peran | Kebutuhan utama | Tingkat akses |
|---|---|---|
| Admin perpustakaan | Mengelola data, memproses pinjam/kembali, memantau stok, dan membuat laporan | Seluruh modul operasional dan pengaturan |
| Guru | Mencari buku, melihat ketersediaan, mengajukan dan memantau pinjaman sendiri | Katalog dan data transaksi milik sendiri |
| Siswa | Mencari buku, melihat ketersediaan, mengajukan dan memantau pinjaman sendiri | Katalog dan data transaksi milik sendiri |

### 5.2 Matriks hak akses

| Fitur | Admin | Guru | Siswa |
|---|:---:|:---:|:---:|
| Melihat dan mencari katalog | Ya | Ya | Ya |
| Melihat detail/ketersediaan buku | Ya | Ya | Ya |
| Mengajukan peminjaman | Opsional/atas nama pengguna | Ya | Ya |
| Melihat pinjaman sendiri | Ya | Ya | Ya |
| Melihat seluruh transaksi | Ya | Tidak | Tidak |
| Mengelola data buku dan eksemplar | Ya | Tidak | Tidak |
| Mengelola kategori/penulis/penerbit | Ya | Tidak | Tidak |
| Memproses permintaan peminjaman | Ya | Tidak | Tidak |
| Mencatat penyerahan dan pengembalian | Ya | Tidak | Tidak |
| Mengelola akun pengguna | Ya | Tidak | Tidak |
| Melihat laporan agregat perpustakaan | Ya | Tidak | Tidak |
| Mengubah kebijakan peminjaman | Ya | Tidak | Tidak |

**Prinsip privasi:** guru dan siswa hanya dapat melihat detail transaksi miliknya sendiri. Guru tidak otomatis memperoleh akses untuk melihat transaksi siswa. Akses tambahan harus ditetapkan secara eksplisit oleh sekolah.

## 6. Alur Proses Utama

### 6.1 Penelusuran dan pengajuan peminjaman

1. Guru/siswa login.
2. Pengguna membuka katalog dan mencari buku berdasarkan judul, penulis, ISBN, kategori, atau kata kunci.
3. Sistem menampilkan detail buku dan jumlah eksemplar yang tersedia.
4. Pengguna memilih **Ajukan Peminjaman** jika buku tersedia dan kuota pinjaman belum terlampaui.
5. Sistem membuat permintaan dengan status **Menunggu Diproses**.
6. Admin memeriksa permintaan, status akun, kuota pengguna, dan ketersediaan eksemplar.
7. Admin menyetujui atau menolak permintaan dengan alasan jika ditolak.
8. Ketika buku benar-benar diserahkan kepada peminjam, admin mencatat checkout. Status transaksi berubah menjadi **Dipinjam**, dan tanggal jatuh tempo ditetapkan.

> Pengajuan bukan berarti buku sudah dipinjam. Status **Dipinjam** hanya berlaku setelah admin mencatat penyerahan buku.

### 6.2 Pengembalian buku

1. Peminjam menyerahkan buku kepada admin.
2. Admin menemukan transaksi aktif berdasarkan nama/ID peminjam, judul, atau kode eksemplar.
3. Admin memeriksa kondisi buku.
4. Admin mencatat tanggal pengembalian dan kondisi buku.
5. Sistem menutup transaksi dan memperbarui status eksemplar menjadi **Tersedia**, **Rusak**, atau **Hilang** sesuai hasil pemeriksaan.
6. Sistem memperbarui status keterlambatan dan data laporan.

### 6.3 Pengelolaan katalog

1. Admin menambahkan data bibliografi buku.
2. Admin mencatat satu atau lebih eksemplar untuk judul tersebut.
3. Sistem membuat atau memvalidasi kode inventaris yang unik untuk setiap eksemplar.
4. Buku tampil di katalog apabila status katalog aktif.
5. Jumlah tersedia dihitung dari eksemplar berstatus tersedia, bukan dari jumlah judul buku.

## 7. Kebutuhan Fungsional

Prioritas: **Must** = wajib untuk MVP, **Should** = penting tetapi dapat menyusul, **Could** = peningkatan opsional.

### 7.1 Autentikasi dan akun

| ID | Kebutuhan | Prioritas | Kriteria penerimaan ringkas |
|---|---|---|---|
| FR-AUTH-01 | Pengguna dapat login menggunakan identitas akun dan kata sandi | Must | Kredensial valid membawa pengguna ke dashboard sesuai peran |
| FR-AUTH-02 | Pengguna dapat logout | Must | Sesi berakhir dan halaman terlindungi tidak dapat diakses kembali tanpa login |
| FR-AUTH-03 | Sistem menerapkan hak akses berdasarkan peran | Must | Pengguna tidak dapat membuka fitur di luar hak aksesnya, termasuk melalui URL langsung |
| FR-AUTH-04 | Admin dapat membuat, mengubah, menonaktifkan, dan mengatur ulang akun pengguna | Must | Perubahan status akun berlaku pada proses login dan pengajuan pinjaman |
| FR-AUTH-05 | Pengguna dapat melihat dan memperbarui profil terbatas | Should | Pengguna hanya dapat mengubah kolom yang diizinkan sekolah |

### 7.2 Katalog buku

| ID | Kebutuhan | Prioritas | Kriteria penerimaan ringkas |
|---|---|---|---|
| FR-CAT-01 | Guru dan siswa dapat melihat katalog buku aktif | Must | Daftar hanya memuat judul yang status katalognya aktif |
| FR-CAT-02 | Pengguna dapat mencari berdasarkan judul, penulis, ISBN, dan kata kunci | Must | Hasil pencarian sesuai kata kunci tanpa membedakan huruf besar/kecil |
| FR-CAT-03 | Pengguna dapat memfilter katalog berdasarkan kategori dan ketersediaan | Must | Filter dapat digunakan bersamaan dan dapat direset |
| FR-CAT-04 | Pengguna dapat melihat detail buku | Must | Detail menampilkan metadata, ringkasan, lokasi rak, dan ketersediaan |
| FR-CAT-05 | Admin dapat menambah, mengubah, dan mengarsipkan data buku | Must | Data wajib tervalidasi; buku yang sudah memiliki transaksi tidak dihapus permanen |
| FR-CAT-06 | Admin dapat mengelola kategori, penulis, dan penerbit | Must | Data master dapat digunakan kembali pada beberapa judul |
| FR-CAT-07 | Admin dapat mengelola eksemplar dan kode inventaris | Must | Setiap eksemplar memiliki kode unik dan status tersendiri |
| FR-CAT-08 | Sistem menampilkan jumlah total, tersedia, dipinjam, dan tidak aktif | Must | Ringkasan berasal dari status eksemplar terkini |
| FR-CAT-09 | Pengguna dapat melihat gambar sampul buku | Should | Jika sampul tidak tersedia, sistem menampilkan placeholder |

**Data minimum buku:** judul, kategori, penulis, penerbit, tahun terbit (bila diketahui), ISBN (opsional untuk koleksi yang tidak memilikinya), deskripsi/ringkasan, bahasa, gambar sampul (opsional), dan status katalog.

**Data minimum eksemplar:** kode inventaris unik, lokasi/rak, kondisi, status, serta catatan bila diperlukan.

### 7.3 Pengajuan dan transaksi peminjaman

| ID | Kebutuhan | Prioritas | Kriteria penerimaan ringkas |
|---|---|---|---|
| FR-LOAN-01 | Guru/siswa dapat mengajukan peminjaman buku yang tersedia | Must | Pengajuan ditolak sistem jika akun tidak aktif, kuota habis, atau tidak ada eksemplar tersedia |
| FR-LOAN-02 | Pengguna dapat melihat status pengajuan | Must | Status dan waktu perubahan status tampil pada akun peminjam |
| FR-LOAN-03 | Admin dapat melihat antrean permintaan | Must | Admin dapat memfilter permintaan berdasarkan status, tanggal, dan pengguna |
| FR-LOAN-04 | Admin dapat menyetujui atau menolak permintaan | Must | Penolakan memerlukan alasan; persetujuan tidak langsung menandai buku telah dipinjam |
| FR-LOAN-05 | Admin dapat mencatat penyerahan buku (checkout) | Must | Sistem menetapkan tanggal pinjam, tanggal jatuh tempo, peminjam, dan eksemplar |
| FR-LOAN-06 | Admin dapat mencatat pengembalian buku | Must | Transaksi ditutup dan status eksemplar diperbarui sesuai kondisi |
| FR-LOAN-07 | Pengguna dapat melihat daftar pinjaman aktif dan riwayat miliknya | Must | Data hanya menampilkan transaksi milik akun tersebut |
| FR-LOAN-08 | Sistem menandai pinjaman yang melewati jatuh tempo | Must | Status terlambat dihitung dari tanggal jatuh tempo dan tanggal pengembalian |
| FR-LOAN-09 | Pengguna dapat mengajukan perpanjangan pinjaman | Could | Perpanjangan hanya dapat dilakukan jika kebijakan mengizinkan dan tidak ada permintaan/reservasi lain |
| FR-LOAN-10 | Admin dapat menginput transaksi langsung atas nama pengguna | Should | Transaksi tetap merekam admin pemroses dan pengguna peminjam |

### 7.4 Laporan

| ID | Kebutuhan | Prioritas | Kriteria penerimaan ringkas |
|---|---|---|---|
| FR-REP-01 | Admin dapat melihat dashboard ringkasan perpustakaan | Must | Menampilkan jumlah judul, eksemplar tersedia, pinjaman aktif, dan keterlambatan |
| FR-REP-02 | Admin dapat melihat laporan peminjaman dan pengembalian berdasarkan periode | Must | Filter tanggal awal dan akhir bekerja dengan benar |
| FR-REP-03 | Admin dapat melihat laporan keterlambatan | Must | Menampilkan peminjam, kontak/identitas yang diizinkan, buku, dan jumlah hari terlambat |
| FR-REP-04 | Admin dapat melihat laporan inventaris | Must | Menampilkan judul, jumlah eksemplar, tersedia, dipinjam, rusak, dan hilang |
| FR-REP-05 | Admin dapat melihat buku paling sering dipinjam | Should | Peringkat dihitung dari transaksi checkout dalam periode terpilih |
| FR-REP-06 | Admin dapat mengekspor laporan ke CSV | Must | File berisi data sesuai filter yang diterapkan |
| FR-REP-07 | Admin dapat mencetak laporan | Should | Halaman cetak menyertakan judul laporan, periode, tanggal cetak, dan tabel data |

### 7.5 Pengaturan perpustakaan

| ID | Kebutuhan | Prioritas | Kriteria penerimaan ringkas |
|---|---|---|---|
| FR-SET-01 | Admin dapat mengatur jumlah maksimal pinjaman berdasarkan peran | Must | Validasi kuota menggunakan konfigurasi aktif |
| FR-SET-02 | Admin dapat mengatur lama peminjaman berdasarkan peran | Must | Tanggal jatuh tempo dihitung berdasarkan durasi yang berlaku saat checkout |
| FR-SET-03 | Admin dapat mengatur kebijakan denda atau menonaktifkannya | Could | Denda tidak diterapkan jika kebijakan sekolah belum ditetapkan |
| FR-SET-04 | Sistem menyimpan jejak aktivitas penting | Should | Perubahan data master dan tindakan transaksi memiliki aktor serta waktu |

## 8. Aturan Bisnis

Aturan berikut adalah **nilai awal yang disarankan**, bukan ketentuan baku sekolah. Admin berwenang mengubahnya sebelum sistem digunakan.

| Kode | Aturan awal | Nilai awal |
|---|---|---|
| BR-01 | Maksimal buku yang dapat dipinjam siswa pada waktu bersamaan | 2 eksemplar |
| BR-02 | Maksimal buku yang dapat dipinjam guru pada waktu bersamaan | 5 eksemplar |
| BR-03 | Durasi peminjaman siswa | 7 hari kalender |
| BR-04 | Durasi peminjaman guru | 14 hari kalender |
| BR-05 | Perpanjangan pinjaman | Tidak aktif pada MVP |
| BR-06 | Denda keterlambatan | Tidak diterapkan sampai sekolah menetapkan kebijakannya |
| BR-07 | Persetujuan permintaan | Dilakukan admin sebelum penyerahan buku |
| BR-08 | Penetapan tanggal pinjam | Pada saat buku diserahkan dan checkout dicatat admin |
| BR-09 | Pengajuan pengguna | Tidak boleh melebihi kuota atau meminta eksemplar yang tidak tersedia |
| BR-10 | Penghapusan data | Data yang terkait transaksi diarsipkan/dinonaktifkan, bukan dihapus permanen |

Aturan tambahan:

1. Pengguna harus memiliki akun aktif untuk mengajukan peminjaman.
2. Setiap transaksi checkout mengacu pada satu pengguna dan satu eksemplar. Untuk beberapa buku, sistem membuat detail transaksi per eksemplar atau struktur transaksi-header dan detail secara konsisten.
3. Satu eksemplar tidak boleh memiliki lebih dari satu transaksi pinjaman aktif.
4. Saat checkout, sistem memvalidasi ketersediaan ulang untuk mencegah dua admin meminjamkan eksemplar yang sama secara bersamaan.
5. Permintaan yang disetujui harus memiliki batas waktu pengambilan yang dapat dikonfigurasi. Sebagai nilai awal, admin dapat membatalkan persetujuan yang tidak diambil setelah 1 hari operasional.
6. Keterlambatan dihitung ketika tanggal saat ini melewati tanggal jatuh tempo dan transaksi belum dikembalikan.
7. Status **Terlambat** merupakan status turunan dari transaksi aktif, bukan status eksemplar. Eksemplar tetap berstatus **Dipinjam**.
8. Eksemplar rusak atau hilang tidak dihitung sebagai tersedia.
9. Tanggal dan waktu disimpan secara konsisten; tampilan menggunakan zona waktu sekolah.
10. Kebijakan yang berlaku pada suatu transaksi (durasi dan kuota) sebaiknya disimpan sebagai snapshot atau nilai transaksi agar perubahan konfigurasi tidak mengubah transaksi lama.

## 9. Status dan Definisi Data

### 9.1 Status permintaan/transaksi

| Status | Makna |
|---|---|
| Menunggu Diproses | Permintaan diajukan pengguna dan belum ditangani admin |
| Disetujui | Permintaan disetujui, tetapi buku belum dicatat diserahkan |
| Ditolak | Permintaan ditolak admin; alasan penolakan disimpan |
| Dipinjam | Buku telah diserahkan dan checkout dicatat |
| Dikembalikan | Buku telah diterima kembali dan transaksi ditutup |
| Dibatalkan | Permintaan dibatalkan pengguna/admin sebelum checkout |

Label **Terlambat** ditampilkan sebagai penanda pada transaksi berstatus Dipinjam ketika tanggal jatuh tempo terlewati.

### 9.2 Status eksemplar

- **Tersedia** — dapat dipinjam.
- **Dipinjam** — sedang berada pada peminjam.
- **Rusak** — tidak dapat dipinjam sampai diperbaiki dan diaktifkan kembali.
- **Hilang** — tidak tersedia karena hilang.
- **Nonaktif** — dikeluarkan dari peredaran/inventaris aktif.

### 9.3 Entitas data inti (konseptual)

| Entitas | Atribut inti | Relasi utama |
|---|---|---|
| User | id, nama, username/email, password_hash, role, nomor_induk, status | Memiliki banyak pengajuan dan transaksi |
| Book | id, ISBN, judul, penerbit_id, tahun, ringkasan, bahasa, cover, kategori_id, status | Memiliki banyak penulis dan eksemplar |
| Category | id, nama, deskripsi | Memiliki banyak buku |
| Author | id, nama | Berelasi banyak-ke-banyak dengan buku |
| Publisher | id, nama | Memiliki banyak buku |
| BookCopy | id, book_id, kode_inventaris, lokasi_rak, kondisi, status | Satu eksemplar dapat memiliki riwayat transaksi |
| LoanRequest | id, user_id, book_id atau copy_id, tanggal_pengajuan, status, alasan | Dimiliki satu pengguna; diproses admin |
| Loan | id, user_id, copy_id, processed_by, tanggal_pinjam, jatuh_tempo, tanggal_kembali, status | Menghubungkan pengguna, eksemplar, dan admin pemroses |
| LoanEvent/AuditLog | id, actor_id, entity, entity_id, action, old_value, new_value, created_at | Merekam aktivitas penting |
| LibrarySetting | id, key, value, updated_by | Menyimpan kebijakan aktif |

Catatan desain data: struktur tabel final perlu diselaraskan dengan ORM dan rancangan basis data. Relasi buku-penulis umumnya many-to-many. Permintaan dapat diarahkan ke judul terlebih dahulu; eksemplar tertentu ditetapkan saat admin checkout. Ini menghindari penguncian eksemplar terlalu dini.

## 10. Kebutuhan Nonfungsional

| ID | Aspek | Kebutuhan |
|---|---|---|
| NFR-SEC-01 | Keamanan | Kata sandi disimpan menggunakan mekanisme hashing yang aman; jangan menyimpan kata sandi teks biasa |
| NFR-SEC-02 | Otorisasi | Semua endpoint dan tindakan penting memeriksa autentikasi serta hak akses di sisi server |
| NFR-SEC-03 | Validasi | Input divalidasi di sisi server; validasi sisi klien hanya sebagai bantuan UX |
| NFR-SEC-04 | Privasi | Guru/siswa tidak dapat mengakses profil atau transaksi pengguna lain tanpa kewenangan |
| NFR-SEC-05 | Audit | Tindakan penting admin pada data dan transaksi tercatat |
| NFR-PERF-01 | Kinerja | Pencarian dan daftar data menggunakan pagination; indeks database diterapkan pada kolom pencarian/filter yang relevan |
| NFR-PERF-02 | Respons | Halaman utama harus tetap nyaman digunakan pada koneksi sekolah yang terbatas; gambar sampul dioptimalkan |
| NFR-UX-01 | Responsif | Antarmuka mendukung ponsel, tablet, laptop, dan desktop |
| NFR-UX-02 | Bahasa | Label, validasi, dan pesan sistem menggunakan Bahasa Indonesia yang jelas |
| NFR-UX-03 | Aksesibilitas | Form memiliki label, fokus keyboard terlihat, kontras memadai, dan status tidak hanya dibedakan dengan warna |
| NFR-REL-01 | Integritas | Proses checkout/pengembalian menggunakan transaksi database untuk menjaga konsistensi stok |
| NFR-REL-02 | Backup | Database dicadangkan secara berkala dan prosedur pemulihan diuji oleh pengelola |
| NFR-MAINT-01 | Pemeliharaan | Struktur kode modular, konfigurasi kebijakan terpisah dari kode, dan perubahan penting terdokumentasi |
| NFR-COMP-01 | Kompatibilitas | Mendukung versi modern browser Chrome, Edge, Firefox, dan Safari |

## 11. Kebutuhan Pelaporan Secara Rinci

### 11.1 Jenis laporan

1. **Ringkasan operasional:** jumlah judul, total eksemplar, eksemplar tersedia, dipinjam, rusak/hilang, transaksi aktif, dan keterlambatan.
2. **Laporan peminjaman:** nomor transaksi, peminjam, peran, kelas/identitas pengguna, judul, kode inventaris, tanggal pinjam, jatuh tempo, dan status.
3. **Laporan pengembalian:** peminjam, judul, tanggal pinjam, tanggal kembali, kondisi saat kembali, dan admin penerima.
4. **Laporan keterlambatan:** peminjam, judul, tanggal jatuh tempo, jumlah hari terlambat, dan status tindak lanjut.
5. **Laporan inventaris:** judul, kategori, kode inventaris, lokasi, kondisi, dan status setiap eksemplar.
6. **Laporan buku populer:** judul dan jumlah checkout pada periode terpilih.

### 11.2 Filter minimum

- Rentang tanggal.
- Status transaksi.
- Kategori buku.
- Peran pengguna.
- Kelas/tingkat atau unit kerja (apabila data tersebut digunakan sekolah).
- Kata kunci judul atau nama peminjam.

Laporan harus menampilkan periode filter dan waktu pembuatan laporan. Data ekspor harus mengikuti filter yang sedang aktif.

## 12. Acceptance Criteria MVP

MVP dapat dinyatakan layak untuk uji coba operasional jika seluruh kondisi berikut terpenuhi:

- [ ] Admin, guru, dan siswa dapat login dan memperoleh menu sesuai perannya.
- [ ] Guru dan siswa dapat mencari buku serta melihat ketersediaan eksemplar.
- [ ] Buku, kategori, penulis, penerbit, dan eksemplar dapat dikelola admin.
- [ ] Pengguna dapat mengajukan peminjaman dan melihat status pengajuan.
- [ ] Admin dapat menyetujui/menolak permintaan serta mencatat checkout.
- [ ] Admin dapat mencatat pengembalian dan kondisi buku.
- [ ] Sistem menolak checkout jika eksemplar sudah dipinjam atau pengguna melampaui kuota.
- [ ] Pinjaman yang melewati tanggal jatuh tempo ditandai terlambat.
- [ ] Guru/siswa hanya dapat melihat transaksi miliknya sendiri.
- [ ] Admin dapat melihat dan mengekspor laporan utama.
- [ ] Tampilan tetap dapat digunakan pada layar ponsel dan desktop.
- [ ] Validasi, pesan gagal, data kosong, dan status proses ditampilkan dengan jelas.

## 13. Skenario Pengujian Prioritas

| ID | Skenario | Hasil yang diharapkan |
|---|---|---|
| TC-01 | Siswa login dengan akun aktif | Berhasil masuk ke dashboard siswa |
| TC-02 | Siswa mencoba membuka URL laporan admin | Akses ditolak |
| TC-03 | Pengguna mencari judul buku yang tersedia | Hasil relevan muncul dan jumlah tersedia benar |
| TC-04 | Siswa mengajukan buku ketika kuota masih ada | Permintaan tercatat sebagai Menunggu Diproses |
| TC-05 | Siswa mengajukan buku ketika kuota penuh | Sistem menolak dan menampilkan alasan |
| TC-06 | Admin menyetujui permintaan | Status menjadi Disetujui, belum menjadi Dipinjam |
| TC-07 | Admin checkout eksemplar tersedia | Status eksemplar menjadi Dipinjam dan jatuh tempo ditetapkan |
| TC-08 | Admin mencoba checkout eksemplar yang telah dipinjam | Sistem menolak tindakan dan menjaga integritas stok |
| TC-09 | Admin mencatat pengembalian buku baik | Transaksi ditutup dan eksemplar menjadi Tersedia |
| TC-10 | Admin mencatat pengembalian buku rusak | Transaksi ditutup dan eksemplar menjadi Rusak |
| TC-11 | Pinjaman melewati tanggal jatuh tempo | Transaksi tampil dengan penanda Terlambat |
| TC-12 | Guru mencoba melihat transaksi siswa lain | Akses ditolak |
| TC-13 | Admin mengekspor laporan dengan filter periode | CSV berisi data sesuai filter |
| TC-14 | Pengguna membuka katalog pada layar ponsel | Pencarian, filter, dan kartu buku tetap dapat digunakan |

## 14. Asumsi, Ketergantungan, dan Risiko

### 14.1 Asumsi

- Sekolah menunjuk minimal satu admin/petugas perpustakaan.
- Admin memasukkan atau mengimpor data buku dan pengguna sebelum operasional penuh.
- Guru dan siswa memiliki akun masing-masing.
- Sekolah memiliki koneksi internet/intranet dan perangkat untuk mengakses aplikasi.
- Pengembalian fisik tetap dilakukan di perpustakaan; aplikasi hanya mencatat prosesnya.
- Kebijakan jumlah buku, durasi pinjam, kalender operasional, dan denda dapat berbeda antar sekolah.

### 14.2 Ketergantungan

- Data identitas pengguna dari sekolah (misalnya NIS/NISN/NIP atau nomor internal) jika akan digunakan sebagai identitas unik.
- Data inventaris buku yang cukup akurat.
- Keputusan sekolah terkait durasi pinjam, kuota, denda, dan penanganan buku rusak/hilang.
- Penetapan tata cara admin memproses permintaan dan pengembalian.

### 14.3 Risiko dan mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Data inventaris awal tidak akurat | Informasi ketersediaan salah | Lakukan stock opname dan rekonsiliasi sebelum go-live |
| Pengguna tidak memahami status permintaan vs pinjaman | Pengguna mengira buku sudah di tangan | Gunakan label dan penjelasan status yang eksplisit |
| Dua admin memproses eksemplar sama | Stok tidak konsisten | Validasi ulang dan transaksi database saat checkout |
| Akun digunakan bersama | Riwayat transaksi tidak akurat | Akun individual dan kebijakan keamanan akun |
| Kebijakan pinjam belum disepakati | Perbedaan perlakuan antar pengguna | Tetapkan konfigurasi dan SOP sebelum go-live |
| Koneksi sekolah tidak stabil | Akses aplikasi terganggu | Optimalkan halaman, pagination, dan strategi backup/pemulihan |

## 15. Tahapan Implementasi yang Disarankan

| Tahap | Fokus | Hasil |
|---|---|---|
| 1. Fondasi | Autentikasi, role, layout, data master | Login dan struktur data dasar berfungsi |
| 2. Katalog | Buku, kategori, penulis, penerbit, eksemplar, pencarian | Katalog dapat digunakan dan stok terkelola |
| 3. Sirkulasi | Pengajuan, persetujuan, checkout, pengembalian | Alur peminjaman dan pengembalian berfungsi |
| 4. Pelaporan | Dashboard, filter, laporan, ekspor | Admin dapat memantau operasional |
| 5. Uji coba | UAT admin/guru/siswa, perbaikan, pelatihan | Sistem siap digunakan secara terbatas |

## 16. Pertanyaan yang Perlu Diputuskan Sebelum Produksi

1. Apakah siswa dan guru sama-sama boleh mengajukan peminjaman melalui aplikasi, atau pengajuan hanya dicatat oleh petugas?
2. Berapa batas pinjaman dan durasi pinjam resmi untuk masing-masing peran/jenjang?
3. Apakah sekolah menerapkan denda keterlambatan, sanksi, atau hanya pencatatan keterlambatan?
4. Apakah permintaan yang disetujui perlu memiliki batas waktu pengambilan?
5. Apakah satu judul boleh diminta ketika semua eksemplarnya sedang dipinjam (daftar tunggu/reservasi)?
6. Apakah identitas pengguna diintegrasikan dengan data akademik, atau dikelola manual oleh admin?
7. Apakah sekolah membutuhkan pencetakan kartu anggota, barcode, atau QR pada tahap awal?
8. Apakah laporan perlu dikelompokkan berdasarkan kelas, tingkat, jurusan, atau unit kerja?

---

**Akhir dokumen PRD.** Perubahan terhadap ruang lingkup, kebijakan pinjam, dan hak akses perlu disepakati pemilik produk sebelum implementasi.
