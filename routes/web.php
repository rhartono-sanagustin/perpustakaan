<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookCopyController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LibrarySettingController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\Admin\LoanRequestController as AdminLoanRequestController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Borrower\DashboardController as BorrowerDashboardController;
use App\Http\Controllers\Borrower\LoanController as BorrowerLoanController;
use App\Http\Controllers\Borrower\LoanRequestController as BorrowerLoanRequestController;
use App\Http\Controllers\Borrower\ProfileController as BorrowerProfileController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
})->name('home');

Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{book:slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/copies', [BookCopyController::class, 'all'])->name('copies.index');

        Route::resource('books', BookController::class)->except(['show', 'destroy']);
        Route::post('/books/{book}/archive', [BookController::class, 'archive'])->name('books.archive');
        Route::post('/books/{book}/activate', [BookController::class, 'activate'])->name('books.activate');

        Route::resource('books.copies', BookCopyController::class)
            ->scoped()
            ->except(['show']);

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::post('/categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');

        Route::resource('authors', AuthorController::class)->except(['show']);
        Route::resource('publishers', PublisherController::class)->except(['show']);

        Route::get('/requests', [AdminLoanRequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/{loanRequest}', [AdminLoanRequestController::class, 'show'])->name('requests.show');
        Route::post('/requests/{loanRequest}/approve', [AdminLoanRequestController::class, 'approve'])->name('requests.approve');
        Route::post('/requests/{loanRequest}/reject', [AdminLoanRequestController::class, 'reject'])->name('requests.reject');
        Route::post('/requests/{loanRequest}/cancel', [AdminLoanRequestController::class, 'cancel'])->name('requests.cancel');
        Route::get('/requests/{loanRequest}/checkout', [AdminLoanRequestController::class, 'checkoutForm'])->name('requests.checkout');
        Route::post('/requests/{loanRequest}/checkout', [AdminLoanRequestController::class, 'checkout'])->name('requests.checkout.store');

        Route::get('/loans', [AdminLoanController::class, 'index'])->name('loans.index');
        Route::get('/returns', [AdminLoanController::class, 'returns'])->name('returns.index');
        Route::get('/history', [AdminLoanController::class, 'history'])->name('loans.history');
        Route::get('/loans/{loan}/return', [AdminLoanController::class, 'returnForm'])->name('loans.return');
        Route::post('/loans/{loan}/return', [AdminLoanController::class, 'returnLoan'])->name('loans.return.store');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/peminjaman', [ReportController::class, 'loans'])->name('reports.loans');
        Route::get('/reports/pengembalian', [ReportController::class, 'returns'])->name('reports.returns');
        Route::get('/reports/keterlambatan', [ReportController::class, 'overdue'])->name('reports.overdue');
        Route::get('/reports/inventaris', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/populer', [ReportController::class, 'popular'])->name('reports.popular');
        Route::get('/reports/{report}/export', [ReportController::class, 'export'])
            ->whereIn('report', ['ringkasan', 'peminjaman', 'pengembalian', 'keterlambatan', 'inventaris', 'populer'])
            ->name('reports.export');
        Route::get('/reports/{report}/print', [ReportController::class, 'print'])
            ->whereIn('report', ['ringkasan', 'peminjaman', 'pengembalian', 'keterlambatan', 'inventaris', 'populer'])
            ->name('reports.print');

        Route::resource('users', UserController::class)->except(['show']);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        Route::get('/settings', [LibrarySettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [LibrarySettingController::class, 'update'])->name('settings.update');

        Route::resource('announcements', AnnouncementController::class)->except(['show']);
        Route::post('/announcements/{announcement}/toggle', [AnnouncementController::class, 'toggle'])->name('announcements.toggle');
    });

Route::middleware(['auth', 'active', 'role:guru,siswa'])
    ->group(function () {
        Route::get('/beranda', [BorrowerDashboardController::class, 'index'])->name('borrower.dashboard');

        Route::get('/pinjaman', [BorrowerLoanController::class, 'index'])->name('borrower.loans.index');
        Route::get('/riwayat', [BorrowerLoanController::class, 'history'])->name('borrower.loans.history');

        Route::get('/profil', [BorrowerProfileController::class, 'edit'])->name('borrower.profile.edit');
        Route::put('/profil', [BorrowerProfileController::class, 'update'])->name('borrower.profile.update');

        Route::get('/katalog/{book:slug}/ajukan', [BorrowerLoanRequestController::class, 'create'])->name('borrower.requests.create');
        Route::post('/katalog/{book:slug}/ajukan', [BorrowerLoanRequestController::class, 'store'])->name('borrower.requests.store');
        Route::post('/pengajuan/{loanRequest}/batal', [BorrowerLoanRequestController::class, 'cancel'])->name('borrower.requests.cancel');
    });
