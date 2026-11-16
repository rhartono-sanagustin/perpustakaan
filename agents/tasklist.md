# Tasklist — Sistem Informasi Perpustakaan Sekolah

**Terakhir diperbarui:** 16 November 2026  
**Progress keseluruhan:** 100% (142 dari 142 task)  
**Fase saat ini:** Fase 7 — Kartu Anggota, Kunjungan & Finalisasi MVP (selesai; siap direview)

> Catatan: progress dihitung dari jumlah checklist task, bukan estimasi jam kerja.
> Setiap task selesai ditandai `[x] ✅` dan diberi catatan file yang dibuat/diubah.

---

## 1. Project Setup & Configuration

- [x] ✅ Task 1.1 - Install Bootstrap 5 + Bootstrap Icons dan hapus Tailwind `[Mudah]` (Selesai)
  - File: `package.json`, `vite.config.js`
- [x] ✅ Task 1.2 - Konfigurasi Vite untuk CSS/JS aplikasi `[Mudah]` (Selesai)
  - File: `vite.config.js`, `resources/js/app.js`
- [x] ✅ Task 1.3 - Buat design token dan style dasar sesuai `design.md` `[Sedang]` (Selesai)
  - File: `resources/css/app.css`
- [x] ✅ Task 1.4 - Import Bootstrap JS, toast, password toggle, dan loading state `[Sedang]` (Selesai)
  - File: `resources/js/app.js`
- [x] ✅ Task 1.5 - Konfigurasi `.env` dan `.env.example` (nama aplikasi, locale, timezone, identitas sekolah) `[Mudah]` (Selesai)
  - File: `.env`, `.env.example`
- [x] ✅ Task 1.6 - Tambah `config/library.php` dan timezone env di `config/app.php` `[Mudah]` (Selesai)
  - File: `config/library.php`, `config/app.php`
- [x] ✅ Task 1.7 - Publikasi dan terjemahan Bahasa Indonesia (`lang/id`) `[Sedang]` (Selesai)
  - File: `lang/id/auth.php`, `lang/id/validation.php`, `lang/id/passwords.php`, `lang/id/pagination.php`, `lang/id/messages.php`
- [x] ✅ Task 1.8 - Pagination Bootstrap dan locale Carbon `[Mudah]` (Selesai)
  - File: `app/Providers/AppServiceProvider.php`
- [x] ✅ Task 1.9 - Konfigurasi database test khusus `perpustakaan_test` `[Sedang]` (Selesai)
  - File: `phpunit.xml`
  - Catatan: PHP lokal tidak memiliki `pdo_sqlite`; digunakan database MySQL khusus test agar DB dev tidak tersentuh.
- [x] ✅ Task 1.10 - Daftarkan middleware role/active dan security headers `[Sedang]` (Selesai)
  - File: `bootstrap/app.php`, `app/Http/Middleware/EnsureUserIsActive.php`, `app/Http/Middleware/EnsureUserHasRole.php`, `app/Http/Middleware/SecurityHeaders.php`
- [x] ✅ Task 1.11 - Tambah aset logo dan placeholder sampul `[Mudah]` (Selesai)
  - File: `public/images/logo.svg`, `public/images/book-placeholder.svg`
- [x] ✅ Task 1.12 - Buat dan kelola tasklist `[Mudah]` (Selesai)
  - File: `agents/tasklist.md`

---

## 2. Database & Migration

- [x] ✅ Task 2.1 - Migration atribut akun perpustakaan pada tabel `users` `[Sedang]` (Selesai)
  - File: `database/migrations/2026_11_08_000001_add_library_fields_to_users_table.php`
  - Kolom: `role`, `nomor_induk`, `status`, `kelas`, `unit_kerja`, `no_telepon`; email nullable.
- [x] ✅ Task 2.2 - Migration tabel `categories` (Selesai)
  - File: `database/migrations/2026_11_09_000001_create_categories_table.php`
- [x] ✅ Task 2.3 - Migration tabel `authors` (Selesai)
  - File: `database/migrations/2026_11_09_000002_create_authors_table.php`
- [x] ✅ Task 2.4 - Migration tabel `publishers` (Selesai)
  - File: `database/migrations/2026_11_09_000003_create_publishers_table.php`
- [x] ✅ Task 2.5 - Migration tabel `books` (Selesai)
  - File: `database/migrations/2026_11_09_000004_create_books_table.php`
- [x] ✅ Task 2.6 - Migration pivot `book_author` (Selesai)
  - File: `database/migrations/2026_11_09_000005_create_book_author_table.php`
  - Catatan: pivot tanpa timestamp; relasi many-to-many menyebut tabel `book_author` secara eksplisit.
- [x] ✅ Task 2.7 - Migration tabel `book_copies` (Selesai)
  - File: `database/migrations/2026_11_09_000006_create_book_copies_table.php`
- [x] ✅ Task 2.8 - Migration tabel `loan_requests` (Selesai)
  - File: `database/migrations/2026_11_09_000007_create_loan_requests_table.php`
- [x] ✅ Task 2.9 - Migration tabel `loans` (Selesai)
  - File: `database/migrations/2026_11_09_000008_create_loans_table.php`
  - Catatan: menyimpan `duration_days` dan `quota_snapshot` sebagai snapshot kebijakan saat checkout.
- [x] ✅ Task 2.10 - Migration tabel `library_settings` (Selesai)
  - File: `database/migrations/2026_11_09_000009_create_library_settings_table.php`
- [x] ✅ Task 2.11 - Migration tabel `audit_logs` (Selesai)
  - File: `database/migrations/2026_11_09_000010_create_audit_logs_table.php`
- [x] ✅ Task 2.12 - Migration tabel `announcements` (Selesai)
  - File: `database/migrations/2026_11_09_000011_create_announcements_table.php`
- [x] ✅ Task 2.13 - Seeder data master, eksemplar, dan transaksi contoh (Selesai)
  - File: `database/seeders/LibrarySettingSeeder.php`, `database/seeders/MasterDataSeeder.php`, `database/seeders/CirculationSeeder.php`, `database/seeders/DatabaseSeeder.php`
  - Hasil seed dev: 13 judul, 27 eksemplar, 22 tersedia, 5 permintaan, 3 pinjaman, 1 terlambat.

---

## 3. Models & Relationships

- [x] ✅ Task 3.1 - Model `User` dengan enum role/status, helper, dan scope `[Sedang]` (Selesai)
  - File: `app/Models/User.php`, `app/Enums/UserRole.php`, `app/Enums/UserStatus.php`
- [x] ✅ Task 3.2 - Factory `User` dengan state admin/guru/siswa/nonaktif `[Mudah]` (Selesai)
  - File: `database/factories/UserFactory.php`
- [x] ✅ Task 3.3 - Model `Category`, `Author`, `Publisher` (Selesai)
  - File: `app/Models/Category.php`, `app/Models/Author.php`, `app/Models/Publisher.php`, `app/Models/Concerns/HasSlug.php`, `database/factories/CategoryFactory.php`, `database/factories/AuthorFactory.php`, `database/factories/PublisherFactory.php`
- [x] ✅ Task 3.4 - Model `Book` dan relasi many-to-many penulis (Selesai)
  - File: `app/Models/Book.php`, `database/factories/BookFactory.php`
  - Catatan: scope pencarian, filter kategori/ketersediaan, hitung eksemplar, dan URL sampul.
- [x] ✅ Task 3.5 - Model `BookCopy` dengan status/kondisi (Selesai)
  - File: `app/Models/BookCopy.php`, `app/Enums/BookCopyStatus.php`, `app/Enums/BookCondition.php`, `database/factories/BookCopyFactory.php`
- [x] ✅ Task 3.6 - Model `LoanRequest` (Selesai)
  - File: `app/Models/LoanRequest.php`, `app/Enums/LoanRequestStatus.php`, `database/factories/LoanRequestFactory.php`
- [x] ✅ Task 3.7 - Model `Loan` dengan status turunan terlambat (Selesai)
  - File: `app/Models/Loan.php`, `app/Enums/LoanStatus.php`, `database/factories/LoanFactory.php`
  - Catatan: `Terlambat` dihitung dari pinjaman aktif yang melewati `due_at`, bukan status tersimpan.
- [x] ✅ Task 3.8 - Model `LibrarySetting`, `AuditLog`, `Announcement` (Selesai)
  - File: `app/Models/LibrarySetting.php`, `app/Models/AuditLog.php`, `app/Models/Announcement.php`, `app/Enums/AnnouncementAudience.php`, factory terkait
