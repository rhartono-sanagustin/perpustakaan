<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') &mdash; {{ config('library.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset(config('library.logo')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg borrower-navbar sticky-top">
        <div class="container-xl">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('borrower.dashboard') }}">
                <span class="brand-mark brand-mark-sm"><i class="bi bi-book-half" aria-hidden="true"></i></span>
                <span class="d-flex flex-column lh-sm">
                    <span class="fw-semibold">{{ config('library.name') }}</span>
                    <span class="small text-muted">{{ config('library.school_name') }}</span>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#borrowerNav" aria-controls="borrowerNav" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="offcanvas-lg offcanvas-end" tabindex="-1" id="borrowerNav" aria-labelledby="borrowerNavLabel">
                <div class="offcanvas-header d-lg-none">
                    <h2 class="offcanvas-title h6" id="borrowerNavLabel">Menu</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('borrower.dashboard') ? 'active' : '' }}" href="{{ route('borrower.dashboard') }}">
                                Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}" href="{{ route('catalog.index') }}">
                                Katalog
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="#" aria-disabled="true">
                                Pinjaman Saya <span class="badge text-bg-light border ms-1">Segera</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="#" aria-disabled="true">Riwayat</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="#" aria-disabled="true">Profil</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle" aria-hidden="true"></i>
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <span class="dropdown-item-text small text-muted">
                                        {{ auth()->user()->roleLabel() }} &middot; {{ auth()->user()->nomor_induk }}
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
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main class="container-xl py-4 py-lg-5 flex-grow-1">
        @yield('content')
    </main>

    <footer class="border-top bg-white py-3 mt-auto">
        <div class="container-xl d-flex flex-column flex-sm-row justify-content-between gap-2 small text-muted">
            <span>&copy; {{ date('Y') }} {{ config('library.school_name') }}</span>
            <span>{{ config('library.name') }}</span>
        </div>
    </footer>

    @include('partials.flash')
    @stack('scripts')
</body>
</html>
