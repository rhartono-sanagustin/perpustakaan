# AGENTS.md

## Project state
Laravel 12.71 / PHP 8.2.26 (composer `^8.2`; keep code PHP 8.2-compatible) school library app (Sistem Informasi Perpustakaan Sekolah). It is currently still the stock skeleton: default `users` table/model (name/email/password only), `welcome` view, and `/` route. No auth UI, Breeze/Jetstream, role package, domain models, or library features exist yet. The PRD requires `role` (admin/guru/siswa), `nomor_induk`, and an active/inactive account status on users.

## Read first
- `.agents/prd.md` — roles, business rules, loan/copy statuses, MVP scope.
- `.agents/design.md` — required Bootstrap 5 UI, Indonesian copy, tokens, page/component patterns.
- `.agents/task-instruction.md` — task workflow and checklist categories.

These specs are the source of truth; don't rewrite or revert them to match the code. The root `README.md` is stock Laravel boilerplate.

**Required after each task:** update `.agents/tasklist.md` (create it if missing) with `[x]` ✅, changed files, and overall progress %.

## Commands
- Setup: `composer install`, `npm install`, copy `.env.example` to `.env` if missing, set `APP_KEY` (`php artisan key:generate` if empty), create the configured DB, `php artisan migrate`. The local `.env` currently uses MySQL DB `perpustakaan`; a fresh `.env` copied from `.env.example` uses SQLite (`database/database.sqlite`).
- Dev: run `php artisan serve`, `npm run dev`, and `php artisan queue:listen` (when using queues) in separate terminals. **Don't use `composer dev` on this Windows setup**: it starts `php artisan pail`, which fails because `pcntl` is unavailable. Use `Get-Content storage/logs/laravel.log -Wait` instead.
- Build: `npm run build`.
- Tests: `php artisan test`. Single test: `php artisan test --filter=ExampleTest` or `php artisan test tests/Feature/ExampleTest.php`.
- Format only your changes: `php vendor/bin/pint --dirty`. The stock skeleton is currently not Pint-clean, so repo-wide Pint creates unrelated diffs.
- PHPUnit 11, not Pest. No CI, JS tests, or static analysis configured.

## Database / testing gotcha
`phpunit.xml` leaves `DB_CONNECTION`/`DB_DATABASE` commented out, so tests use `.env`'s MySQL `perpustakaan` database. Before adding tests that use `RefreshDatabase`, add SQLite `:memory:` overrides (or a dedicated test DB) to `phpunit.xml`; otherwise the suite can wipe the dev database. Avoid `migrate:fresh` on the default connection for the same reason. Dev is MySQL and the recommended test DB is SQLite, so keep queries portable (avoid MySQL-only SQL).

## Frontend
- `design.md` mandates Bootstrap 5, but the skeleton currently wires Tailwind 4 + Vite. Bootstrap is the requirement: add Bootstrap 5 via npm and replace the Tailwind wiring in the Vite entrypoints. Don't build the library UI with Tailwind utilities or install Breeze/Jetstream (their scaffolding is Tailwind-based).
- `resources/js/bootstrap.js` is Laravel's axios setup, not Bootstrap 5. Import Bootstrap's CSS/JS bundle separately; modal/offcanvas/toast need its JS.
- Use Blade + Bootstrap (no Vue/React/SPA). User-facing labels, validation, and status text must be Indonesian; Laravel's default validation messages are English and there is no `lang/` directory, so run `php artisan lang:publish` and add Indonesian messages/translations. Follow the layout/component structure in `design.md` §11.
- `config/app.php` hardcodes UTC (no `APP_TIMEZONE` env hook) and defaults locale to `en`; set `APP_LOCALE=id` and handle school-local date conversion explicitly for due dates.
- Laravel 12 wiring: providers in `bootstrap/providers.php`; middleware/exceptions in `bootstrap/app.php`; scheduled tasks in `routes/console.php` (run locally with `php artisan schedule:work`). There is no `app/Http/Kernel.php` or `app/Console/Kernel.php`.

## Domain invariants easy to miss
- A request is not a loan: requests should target a book, the specific copy is assigned at checkout, and `Dipinjam` is set only then (with the due date). `Terlambat` is a derived display state for active overdue loans, not a loan or copy status.
- Availability comes from copies with status `Tersedia`, not from book/title counts. One copy can have only one active loan; wrap checkout/return in DB transactions.
- Teachers/students can only access their own loans; enforce role and ownership checks server-side, not just by hiding menus.
- Loan quotas/durations are configurable settings (initial: siswa 2 copies/7 days, guru 5/14 days) and should be snapshotted on the loan. Archive/deactivate records with transaction history; don't hard-delete them.