- [x] ✅ Task 3.9 - Policy otorisasi buku, pinjaman, pengguna, dan pengajuan (Selesai)
  - File: `app/Policies/BookPolicy.php`, `app/Policies/BookCopyPolicy.php`, `app/Policies/CategoryPolicy.php`, `app/Policies/AuthorPolicy.php`, `app/Policies/PublisherPolicy.php`, `app/Policies/LoanPolicy.php`, `app/Policies/LoanRequestPolicy.php`, `app/Policies/UserPolicy.php`, `app/Providers/AppServiceProvider.php`
  - Catatan: seluruh policy didaftarkan eksplisit lewat `Gate::policy()`; controller admin memakai `$this->authorize()` sebagai lapisan kedua setelah middleware `role:admin`.

---

## 4. Backend Logic & API

- [x] ✅ Task 4.1 - Login/logout custom dengan email atau nomor induk `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Auth/LoginController.php`, `app/Http/Requests/Auth/LoginRequest.php`
- [x] ✅ Task 4.2 - Rate limit login 5 percobaan per menit `[Sedang]` (Selesai)
  - File: `app/Http/Requests/Auth/LoginRequest.php`
- [x] ✅ Task 4.3 - Middleware role dan status akun aktif `[Sedang]` (Selesai)
  - File: `app/Http/Middleware/EnsureUserHasRole.php`, `app/Http/Middleware/EnsureUserIsActive.php`
- [x] ✅ Task 4.4 - Redirect dashboard sesuai peran dan dashboard placeholder `[Mudah]` (Selesai)
  - File: `app/Http/Controllers/DashboardController.php`, `app/Http/Controllers/Admin/DashboardController.php`, `app/Http/Controllers/Borrower/DashboardController.php`, `routes/web.php`
- [x] ✅ Task 4.5 - Backend katalog: pencarian, filter, pagination, detail (Selesai)
  - File: `app/Http/Controllers/CatalogController.php`, `app/Models/Book.php`, `routes/web.php`
- [x] ✅ Task 4.6 - Backend pengajuan pinjam dan pembatalan oleh peminjam (Selesai)
  - File: `app/Http/Controllers/Borrower/LoanRequestController.php`, `app/Http/Requests/Borrower/StoreLoanRequest.php`, `app/Services/CirculationService.php`, `resources/views/borrower/requests/create.blade.php`, `routes/web.php`
  - Catatan: pengajuan menargetkan buku; eksemplar baru ditetapkan saat checkout. Kuota menghitung pinjaman aktif + pengajuan terbuka.
- [x] ✅ Task 4.7 - Backend pinjaman aktif, riwayat, dan profil peminjam (Selesai)
  - File: `app/Http/Controllers/Borrower/LoanController.php`, `app/Http/Controllers/Borrower/ProfileController.php`, `app/Http/Requests/Borrower/UpdateProfileRequest.php`, `resources/views/borrower/loans/index.blade.php`, `resources/views/borrower/loans/history.blade.php`, `resources/views/borrower/profile.blade.php`, `routes/web.php`
  - Catatan: seluruh query dibatasi `user_id`; perubahan profil mencakup nama, email, telepon, dan kata sandi.
- [x] ✅ Task 4.8 - CRUD admin buku dan eksemplar (Selesai)
  - File: `app/Http/Controllers/Admin/BookController.php`, `app/Http/Controllers/Admin/BookCopyController.php`, `app/Http/Requests/Admin/BookRequest.php`, `app/Http/Requests/Admin/BookCopyRequest.php`, `routes/web.php`
  - Catatan: hapus buku = arsip; eksemplar dengan riwayat transaksi dinonaktifkan, bukan dihapus.
- [x] ✅ Task 4.9 - CRUD admin kategori, penulis, dan penerbit (Selesai)
  - File: `app/Http/Controllers/Admin/CategoryController.php`, `app/Http/Controllers/Admin/AuthorController.php`, `app/Http/Controllers/Admin/PublisherController.php`, request terkait
  - Catatan: kategori yang masih dipakai buku dinonaktifkan; penulis/penerbit yang masih dipakai tidak dapat dihapus.
- [x] ✅ Task 4.10 - Antrean permintaan, approve/reject dengan alasan (Selesai)
  - File: `app/Http/Controllers/Admin/LoanRequestController.php`, `app/Http/Requests/Admin/RejectLoanRequest.php`, `app/Services/CirculationService.php`, `resources/views/admin/circulation/requests/index.blade.php`, `resources/views/admin/circulation/requests/show.blade.php`, `routes/web.php`
  - Catatan: persetujuan menahan stok secara logis, menetapkan batas pengambilan, dan tetap bukan peminjaman sampai checkout.
- [x] ✅ Task 4.11 - Checkout dan pengembalian dengan transaksi database (Selesai)
  - File: `app/Http/Controllers/Admin/LoanController.php`, `app/Http/Requests/Admin/CheckoutLoanRequest.php`, `app/Http/Requests/Admin/ReturnLoanRequest.php`, `app/Enums/ReturnCondition.php`, `app/Services/CirculationService.php`, `resources/views/admin/circulation/requests/checkout.blade.php`, `resources/views/admin/circulation/loans/index.blade.php`, `resources/views/admin/circulation/loans/return.blade.php`, `resources/views/admin/circulation/loans/history.blade.php`, `routes/web.php`
  - Catatan: checkout dan pengembalian memakai `DB::transaction`; durasi dan kuota disnapshot pada pinjaman; kondisi kembali memperbarui status/kondisi eksemplar.
- [x] ✅ Task 4.12 - Laporan, CSV, dan halaman cetak `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/ReportController.php`, `app/Services/ReportService.php`, `app/Services/CsvExporter.php`, `app/Support/ReportFilters.php`, `resources/views/admin/reports/*.blade.php`, `resources/views/layouts/print.blade.php`, `routes/web.php`
  - Catatan: enam laporan (ringkasan, peminjaman, pengembalian, keterlambatan, inventaris, buku populer) berbagi filter periode/kategori/peran/status; CSV dan cetak mewarisi filter aktif. Default periode adalah bulan berjalan.
- [x] ✅ Task 4.13 - CRUD pengguna, reset password, aktif/nonaktif `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/UserController.php`, `app/Http/Requests/Admin/StoreUserRequest.php`, `app/Http/Requests/Admin/UpdateUserRequest.php`, `app/Http/Requests/Admin/ResetUserPasswordRequest.php`, `resources/views/admin/users/*.blade.php`, `routes/web.php`
  - Catatan: admin tidak dapat mengubah/menghapus akunnya sendiri; reset password hanya menerima sandi baru tanpa menampilkannya; akun dengan riwayat transaksi atau jejak audit dinonaktifkan, bukan dihapus.
- [x] ✅ Task 4.14 - Pengaturan kuota, durasi, dan batas pengambilan `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/LibrarySettingController.php`, `app/Http/Requests/Admin/UpdateLibrarySettingsRequest.php`, `config/library.php`, `resources/views/admin/settings/index.blade.php`, `routes/web.php`
  - Catatan: lima kebijakan sirkulasi dapat diubah admin dengan validasi rentang; setiap perubahan dicatat per kunci dan berlaku untuk transaksi baru.
- [x] ✅ Task 4.15 - CRUD pesan sistem/announcement `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/AnnouncementController.php`, `app/Http/Requests/Admin/AnnouncementRequest.php`, `resources/views/admin/announcements/*.blade.php`, `resources/views/borrower/dashboard.blade.php`, `routes/web.php`
  - Catatan: isi pengumuman berupa teks biasa yang di-escape saat ditampilkan; hanya pengumuman aktif, sudah terbit, dan sesuai audiens yang tampil di dashboard (maksimal tiga).
- [x] ✅ Task 4.16 - Endpoint polling notifikasi admin dan peminjam (Selesai)
  - File: `app/Http/Controllers/NotificationController.php`, `routes/web.php`, `tests/Feature/NotificationTest.php`
  - Catatan: endpoint JSON ringan untuk admin dan peminjam, dilindungi `auth`, `active`, `role`, dan `throttle:60,1`. Data diturunkan dari `loan_requests` dan `loans` tanpa tabel notifikasi baru; peminjam hanya menerima data miliknya.

---

## 5. Security (Input Sanitization, Rate Limiting, XSS Protection, CSRF)

- [x] ✅ Task 5.1 - Form Request login dan validasi server-side `[Sedang]` (Selesai)
  - File: `app/Http/Requests/Auth/LoginRequest.php`
- [x] ✅ Task 5.2 - CSRF pada seluruh form `[Mudah]` (Selesai)
  - File: `resources/views/auth/login.blade.php`, layout admin/borrower
