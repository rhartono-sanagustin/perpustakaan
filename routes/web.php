<?php

use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookCopyController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Borrower\DashboardController as BorrowerDashboardController;
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
    });

Route::middleware(['auth', 'active', 'role:guru,siswa'])
    ->group(function () {
        Route::get('/beranda', [BorrowerDashboardController::class, 'index'])->name('borrower.dashboard');
    });
