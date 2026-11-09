<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') &mdash; {{ config('library.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset(config('library.logo')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="admin-shell d-flex">
        <aside class="offcanvas-lg offcanvas-start admin-sidebar" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
            <div class="offcanvas-header border-bottom d-lg-none">
                <h2 class="offcanvas-title h6 mb-0" id="adminSidebarLabel">Menu Admin</h2>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                    <span class="brand-mark brand-mark-sm"><i class="bi bi-book-half" aria-hidden="true"></i></span>
                    <span class="d-flex flex-column lh-sm min-w-0">
                        <span class="fw-semibold text-truncate">{{ config('library.name') }}</span>
                        <span class="small text-muted text-truncate">{{ config('library.school_name') }}</span>
                    </span>
                </a>

                <nav class="sidebar-nav nav flex-column gap-1 mt-3" aria-label="Navigasi admin">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2" aria-hidden="true"></i>Dashboard
                    </a>

                    <div class="sidebar-heading">Katalog Buku</div>
                    <a class="nav-link {{ request()->routeIs('admin.books.index', 'admin.books.create', 'admin.books.edit') ? 'active' : '' }}" href="{{ route('admin.books.index') }}">
                        <i class="bi bi-journal-bookmark" aria-hidden="true"></i>Daftar Buku
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="bi bi-tags" aria-hidden="true"></i>Kategori
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.authors.*') ? 'active' : '' }}" href="{{ route('admin.authors.index') }}">
                        <i class="bi bi-pen" aria-hidden="true"></i>Penulis
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.publishers.*') ? 'active' : '' }}" href="{{ route('admin.publishers.index') }}">
                        <i class="bi bi-building" aria-hidden="true"></i>Penerbit
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.copies.*', 'admin.books.copies.*') ? 'active' : '' }}" href="{{ route('admin.copies.index') }}">
                        <i class="bi bi-collection" aria-hidden="true"></i>Eksemplar/Inventaris
                    </a>

                    <div class="sidebar-heading">Transaksi Peminjaman</div>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-inbox" aria-hidden="true"></i>Permintaan Peminjaman
                    </a>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-arrow-left-right" aria-hidden="true"></i>Peminjaman Aktif
                    </a>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-arrow-return-left" aria-hidden="true"></i>Pengembalian
                    </a>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-clock-history" aria-hidden="true"></i>Riwayat Transaksi
                    </a>

                    <div class="sidebar-heading">Laporan</div>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-bar-chart" aria-hidden="true"></i>Ringkasan
                    </a>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-file-earmark-text" aria-hidden="true"></i>Peminjaman &amp; Pengembalian
                    </a>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>Keterlambatan
                    </a>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-box-seam" aria-hidden="true"></i>Inventaris
                    </a>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-star" aria-hidden="true"></i>Buku Populer
                    </a>

                    <div class="sidebar-heading">Manajemen</div>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-people" aria-hidden="true"></i>Pengguna
                    </a>
                    <a class="nav-link disabled" href="#" aria-disabled="true">
                        <i class="bi bi-gear" aria-hidden="true"></i>Pengaturan
                    </a>
                </nav>

                <div class="mt-auto pt-3 border-top small text-muted">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                            <div class="text-truncate">{{ auth()->user()->roleLabel() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="admin-main flex-grow-1 d-flex flex-column">
            <header class="admin-topbar px-3 px-lg-4 py-3 d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Buka menu">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>

                <div class="flex-grow-1 min-w-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@yield('breadcrumb', 'Dashboard')</li>
                        </ol>
                    </nav>
                </div>

                <div class="dropdown">
                    <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                {{ auth()->user()->email ?? auth()->user()->nomor_induk }}
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </header>

            <main class="flex-grow-1 p-3 p-lg-4">
                @yield('content')
            </main>

            <footer class="px-3 px-lg-4 py-3 small text-muted border-top bg-white">
                {{ config('library.name') }} &mdash; {{ config('library.school_name') }}
            </footer>
        </div>
    </div>

    @include('partials.flash')
    @stack('scripts')
</body>
</html>