- [x] ✅ Task 5.3 - Password hashing, session regenerate, dan invalidate saat logout `[Sedang]` (Selesai)
  - File: `app/Http/Requests/Auth/LoginRequest.php`, `app/Http/Controllers/Auth/LoginController.php`, `app/Models/User.php`
- [x] ✅ Task 5.4 - Penolakan login dan pemaksaan logout untuk akun nonaktif `[Sedang]` (Selesai)
  - File: `app/Http/Middleware/EnsureUserIsActive.php`, `app/Http/Requests/Auth/LoginRequest.php`
- [x] ✅ Task 5.5 - Security headers dasar `[Mudah]` (Selesai)
  - File: `app/Http/Middleware/SecurityHeaders.php`, `bootstrap/app.php`
- [x] ✅ Task 5.6 - Rate limiting login `[Sedang]` (Selesai)
  - File: `app/Http/Requests/Auth/LoginRequest.php`
- [x] ✅ Task 5.7 - Otorisasi role pada route admin/peminjam `[Sedang]` (Selesai)
  - File: `app/Http/Middleware/EnsureUserHasRole.php`, `routes/web.php`
- [x] ✅ Task 5.8 - Policy ownership untuk transaksi milik sendiri (Selesai)
  - File: `app/Policies/LoanPolicy.php`, `app/Policies/LoanRequestPolicy.php`, `app/Policies/UserPolicy.php`, `tests/Feature/Security/PolicyTest.php`
  - Catatan: guru/siswa hanya dapat melihat pinjaman dan pengajuan miliknya; admin dapat melihat semua. Pembatalan pengajuan dibatasi pada status yang diizinkan, dan checkout hanya untuk pengajuan disetujui yang belum kedaluwarsa.
- [x] ✅ Task 5.9 - Validasi upload sampul dan penyimpanan file aman (Selesai)
  - File: `app/Http/Requests/Admin/BookRequest.php`, `app/Http/Controllers/Admin/BookController.php`
  - Catatan: hanya JPG/PNG/WEBP maksimal 2 MB; file disimpan di disk `public` dengan nama acak; sampul lama dihapus saat diganti.
- [x] ✅ Task 5.10 - Perlindungan formula injection pada ekspor CSV `[Sedang]` (Selesai)
  - File: `app/Services/CsvExporter.php`, `tests/Feature/Admin/ReportTest.php`
  - Catatan: CSV memakai BOM UTF-8 dan delimiter `;`; nilai yang diawali `=`, `+`, `-`, `@`, atau tab diberi prefiks kutip tunggal, dan baris baru dinormalisasi.
- [x] ✅ Task 5.11 - Audit log tindakan admin (Selesai)
  - File: `app/Services/AuditLogger.php`, `app/Http/Controllers/Admin/BookController.php`, `app/Http/Controllers/Admin/BookCopyController.php`, `app/Http/Controllers/Admin/CategoryController.php`, `app/Http/Controllers/Admin/AuthorController.php`, `app/Http/Controllers/Admin/PublisherController.php`, `tests/Feature/Admin/AuditLogTest.php`
  - Catatan: mencatat aktor, aksi, tipe/ID entitas, nilai sebelum-sesudah, IP, dan user agent. Perubahan hanya mencatat atribut yang benar-benar berubah; arsip/penonaktifan juga tercatat.
- [x] ✅ Task 5.12 - Review XSS pada seluruh tampilan data pengguna `[Sedang]` (Selesai)
  - File: `resources/views/admin/announcements/*.blade.php`, `resources/views/borrower/dashboard.blade.php`, `tests/Feature/Admin/AnnouncementManagementTest.php`
  - Catatan: seluruh output memakai escaping Blade; pengumuman teks biasa di-escape dan baris baru ditampilkan lewat CSS `white-space: pre-line`, bukan HTML mentah.

---

## 6. Frontend & Blade Views (Ikuti design.md)

- [x] ✅ Task 6.1 - Layout autentikasi `[Sedang]` (Selesai)
  - File: `resources/views/layouts/auth.blade.php`
- [x] ✅ Task 6.2 - Halaman login dengan show/hide password dan bantuan admin `[Sedang]` (Selesai)
  - File: `resources/views/auth/login.blade.php`
- [x] ✅ Task 6.3 - Layout admin dengan sidebar, offcanvas mobile, topbar, dan breadcrumb `[Sedang]` (Selesai)
  - File: `resources/views/layouts/admin.blade.php`
- [x] ✅ Task 6.4 - Layout peminjam dengan navbar responsif `[Sedang]` (Selesai)
  - File: `resources/views/layouts/borrower.blade.php`
- [x] ✅ Task 6.5 - Layout publik `[Mudah]` (Selesai)
  - File: `resources/views/layouts/public.blade.php`
- [x] ✅ Task 6.6 - Landing page publik `[Sedang]` (Selesai)
  - File: `resources/views/welcome.blade.php`
  - Catatan: konten memakai `container-xl` agar margin kiri-kanan selaras dengan navbar dan footer.
- [x] ✅ Task 6.7 - Dashboard admin placeholder `[Mudah]` (Selesai)
  - File: `resources/views/admin/dashboard.blade.php`
- [x] ✅ Task 6.8 - Dashboard peminjam placeholder `[Mudah]` (Selesai)
  - File: `resources/views/borrower/dashboard.blade.php`
- [x] ✅ Task 6.9 - Komponen `page-header`, `status-badge`, `empty-state`, `kpi-card` `[Sedang]` (Selesai)
  - File: `resources/views/components/*.blade.php`
- [x] ✅ Task 6.10 - Partial toast/flash message `[Mudah]` (Selesai)
  - File: `resources/views/partials/flash.blade.php`
- [x] ✅ Task 6.11 - Halaman error 403, 404, 419, 429, 500 `[Mudah]` (Selesai)
  - File: `resources/views/errors/*.blade.php`
- [x] ✅ Task 6.12 - Halaman katalog index (Selesai)
  - File: `resources/views/catalog/index.blade.php`, `resources/views/components/book-card.blade.php`, `resources/views/layouts/public.blade.php`
- [x] ✅ Task 6.13 - Halaman detail buku (Selesai)
  - File: `resources/views/catalog/show.blade.php`
- [x] ✅ Task 6.14 - Halaman pinjaman saya, riwayat, dan profil peminjam (Selesai)
  - File: `resources/views/borrower/loans/index.blade.php`, `resources/views/borrower/loans/history.blade.php`, `resources/views/borrower/profile.blade.php`, `resources/views/borrower/requests/create.blade.php`
- [x] ✅ Task 6.15 - Halaman admin buku dan eksemplar (Selesai)
  - File: `resources/views/admin/books/index.blade.php`, `resources/views/admin/books/form.blade.php`, `resources/views/admin/books/copies/index.blade.php`, `resources/views/admin/books/copies/form.blade.php`, `resources/views/admin/copies/index.blade.php`
- [x] ✅ Task 6.16 - Halaman admin kategori, penulis, dan penerbit (Selesai)
  - File: `resources/views/admin/categories/*.blade.php`, `resources/views/admin/authors/*.blade.php`, `resources/views/admin/publishers/*.blade.php`
- [x] ✅ Task 6.17 - Halaman admin permintaan, checkout, pengembalian, dan riwayat (Selesai)
  - File: `resources/views/admin/circulation/requests/index.blade.php`, `resources/views/admin/circulation/requests/show.blade.php`, `resources/views/admin/circulation/requests/checkout.blade.php`, `resources/views/admin/circulation/loans/index.blade.php`, `resources/views/admin/circulation/loans/return.blade.php`, `resources/views/admin/circulation/loans/history.blade.php`
- [x] ✅ Task 6.18 - Halaman laporan admin `[Sedang]` (Selesai)
  - File: `resources/views/admin/reports/index.blade.php`, `resources/views/admin/reports/loans.blade.php`, `resources/views/admin/reports/overdue.blade.php`, `resources/views/admin/reports/inventory.blade.php`, `resources/views/admin/reports/popular.blade.php`, `resources/views/admin/reports/_nav.blade.php`, `resources/views/admin/reports/_filters.blade.php`
  - Catatan: navigasi antar laporan, filter seragam, ringkasan status eksemplar, dan tombol ekspor/cetak.
- [x] ✅ Task 6.19 - Halaman pengguna, pengaturan, dan pesan sistem `[Sedang]` (Selesai)
  - File: `resources/views/admin/users/index.blade.php`, `resources/views/admin/users/form.blade.php`, `resources/views/admin/settings/index.blade.php`, `resources/views/admin/announcements/index.blade.php`, `resources/views/admin/announcements/form.blade.php`
  - Catatan: formulir responsif, konfirmasi tindakan destruktif, dan tampilan status/audiens memakai komponen Bootstrap yang sudah ada.
