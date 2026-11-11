# Tasklist — Sistem Informasi Perpustakaan Sekolah

**Terakhir diperbarui:** 11 November 2026  
**Progress keseluruhan:** 73% (89 dari 122 task)  
**Fase saat ini:** Fase 3 — Sirkulasi (selesai; siap direview)

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
- [ ] Task 4.12 - Laporan, CSV, dan halaman cetak
- [ ] Task 4.13 - CRUD pengguna, reset password, aktif/nonaktif
- [ ] Task 4.14 - Pengaturan kuota, durasi, dan batas pengambilan
- [ ] Task 4.15 - CRUD pesan sistem/announcement
- [ ] Task 4.16 - Endpoint polling notifikasi admin dan peminjam

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
- [ ] Task 5.10 - Perlindungan formula injection pada ekspor CSV
- [x] ✅ Task 5.11 - Audit log tindakan admin (Selesai)
  - File: `app/Services/AuditLogger.php`, `app/Http/Controllers/Admin/BookController.php`, `app/Http/Controllers/Admin/BookCopyController.php`, `app/Http/Controllers/Admin/CategoryController.php`, `app/Http/Controllers/Admin/AuthorController.php`, `app/Http/Controllers/Admin/PublisherController.php`, `tests/Feature/Admin/AuditLogTest.php`
  - Catatan: mencatat aktor, aksi, tipe/ID entitas, nilai sebelum-sesudah, IP, dan user agent. Perubahan hanya mencatat atribut yang benar-benar berubah; arsip/penonaktifan juga tercatat.
- [ ] Task 5.12 - Review XSS pada seluruh tampilan data pengguna

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
- [ ] Task 6.18 - Halaman laporan admin
- [ ] Task 6.19 - Halaman pengguna, pengaturan, dan pesan sistem
- [ ] Task 6.20 - Layout dan style halaman cetak
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
- [ ] Task 7.6 - Share detail buku (Web Share API + clipboard)
- [ ] Task 7.7 - Polling notifikasi admin
- [ ] Task 7.8 - Polling status pinjaman peminjam
- [ ] Task 7.9 - Sound notification untuk permintaan baru
- [ ] Task 7.10 - Filter katalog auto-submit
- [ ] Task 7.11 - Grafik aktivitas dashboard
- [ ] Task 7.12 - Dukungan `prefers-reduced-motion`

---

## 8. Additional Features (Sound Notification, Share, Toast, System Message, dll)

- [x] ✅ Task 8.1 - Toast notifikasi sukses/gagal/info `[Mudah]` (Selesai)
  - File: `resources/views/partials/flash.blade.php`, `resources/js/app.js`
- [ ] Task 8.2 - Sound notification permintaan peminjaman baru
- [ ] Task 8.3 - Share detail buku
- [ ] Task 8.4 - Pesan sistem/announcement di dashboard peminjam
- [x] ✅ Task 8.5 - Peringatan jatuh tempo pinjaman (Selesai)
  - File: `resources/views/borrower/dashboard.blade.php`, `resources/views/borrower/loans/index.blade.php`, `resources/views/admin/dashboard.blade.php`, `resources/views/admin/circulation/loans/index.blade.php`, `resources/views/admin/circulation/loans/return.blade.php`
  - Catatan: status `Terlambat`, sisa hari, dan tanggal jatuh tempo ditampilkan pada dashboard serta halaman pinjaman/pengembalian.
- [x] ✅ Task 8.6 - Placeholder sampul buku `[Mudah]` (Selesai)
  - File: `public/images/book-placeholder.svg`
- [ ] Task 8.7 - Ekspor CSV laporan
- [ ] Task 8.8 - Halaman cetak laporan
- [x] ✅ Task 8.9 - Halaman error berbahasa Indonesia `[Mudah]` (Selesai)
  - File: `resources/views/errors/*.blade.php`
- [x] ✅ Task 8.10 - Bantuan kontak admin di halaman login `[Mudah]` (Selesai)
  - File: `resources/views/auth/login.blade.php`

---

## 9. Cleanup Command & Scheduler

- [ ] Task 9.1 - Artisan command `library:cleanup`
- [ ] Task 9.2 - Batalkan permintaan disetujui yang melewati batas pengambilan
- [ ] Task 9.3 - Daftarkan schedule harian
- [ ] Task 9.4 - Opsi `--dry-run`
- [ ] Task 9.5 - Test command cleanup

---

## 10. Final Polish & Bug Fixes

- [x] ✅ Task 10.1 - Feature test autentikasi dan otorisasi dasar `[Sedang]` (Selesai)
  - File: `tests/Feature/Auth/LoginTest.php`
- [x] ✅ Task 10.2 - Test security headers dan factory state `[Mudah]` (Selesai)
  - File: `tests/Feature/SecurityHeadersTest.php`
- [ ] Task 10.3 - Test lengkap alur MVP (katalog, pengajuan, checkout, return, laporan)
- [ ] Task 10.4 - QA responsif 360/390/768/1024/1440 px
- [ ] Task 10.5 - QA aksesibilitas (label, fokus, kontras, status non-warna)
- [x] ✅ Task 10.6 - Format kode dengan Pint `[Mudah]` (Selesai)
  - Perintah: `php vendor/bin/pint --dirty`
- [x] ✅ Task 10.7 - Build Vite produksi `[Mudah]` (Selesai)
  - Perintah: `npm run build`
- [x] ✅ Task 10.8 - Seeder akun demo admin/guru/siswa/nonaktif `[Mudah]` (Selesai)
  - File: `database/seeders/DatabaseSeeder.php`
  - Akun: `admin@perpus.test`, `guru@perpus.test`, `siswa@perpus.test`, `nonaktif@perpus.test` — password `password`.
- [x] ✅ Task 10.9 - Update tasklist dan progress `[Mudah]` (Selesai)
  - File: `agents/tasklist.md`
- [ ] Task 10.10 - Dokumentasi setup dan akun demo
- [ ] Task 10.11 - Perbaikan bug hasil review
- [ ] Task 10.12 - Checklist UAT akhir

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