- [x] ✅ Task 6.20 - Layout dan style halaman cetak `[Sedang]` (Selesai)
  - File: `resources/views/layouts/print.blade.php`, `resources/views/admin/reports/print.blade.php`, `resources/css/app.css`, `resources/js/app.js`
  - Catatan: halaman cetak A4 landscape dengan toolbar pratinjau, header identitas sekolah, tanda tangan petugas, dan aturan `@media print` tanpa dependensi PDF.
- [x] ✅ Task 6.21 - Terapkan gaya flat design Bootstrap pada layout, komponen, dan halaman `[Sedang]` (Selesai)
  - File: `resources/css/app.css`, `resources/views/layouts/auth.blade.php`, `resources/views/welcome.blade.php`, `resources/views/components/kpi-card.blade.php`, `resources/views/admin/dashboard.blade.php`, `resources/views/borrower/dashboard.blade.php`, `resources/views/errors/minimal.blade.php`, `public/images/logo.svg`, `public/images/book-placeholder.svg`
  - Catatan: tanpa gradient/bayangan, warna solid, radius 0.25rem, aksen garis primary, dan header kartu abu-abu muda.

---

## 7. JavaScript & Realtime Features

- [x] ✅ Task 7.1 - Import Bootstrap JS bundle `[Mudah]` (Selesai)
  - File: `resources/js/app.js`
- [x] ✅ Task 7.2 - Toast otomatis untuk flash message `[Mudah]` (Selesai)
  - File: `resources/js/app.js`, `resources/views/partials/flash.blade.php`
- [x] ✅ Task 7.3 - Show/hide password `[Mudah]` (Selesai)
  - File: `resources/js/app.js`, `resources/views/auth/login.blade.php`
- [x] ✅ Task 7.4 - Loading state dan pencegahan klik ganda `[Mudah]` (Selesai)
  - File: `resources/js/app.js`
- [x] ✅ Task 7.5 - Modal konfirmasi reusable (Selesai)
  - File: `resources/views/partials/confirm-modal.blade.php`, `resources/js/app.js`, `resources/views/layouts/admin.blade.php`, `resources/views/layouts/borrower.blade.php`, `resources/views/layouts/public.blade.php`
  - Catatan: form `data-confirm` memakai modal Bootstrap; tombol loading tidak dinonaktifkan saat pengguna membatalkan konfirmasi.
- [x] ✅ Task 7.6 - Share detail buku (Web Share API + clipboard) (Selesai)
  - File: `resources/js/share.js`, `resources/js/app.js`, `resources/js/toast.js`, `resources/views/catalog/show.blade.php`, `tests/Feature/Catalog/CatalogTest.php`
  - Catatan: memakai Web Share API bila tersedia; fallback menyalin tautan ke clipboard lalu menampilkan toast “Tautan buku berhasil disalin.” Tombol tersedia untuk tamu maupun pengguna login.
- [x] ✅ Task 7.7 - Polling notifikasi admin (Selesai)
  - File: `resources/js/notifications.js`, `resources/js/toast.js`, `resources/views/layouts/admin.blade.php`, `resources/css/app.css`, `app/Http/Controllers/NotificationController.php`, `routes/web.php`
  - Catatan: polling setiap 30 detik, berhenti saat tab tidak aktif, badge dan dropdown ringkasan permintaan menunggu/pinjaman terlambat; endpoint dibatasi role admin.
- [x] ✅ Task 7.8 - Polling status pinjaman peminjam (Selesai)
  - File: `resources/js/notifications.js`, `resources/views/layouts/borrower.blade.php`, `resources/css/app.css`, `app/Http/Controllers/NotificationController.php`, `routes/web.php`
  - Catatan: hanya mengambil pengajuan dan pinjaman milik pengguna login; perubahan status pengajuan memunculkan toast otomatis dan badge memperbarui jumlah pengajuan menunggu/pinjaman terlambat.
- [x] ✅ Task 7.9 - Sound notification untuk permintaan baru (Selesai)
  - File: `resources/js/notifications.js`, `resources/views/layouts/admin.blade.php`
  - Catatan: chime Web Audio API tanpa file audio; berbunyi hanya saat `latest_request_id` naik setelah polling pertama. Tombol mute/unmute tersimpan di `localStorage` dan audio diaktifkan setelah interaksi pertama pengguna.
- [x] ✅ Task 7.10 - Filter katalog auto-submit (Selesai)
  - File: `resources/views/catalog/index.blade.php`, `resources/js/app.js`, `tests/Feature/Catalog/CatalogTest.php`
  - Catatan: select kategori dan ketersediaan memakai `data-auto-submit`; pencarian teks tetap dikirim lewat tombol/Enter agar tidak membanjiri server.
- [x] ✅ Task 7.11 - Grafik aktivitas dashboard (Selesai)
  - File: `app/Http/Controllers/Admin/DashboardController.php`, `resources/views/admin/dashboard.blade.php`, `resources/css/app.css`, `tests/Feature/Admin/DashboardTest.php`
  - Catatan: grafik batang HTML/CSS tujuh hari terakhir dari agregasi `borrowed_at`, tanpa Chart.js; tersedia tabel alternatif untuk pembaca layar dan empty state bila belum ada transaksi.
- [x] ✅ Task 7.12 - Dukungan `prefers-reduced-motion` (Selesai)
  - File: `resources/css/app.css`
  - Catatan: aturan global mematikan animasi/transisi dan smooth scroll saat pengguna meminta pengurangan gerak, melengkapi perilaku carousel yang sudah ada.

---

## 8. Additional Features (Sound Notification, Share, Toast, System Message, dll)

- [x] ✅ Task 8.1 - Toast notifikasi sukses/gagal/info `[Mudah]` (Selesai)
  - File: `resources/views/partials/flash.blade.php`, `resources/js/app.js`
- [x] ✅ Task 8.2 - Sound notification permintaan peminjaman baru (Selesai)
  - File: `resources/js/notifications.js`, `resources/views/layouts/admin.blade.php`
  - Catatan: nada singkat dua oktaf memakai Web Audio API; status mute disimpan di `localStorage` dan tombol suara tersedia di dropdown notifikasi admin.
- [x] ✅ Task 8.3 - Share detail buku (Selesai)
  - File: `resources/js/share.js`, `resources/js/app.js`, `resources/views/catalog/show.blade.php`
  - Catatan: tombol “Bagikan” memakai Web Share API atau clipboard, dengan toast sukses dan fallback prompt bila clipboard tidak tersedia.
- [x] ✅ Task 8.4 - Pesan sistem/announcement di dashboard peminjam `[Mudah]` (Selesai)
  - File: `resources/views/borrower/dashboard.blade.php`, `app/Http/Controllers/Borrower/DashboardController.php`
  - Catatan: maksimal tiga pengumuman aktif sesuai peran, dengan judul, isi, waktu terbit, dan nama pembuat.
- [x] ✅ Task 8.5 - Peringatan jatuh tempo pinjaman (Selesai)
  - File: `resources/views/borrower/dashboard.blade.php`, `resources/views/borrower/loans/index.blade.php`, `resources/views/admin/dashboard.blade.php`, `resources/views/admin/circulation/loans/index.blade.php`, `resources/views/admin/circulation/loans/return.blade.php`
  - Catatan: status `Terlambat`, sisa hari, dan tanggal jatuh tempo ditampilkan pada dashboard serta halaman pinjaman/pengembalian.
- [x] ✅ Task 8.6 - Placeholder sampul buku `[Mudah]` (Selesai)
  - File: `public/images/book-placeholder.svg`
- [x] ✅ Task 8.7 - Ekspor CSV laporan `[Mudah]` (Selesai)
  - File: `app/Services/CsvExporter.php`, `app/Http/Controllers/Admin/ReportController.php`, `routes/web.php`
  - Catatan: unduhan streaming untuk enam laporan, aman untuk Excel Indonesia, dan terlindungi dari formula injection.
- [x] ✅ Task 8.8 - Halaman cetak laporan `[Mudah]` (Selesai)
  - File: `resources/views/layouts/print.blade.php`, `resources/views/admin/reports/print.blade.php`, `resources/css/app.css`, `resources/js/app.js`
  - Catatan: cetak melalui dialog browser; tidak menambah dependensi PDF baru.
- [x] ✅ Task 8.9 - Halaman error berbahasa Indonesia `[Mudah]` (Selesai)
  - File: `resources/views/errors/*.blade.php`
- [x] ✅ Task 8.10 - Bantuan kontak admin di halaman login `[Mudah]` (Selesai)
  - File: `resources/views/auth/login.blade.php`

---

## 9. Cleanup Command & Scheduler

- [x] ✅ Task 9.1 - Artisan command `library:cleanup` `[Mudah]` (Selesai)
  - File: `app/Console/Commands/CleanupExpiredLoanRequests.php`
- [x] ✅ Task 9.2 - Batalkan permintaan disetujui yang melewati batas pengambilan `[Sedang]` (Selesai)
  - File: `app/Services/CirculationService.php`
  - Catatan: status berubah menjadi `Dibatalkan`, `cancelled_at` diisi, dan audit `loan_request.expired` dicatat dalam transaksi database.
- [x] ✅ Task 9.3 - Daftarkan schedule harian `[Mudah]` (Selesai)
  - File: `routes/console.php`
  - Catatan: dijadwalkan setiap hari pukul 01.00.
- [x] ✅ Task 9.4 - Opsi `--dry-run` `[Mudah]` (Selesai)
  - File: `app/Console/Commands/CleanupExpiredLoanRequests.php`
  - Catatan: hanya menghitung pengajuan kedaluwarsa tanpa mengubah data.
- [x] ✅ Task 9.5 - Test command cleanup `[Sedang]` (Selesai)
  - File: `tests/Feature/Console/CleanupExpiredLoanRequestsTest.php`

---

## 10. Final Polish & Bug Fixes

- [x] ✅ Task 10.1 - Feature test autentikasi dan otorisasi dasar `[Sedang]` (Selesai)
  - File: `tests/Feature/Auth/LoginTest.php`
- [x] ✅ Task 10.2 - Test security headers dan factory state `[Mudah]` (Selesai)
  - File: `tests/Feature/SecurityHeadersTest.php`
- [x] ✅ Task 10.3 - Test lengkap alur MVP (katalog, pengajuan, checkout, return, laporan) `[Sedang]` (Selesai)
  - File: `tests/Feature/MvpFlowTest.php`
  - Catatan: satu alur penuh dengan 102 assertion; mencakup katalog publik, login siswa/admin, pengajuan, persetujuan, checkout, pengembalian, laporan/CSV, kartu anggota, kunjungan, grafik dashboard, dan audit log.
- [x] ✅ Task 10.4 - QA responsif 360/390/768/1024/1440 px `[Sedang]` (Selesai)
  - File: `resources/views/welcome.blade.php`, `resources/views/catalog/show.blade.php`, `resources/css/app.css`
  - Catatan: audit 20 halaman × 5 lebar (100 kombinasi) memakai Chrome DevTools Protocol; tidak ada horizontal overflow. Overflow 12 px pada beranda/detail katalog diperbaiki dengan `g-4 g-xl-5`.
- [x] ✅ Task 10.5 - QA aksesibilitas (label, fokus, kontras, status non-warna) `[Sedang]` (Selesai)
  - File: `resources/css/app.css`, `resources/js/app.js`, `resources/views/layouts/admin.blade.php`, `resources/views/layouts/print-card.blade.php`
  - Catatan: axe-core WCAG 2.1 A/AA pada halaman utama desktop/mobile menghasilkan 0 pelanggaran; perbaikan kontras link/tombol outline, `aria-label` menu akun, dan region tabel/pratinjau yang dapat difokus keyboard.
- [x] ✅ Task 10.6 - Format kode dengan Pint `[Mudah]` (Selesai)
  - Perintah: `php vendor/bin/pint --dirty`
- [x] ✅ Task 10.7 - Build Vite produksi `[Mudah]` (Selesai)
  - Perintah: `npm run build`
- [x] ✅ Task 10.8 - Seeder akun demo admin/guru/siswa/nonaktif `[Mudah]` (Selesai)
  - File: `database/seeders/DatabaseSeeder.php`
  - Akun: `admin@perpus.test`, `guru@perpus.test`, `siswa@perpus.test`, `nonaktif@perpus.test` — password `password`.
- [x] ✅ Task 10.9 - Update tasklist dan progress `[Mudah]` (Selesai)
  - File: `agents/tasklist.md`
- [x] ✅ Task 10.10 - Dokumentasi setup dan akun demo `[Mudah]` (Selesai)
  - File: `README.md`
  - Catatan: kebutuhan sistem, instalasi MySQL/SQLite, akun demo, perintah dev/test, cetak kartu, laporan, scheduler, dan troubleshooting.
- [x] ✅ Task 10.11 - Perbaikan bug hasil review `[Sedang]` (Selesai)
  - File: `resources/css/app.css`, `resources/views/admin/member-cards/print.blade.php`, `app/Support/MemberBarcode.php`, `app/Http/Controllers/Admin/UserController.php`, `resources/views/layouts/admin.blade.php`, `resources/js/app.js`
  - Catatan: footer/barcode kartu tidak lagi hilang saat cetak, kartu dipenggal per 8 per halaman, nomor induk barcode dibatasi 20 karakter, overflow 12 px diperbaiki, kontras dan nama aksesibel tombol akun diperbaiki, dan pengguna dengan riwayat kunjungan dinonaktifkan (bukan dihapus).
- [x] ✅ Task 10.12 - Checklist UAT akhir `[Mudah]` (Selesai)
  - File: `agents/uat-checklist.md`
  - Catatan: 74 skenario per peran/modul, ringkasan verifikasi otomatis, kriteria lulus, lembar tanda tangan, dan format pencatatan defect.

---

## 11. Fase 5 — Identitas Sekolah & Carousel Beranda

- [x] ✅ Task 11.1 - Layanan identitas sekolah berbasis database dengan fallback konfigurasi `[Sedang]` (Selesai)
  - File: `app/Services/LibraryIdentity.php`, `config/library.php`, `app/Providers/AppServiceProvider.php`
  - Catatan: kunci `school_name`, `library_name`, `tagline`, dan `logo_path` disimpan di `library_settings`; nilai `config/library.php` tetap menjadi fallback. Layanan didaftarkan sebagai singleton dan cache-nya dibersihkan setelah perubahan.
- [x] ✅ Task 11.2 - Halaman pengaturan identitas sekolah dan validasi server-side `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/LibraryIdentityController.php`, `app/Http/Requests/Admin/UpdateLibraryIdentityRequest.php`, `resources/views/admin/settings/identity.blade.php`, `resources/views/admin/settings/_updated-at.blade.php`, `routes/web.php`
  - Catatan: hanya admin; perubahan dicatat per kunci ke audit log dan nilai yang tidak berubah tidak diaudit.
- [x] ✅ Task 11.3 - Unggah dan hapus logo sekolah dengan pembersihan file `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/LibraryIdentityController.php`, `app/Http/Requests/Admin/UpdateLibraryIdentityRequest.php`, `resources/views/components/library-logo.blade.php`, `resources/css/app.css`
  - Catatan: JPG/PNG/WEBP maksimal 1 MB, SVG ditolak; logo lama dihapus saat diganti atau direset, dan file baru dibersihkan bila transaksi gagal.
- [x] ✅ Task 11.4 - Terapkan identitas dan logo pada seluruh layout aplikasi `[Sedang]` (Selesai)
  - File: `resources/views/layouts/admin.blade.php`, `resources/views/layouts/auth.blade.php`, `resources/views/layouts/public.blade.php`, `resources/views/layouts/borrower.blade.php`, `resources/views/layouts/print.blade.php`, `resources/views/errors/minimal.blade.php`, `resources/views/welcome.blade.php`, `resources/views/admin/reports/print.blade.php`
  - Catatan: sidebar admin, halaman login, navbar publik/peminjam, footer, favicon, halaman cetak, dan halaman error memakai identitas yang sama.
- [x] ✅ Task 11.5 - Skema, model, factory, dan policy carousel slide `[Sedang]` (Selesai)
  - File: `database/migrations/2026_11_15_000001_create_carousel_slides_table.php`, `app/Models/CarouselSlide.php`, `database/factories/CarouselSlideFactory.php`, `app/Policies/CarouselSlidePolicy.php`, `app/Providers/AppServiceProvider.php`
  - Catatan: kolom judul, keterangan, teks alternatif, path gambar, tautan, urutan, dan status aktif; scope `active()` dan `ordered()`; akses policy hanya admin.
- [x] ✅ Task 11.6 - CRUD slide carousel di pengaturan admin `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/CarouselSlideController.php`, `app/Http/Requests/Admin/CarouselSlideRequest.php`, `resources/views/admin/settings/carousel/index.blade.php`, `resources/views/admin/settings/carousel/form.blade.php`, `routes/web.php`
  - Catatan: gambar JPG/PNG/WEBP maksimal 2 MB; tautan hanya path internal `/...` atau URL http/https; gambar lama dihapus saat diganti/dihapus.
- [x] ✅ Task 11.7 - Aktif/nonaktif, batas lima slide aktif, dan pengurutan slide `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/CarouselSlideController.php`, `resources/views/admin/settings/carousel/index.blade.php`, `config/library.php`
  - Catatan: maksimal 5 slide aktif; tombol naik/turun menukar posisi dan menomori ulang urutan dalam transaksi database.
- [x] ✅ Task 11.8 - Komponen carousel landing page dan empty state `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/HomeController.php`, `resources/views/partials/landing-carousel.blade.php`, `resources/views/welcome.blade.php`, `resources/css/app.css`, `routes/web.php`
  - Catatan: hanya slide aktif yang tampil, berurutan; tautan eksternal memakai `target="_blank"` + `rel="noopener noreferrer"`; carousel disembunyikan bila tidak ada slide aktif.
- [x] ✅ Task 11.9 - Auto-play, tombol jeda/putar, dan dukungan `prefers-reduced-motion` `[Sedang]` (Selesai)
  - File: `resources/js/app.js`, `resources/css/app.css`, `resources/views/partials/landing-carousel.blade.php`
  - Catatan: interval 6 detik, kontrol jeda/putar tersedia, dan animasi dimatikan otomatis saat pengguna meminta pengurangan gerak.
- [x] ✅ Task 11.10 - Test fitur identitas, carousel, dan landing page `[Sedang]` (Selesai)
  - File: `tests/Feature/Admin/LibraryIdentityTest.php`, `tests/Feature/Admin/CarouselSlideTest.php`, `tests/Feature/LandingCarouselTest.php`
  - Catatan: 29 test mencakup otorisasi, validasi, unggah/ganti/hapus file, audit, batas slide aktif, pengurutan, escaping, tautan eksternal/internal, empty state, dan redirect pengguna login.

---

## 12. Fase 7 — Kartu Anggota, Kunjungan & Finalisasi MVP

- [x] ✅ Task 12.1 - Skema, model, factory, dan relasi kunjungan perpustakaan `[Sedang]` (Selesai)
  - File: `database/migrations/2026_11_15_000002_create_library_visits_table.php`, `app/Models/LibraryVisit.php`, `database/factories/LibraryVisitFactory.php`, `app/Models/User.php`
  - Catatan: kunjungan menyimpan anggota, waktu/tanggal kunjungan, dan admin pencatat; beberapa kunjungan per hari diizinkan.
- [x] ✅ Task 12.2 - Generator barcode Code 128 untuk nomor induk `[Sedang]` (Selesai)
  - File: `app/Support/MemberBarcode.php`, `composer.json`, `composer.lock`, `tests/Unit/MemberBarcodeTest.php`
  - Catatan: memakai `picqer/php-barcode-generator` 2.4.2, menghasilkan SVG inline; hanya ASCII, maksimal 20 karakter agar modul barcode tetap terbaca.
- [x] ✅ Task 12.3 - Halaman daftar kartu anggota dengan filter dan cetak massal `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/MemberCardController.php`, `resources/views/admin/member-cards/index.blade.php`, `routes/web.php`, `tests/Feature/Admin/MemberCardTest.php`
  - Catatan: hanya guru/siswa aktif dengan nomor induk; filter peran/kelas/pencarian; maksimal 200 kartu per proses; anggota yang dilewati dilaporkan.
- [x] ✅ Task 12.4 - Komponen kartu ID-1 dan layout cetak A4 8 kartu per lembar `[Sedang]` (Selesai)
  - File: `resources/views/components/member-card.blade.php`, `resources/views/admin/member-cards/print.blade.php`, `resources/views/layouts/print-card.blade.php`, `resources/css/app.css`
  - Catatan: kartu 85,6 × 54 mm, grid 2 × 4 per A4 portrait, dipenggal per 8 kartu; aturan cetak hanya menyembunyikan footer halaman sehingga barcode/footer kartu tetap tercetak.
- [x] ✅ Task 12.5 - Pencatatan kunjungan via scan barcode dan ringkasan anggota `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/VisitController.php`, `resources/views/admin/visits/index.blade.php`, `routes/web.php`, `tests/Feature/Admin/VisitTest.php`
  - Catatan: scan/paste nomor induk, beberapa kunjungan per hari, scan berulang <30 menit diabaikan, ringkasan pinjaman aktif/terlambat, dan pembatalan hanya untuk kunjungan hari ini.
- [x] ✅ Task 12.6 - Audit log kunjungan dan guard penghapusan pengguna `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/VisitController.php`, `app/Http/Controllers/Admin/UserController.php`, `tests/Feature/Admin/UserManagementTest.php`
  - Catatan: audit `visit.recorded` dan `visit.deleted`; pengguna dengan riwayat kunjungan dinonaktifkan, bukan dihapus, agar jejak audit tetap utuh.
- [x] ✅ Task 12.7 - Grafik kunjungan 7 hari di dashboard admin `[Sedang]` (Selesai)
  - File: `app/Http/Controllers/Admin/DashboardController.php`, `resources/views/admin/dashboard.blade.php`, `resources/css/app.css`, `tests/Feature/Admin/VisitTest.php`
  - Catatan: agregasi tujuh hari, memakai ulang `.activity-chart` dengan warna teal, berdampingan dengan grafik peminjaman pada layar xl, dan tabel alternatif untuk pembaca layar.
- [x] ✅ Task 12.8 - Menu sidebar, tombol cetak kartu, dan navigasi `[Mudah]` (Selesai)
  - File: `resources/views/layouts/admin.blade.php`, `resources/views/admin/users/index.blade.php`, `resources/views/admin/users/form.blade.php`, `routes/web.php`
  - Catatan: menu **Kunjungan** dan **Kartu Anggota** di sidebar; tombol cetak kartu pada daftar dan formulir pengguna.
- [x] ✅ Task 12.9 - Test Fase 7 dan test E2E alur MVP `[Sedang]` (Selesai)
  - File: `tests/Unit/MemberBarcodeTest.php`, `tests/Feature/Admin/MemberCardTest.php`, `tests/Feature/Admin/VisitTest.php`, `tests/Feature/Admin/UserManagementTest.php`, `tests/Feature/MvpFlowTest.php`
  - Catatan: 23 test baru sejak Fase 6; E2E MVP memuat 102 assertion dari katalog sampai audit.
- [x] ✅ Task 12.10 - QA cetak/responsif/aksesibilitas, dokumentasi, dan UAT `[Sedang]` (Selesai)
  - File: `README.md`, `agents/uat-checklist.md`, `resources/css/app.css`, `resources/js/app.js`, `resources/views/welcome.blade.php`, `resources/views/catalog/show.blade.php`, `resources/views/admin/member-cards/print.blade.php`, `resources/views/layouts/print-card.blade.php`
  - Catatan: 100 kombinasi halaman/lebar tanpa overflow, 0 pelanggaran axe; PDF 10 kartu = 2 halaman A4 dengan 10 barcode Code 128 terdekode; laporan A4 landscape dengan tanda tangan; dokumentasi setup dan 74 skenario UAT.

---

## Catatan Fase 1

- Fase 1 selesai dan siap direview sebelum masuk Fase 2.
- Perintah verifikasi yang sudah dijalankan:
  - `php artisan migrate` — berhasil.
  - `php artisan db:seed` — berhasil.
  - `php artisan test` — 20 test lulus, 71 assertion.
  - `npm run build` — berhasil.
  - `php artisan view:cache` — seluruh Blade berhasil dikompilasi.
  - `php vendor/bin/pint --dirty` — berhasil.
- Database test: `perpustakaan_test` (MySQL), bukan database dev `perpustakaan`.
- Login dapat memakai email atau nomor induk.
- Tampilan diperbarui ke gaya flat design Bootstrap atas permintaan review: tanpa gradient dan shadow, warna solid, radius kecil, serta aksen garis primary pada topbar dan kartu autentikasi.
- Verifikasi ulang tampilan: `npm run build`, `php artisan view:cache`, dan screenshot login, beranda, dashboard admin, serta dashboard peminjam.
- Katalog publik dan modul domain lainnya belum diimplementasikan; akan dikerjakan pada fase berikutnya.

---

## Catatan Fase 2

- Fase 2 selesai dan siap direview sebelum masuk Fase 3 (sirkulasi).
- Perintah verifikasi yang sudah dijalankan:
  - `php artisan migrate` — 11 migration domain berhasil.
  - `php artisan db:seed` — pengaturan, data master, dan transaksi contoh berhasil.
  - `php artisan test` — 47 test lulus, 269 assertion.
  - `php artisan view:cache` — seluruh Blade berhasil dikompilasi.
  - `npm run build` — berhasil.
  - `php vendor/bin/pint --dirty` — berhasil.
- Smoke test HTTP 200: `/katalog`, `/katalog/laskar-pelangi`, serta 13 halaman admin (dashboard, buku, eksemplar, kategori, penulis, penerbit).
- Cakupan Fase 2:
  - Skema domain: kategori, penulis, penerbit, buku, pivot penulis, eksemplar, permintaan, pinjaman, pengaturan, audit log, pengumuman.
  - Katalog publik dengan pencarian judul/penulis/ISBN, filter kategori dan ketersediaan, pagination, detail buku, lokasi rak, dan buku terkait.
  - CRUD admin buku (termasuk unggah sampul), eksemplar, kategori, penulis, dan penerbit.
  - Dashboard admin dan peminjam memakai data agregat nyata.
- Aturan integritas yang sudah diterapkan:
  - Status eksemplar `Dipinjam` tidak dapat diisi manual; hanya untuk checkout Fase 3.
  - Eksemplar dengan pinjaman aktif tidak dapat diubah ke status lain.
  - Buku dihapus dengan cara diarsipkan; eksemplar dengan riwayat transaksi dinonaktifkan.
  - Ketersediaan katalog dihitung dari eksemplar berstatus `Tersedia`.
- Penyelesaian sisa item Fase 2:
  - Policy otorisasi untuk buku, eksemplar, kategori, penulis, penerbit, pinjaman, pengajuan, dan pengguna; kepemilikan transaksi diperiksa di level policy.
  - Audit log tindakan admin pada CRUD buku/eksemplar dan seluruh master data, termasuk arsip dan penonaktifan.
  - Penguatan test: matriks policy, kepemilikan transaksi, aturan pembatalan/checkout pengajuan, dan pencatatan audit log.
  - `php vendor/bin/pint --dirty` — berhasil.
- Ditunda ke fase berikutnya: pengajuan pinjaman, checkout/pengembalian, laporan, pengguna, pengaturan, pengumuman, polling, dan audit log otomatis untuk modul yang belum dibangun.

---

## Catatan Fase 3

- Fase 3 (sirkulasi) selesai dan siap direview sebelum masuk Fase 4.
- Perintah verifikasi yang sudah dijalankan:
  - `php artisan test` — 71 test lulus, 403 assertion.
  - `php artisan view:cache` — seluruh Blade berhasil dikompilasi.
  - `npm run build` — berhasil.
  - `php vendor/bin/pint --dirty` — berhasil.
- Cakupan Fase 3:
  - Peminjam dapat mengajukan peminjaman per judul buku, membatalkan pengajuan, melihat pinjaman aktif, riwayat, dan memperbarui profil.
  - Admin dapat memproses antrean pengajuan (setujui/tolak dengan alasan/batalkan), mencatat checkout eksemplar, mencatat pengembalian beserta kondisi buku, dan menelusuri riwayat transaksi.
  - Kuota menghitung pinjaman aktif + pengajuan terbuka (`Menunggu` dan `Disetujui` yang belum kedaluwarsa); pengajuan disetujui menahan stok secara logis.
  - Persetujuan memiliki batas pengambilan (`pickup_deadline_days`); pengajuan yang melewati batas ditampilkan `Kedaluwarsa` dan diblokir dari checkout.
  - Checkout dan pengembalian dibungkus transaksi database; checkout mengunci pengajuan, peminjam, dan eksemplar, sedangkan persetujuan mengunci baris buku untuk mencegah stok tertahan berlebih.
  - Kondisi kembali (`Baik`, `Rusak Ringan`, `Rusak Berat`, `Hilang`) memperbarui status/kondisi eksemplar melalui enum `ReturnCondition`.
  - Audit log mencatat pembuatan pengajuan, persetujuan, penolakan, pembatalan, checkout, dan pengembalian.
  - Modal konfirmasi Bootstrap reusable dipakai seluruh form destruktif/berisiko.
- Ditunda: checkout langsung oleh admin tanpa pengajuan (FR-LOAN-10), laporan/CSV, pengguna, pengaturan, pengumuman, polling/sound, dan command cleanup permintaan kedaluwarsa (Fase 9).

---

## Catatan Fase 4

- Fase 4 (laporan & manajemen) selesai dan siap direview sebelum masuk fitur realtime dan final polish.
- Perintah verifikasi yang sudah dijalankan:
  - `php artisan test` — 110 test lulus, 628 assertion.
  - `php artisan view:cache` — seluruh Blade berhasil dikompilasi.
  - `npm run build` — berhasil.
  - `php vendor/bin/pint --dirty` dan Pint pada seluruh file baru — berhasil.
  - `php artisan route:list --path=admin` — 71 route admin terdaftar.
- Cakupan Fase 4:
  - Enam laporan admin dengan filter periode (default bulan berjalan), kategori, peran, status/kondisi, pencarian, dan batas jumlah data.
  - Ekspor CSV streaming dengan BOM UTF-8, delimiter `;`, normalisasi baris baru, dan perlindungan formula injection.
  - Halaman cetak berbasis browser (A4 landscape) untuk seluruh laporan, lengkap dengan identitas sekolah dan tanda tangan petugas.
  - CRUD pengguna dengan pembuatan akun, edit, reset kata sandi, aktif/nonaktif, dan penghapusan aman.
  - Halaman pengaturan lima kebijakan sirkulasi dengan validasi rentang dan audit per perubahan.
  - CRUD pengumuman dengan audiens, status aktif, jadwal terbit, dan tampilan maksimal tiga pengumuman di dashboard peminjam.
  - Command `library:cleanup` dengan `--dry-run`, dijadwalkan harian pukul 01.00.
  - Test fitur untuk laporan/CSV/cetak, pengguna, pengaturan, pengumuman, cleanup, dan policy Fase 4.
- Aturan keamanan yang diterapkan:
  - Laporan hanya untuk admin melalui gate `view-reports` dan middleware `role:admin`.
  - Admin tidak dapat mengelola akunnya sendiri (edit, reset, aktif/nonaktif, hapus).
  - Akun dengan riwayat transaksi, kepengarangan pengumuman, atau jejak audit dinonaktifkan agar integritas data tetap terjaga.
  - Isi pengumuman selalu di-escape; tidak ada HTML mentah yang dirender.
  - Ekspor CSV melindungi sel dari eksekusi formula saat dibuka di aplikasi spreadsheet.
- Ditunda: polling notifikasi admin/peminjam, sound notification, share detail buku, auto-submit filter katalog, grafik dashboard, dukungan `prefers-reduced-motion`, checkout langsung tanpa pengajuan, perpanjangan pinjaman, denda, QA responsif/aksesibilitas menyeluruh, dokumentasi setup/UAT, dan test E2E satu alur penuh (Task 10.3).

---

## Catatan Fase 5

- Fase 5 (identitas sekolah & carousel beranda) selesai dan siap direview sebelum masuk fitur realtime dan final polish.
- Perintah verifikasi yang sudah dijalankan:
  - `php artisan migrate --force` — migration `carousel_slides` diterapkan ke database dev.
  - `php artisan test` — 139 test lulus, 773 assertion (29 test baru untuk Fase 5).
  - `php artisan view:cache` — seluruh Blade berhasil dikompilasi.
  - `npm run build` — berhasil.
  - `php vendor/bin/pint --dirty` — berhasil (satu import tidak terpakai dirapikan).
  - `php artisan route:list --path=admin/settings` — route identitas dan carousel terdaftar.
  - Smoke test HTTP: `/`, `/login`, `/admin/settings/identity`, dan `/admin/settings/carousel` merespons normal.
  - QA visual dengan Chrome headless pada 1440 px dan 390 px; slide contoh sementara dibuat untuk pratinjau lalu dihapus kembali.
- Cakupan Fase 5:
  - Identitas sekolah (nama sekolah, nama perpustakaan, tagline, logo) dapat diubah admin dan langsung dipakai di sidebar admin, login, navbar publik/peminjam, footer, favicon, halaman cetak, dan halaman error.
  - Logo kustom disimpan di disk `public`; logo bawaan dipakai kembali saat logo kustom dihapus.
  - Carousel landing page dikelola dari tab **Carousel Beranda**: tambah, edit, hapus, aktif/nonaktif, dan ubah urutan.
  - Maksimal lima slide aktif; slide nonaktif tetap tersimpan tetapi tidak tampil di beranda.
  - Carousel otomatis berganti setiap 6 detik, memiliki tombol jeda/putar, indikator, kontrol sebelumnya/berikutnya, serta menghormati `prefers-reduced-motion`.
  - Tautan slide mendukung path internal dan URL eksternal; tautan eksternal dibuka di tab baru dengan `rel="noopener noreferrer"`.
  - Bila belum ada slide aktif, area carousel tidak dirender sehingga landing page tetap rapi.
- Aturan keamanan dan integritas yang diterapkan:
  - Seluruh route pengaturan identitas dan carousel hanya untuk admin, dijaga middleware `role:admin`, policy, dan `authorize()` di controller.
  - Validasi tipe file, MIME, dan ukuran; SVG ditolak untuk logo maupun gambar carousel.
  - Judul, keterangan, teks alternatif, dan tautan di-escape saat dirender; skema tautan dibatasi `/`, `http`, dan `https`.
  - Perubahan identitas dan seluruh aksi carousel dicatat ke audit log; file gambar lama dibersihkan saat diganti atau dihapus.
  - Batas slide aktif diperiksa di server, bukan hanya di tampilan.
- Catatan teknis:
  - Kunci identitas memakai tabel `library_settings` yang sudah ada, sehingga tidak menambah tabel baru khusus identitas.
  - Carousel memakai tabel `carousel_slides` tersendiri agar urutan, status, dan file gambar dapat dikelola per slide.
  - Tab pengaturan menyatukan **Identitas Sekolah**, **Kebijakan Sirkulasi**, dan **Carousel Beranda** tanpa mengubah test pengaturan sirkulasi yang sudah ada.
- Ditunda: pemotongan/resize gambar otomatis, pengurutan drag-and-drop, batas dimensi gambar, dukungan `prefers-reduced-motion` menyeluruh di luar carousel (Task 7.12), polling notifikasi, sound notification, share detail buku, grafik dashboard, dokumentasi setup/UAT, dan test E2E satu alur penuh (Task 10.3).

---

## Catatan Fase 6

- Fase 6 (fitur realtime & interaksi) selesai dan siap direview sebelum finalisasi MVP.
- Perintah verifikasi yang sudah dijalankan:
  - `php artisan test` — 147 test lulus, 846 assertion (8 test baru: 5 notifikasi, 2 dashboard, 1 katalog/share).
  - `php artisan view:cache` — seluruh Blade berhasil dikompilasi.
  - `npm run build` — berhasil (63 modul).
  - `php vendor/bin/pint --dirty` — berhasil.
  - QA visual Chrome headless pada 1440 px dan 390 px untuk dashboard, dropdown notifikasi admin/peminjam, filter katalog, tombol share, dan toast; tanpa error console.
- Cakupan Fase 6:
  - Notifikasi admin dan peminjam memakai polling 30 detik; badge dan dropdown diperbarui tanpa reload.
  - Polling berhenti saat tab tidak aktif dan langsung menyegarkan saat tab kembali terlihat.
  - Admin menerima ringkasan permintaan menunggu, pinjaman terlambat, dan permintaan terbaru; peminjam menerima status pengajuan serta pinjaman miliknya.
  - Perubahan status pengajuan peminjam memunculkan toast otomatis.
  - Sound notification admin memakai Web Audio API, dengan tombol mute yang tersimpan di `localStorage`.
  - Tombol bagikan pada detail buku memakai Web Share API, fallback clipboard, dan toast sukses.
  - Filter kategori/ketersediaan katalog langsung mengirim form saat berubah; pencarian teks tetap manual.
  - Dashboard admin menampilkan grafik batang aktivitas peminjaman tujuh hari terakhir dengan tabel alternatif untuk pembaca layar dan empty state.
  - Aturan global `prefers-reduced-motion` mematikan animasi/transisi serta smooth scroll.
- Aturan keamanan dan integritas:
  - Endpoint notifikasi dilindungi `auth`, `active`, `role`, dan `throttle:60,1`.
  - Peminjam hanya menerima data miliknya sendiri; tidak ada data pengguna lain di respons JSON.
  - Tidak ada tabel notifikasi baru; seluruh angka diturunkan dari data transaksi nyata.
  - Sound hanya dipicu oleh `latest_request_id` yang meningkat setelah polling pertama, bukan setiap polling.
- Catatan teknis:
  - `resources/js/notifications.js` menangani polling, badge, dropdown, toast perubahan status, dan chime; `resources/js/toast.js` menyediakan toast dinamis; `resources/js/share.js` menangani share/clipboard.
  - Grafik memakai HTML/CSS tanpa Chart.js; data agregasi dihitung portabel di PHP agar tidak bergantung fungsi tanggal MySQL.
  - Data contoh pinjaman untuk QA grafik dibuat sementara lalu dihapus kembali setelah pengujian.
- Ditunda ke Fase 7: test E2E satu alur MVP, QA responsif 360/768/1024 px dan aksesibilitas menyeluruh, dokumentasi setup/akun demo, checklist UAT, perbaikan bug hasil review, serta opsional checkout langsung oleh admin (FR-LOAN-10).

---

## Catatan Fase 7

- Fase 7 (kartu anggota, kunjungan, dan finalisasi MVP) selesai dan siap direview. Seluruh checklist task mencapai **100% (142/142)**.
- Perintah verifikasi yang sudah dijalankan:
  - `php artisan test` — **170 test lulus, 1039 assertion** (23 test baru sejak Fase 6: barcode, kartu anggota, kunjungan, guard pengguna, dan E2E MVP).
  - `npm run build` — berhasil (63 modul).
  - `php artisan view:cache` — seluruh Blade berhasil dikompilasi.
  - `php vendor/bin/pint --dirty` — berhasil.
  - `php artisan route:list --path=admin/kunjungan` — 3 route terdaftar (index, store, destroy).
  - `php artisan route:list --path=admin/member-cards` — 2 route terdaftar (index, print).
- Cakupan Fase 7:
  - Kartu anggota ID-1 85,6 × 54 mm untuk guru/siswa aktif dengan barcode Code 128 dari nomor induk.
  - Cetak tunggal dan massal (maksimal 200 anggota), 8 kartu per lembar A4 portrait, halaman berikutnya otomatis.
  - Filter peran/kelas/pencarian pada daftar kartu; anggota tanpa nomor induk atau nomor >20 karakter dilewati dengan peringatan.
  - Kunjungan dicatat admin dengan scan/paste nomor induk; beberapa kunjungan per hari diizinkan; scan berulang <30 menit diabaikan.
  - Ringkasan anggota (pinjaman aktif/terlambat) tampil setelah scan; hanya kunjungan hari ini yang dapat dibatalkan.
  - Audit `visit.recorded` dan `visit.deleted`; pengguna dengan riwayat kunjungan dinonaktifkan, bukan dihapus.
  - Grafik kunjungan 7 hari di dashboard admin, berdampingan dengan grafik peminjaman pada layar xl.
  - Menu sidebar **Kunjungan** dan **Kartu Anggota**, serta tombol cetak kartu di daftar/form pengguna.
- Hasil QA:
  - 20 halaman × 5 lebar (360/390/768/1024/1440) = **100 kombinasi, tanpa horizontal overflow**.
  - axe-core WCAG 2.1 A/AA: **0 pelanggaran** pada halaman utama desktop dan mobile.
  - PDF 10 kartu = **2 halaman A4 portrait** (8 kartu/lembar); seluruh **10 barcode terdekode sebagai Code 128**.
  - PDF laporan peminjaman = **1 halaman A4 landscape**; footer tanda tangan tampil.
- Bug yang ditemukan dan diperbaiki saat QA:
  - Aturan cetak global menyembunyikan semua `<footer>` sehingga barcode/footer kartu hilang; selektor dipersempit ke footer halaman.
  - Kartu massal terpotong antar halaman; lembar dipenggal eksplisit per 8 kartu dengan `break-after: page`.
  - Nomor induk panjang membuat barcode terlalu rapat; panjang payload dibatasi 20 karakter dengan validasi dan test.
  - Overflow 12 px pada beranda/detail katalog di 768/1024 px; gutter dibuat `g-4 g-xl-5`.
  - Kontras link/tombol outline, nama aksesibel dropdown akun, dan region scroll yang dapat difokus keyboard diperbaiki.
  - Pengguna dengan riwayat kunjungan sebelumnya dapat terhapus; guard penghapusan diperluas dan diuji.
- Dokumentasi:
  - `README.md` — setup MySQL/SQLite, akun demo, perintah dev/test, cetak kartu, laporan, scheduler, dan troubleshooting.
  - `agents/uat-checklist.md` — 74 skenario UAT per peran/modul, ringkasan verifikasi otomatis, kriteria lulus, lembar tanda tangan, dan format defect.
- Ditunda/opsional: checkout langsung oleh admin tanpa pengajuan (FR-LOAN-10), perpanjangan pinjaman (FR-LOAN-09), denda (FR-SET-03), pengurutan carousel drag-and-drop, dan pemotongan/resize gambar otomatis.
