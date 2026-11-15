@inject('identity', 'App\Services\LibraryIdentity')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') &mdash; {{ $identity->libraryName() }}</title>
    <link rel="icon" href="{{ $identity->logoUrl() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg borrower-navbar sticky-top">
        <div class="container-xl">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('borrower.dashboard') }}">
                <x-library-logo size="sm" />
                <span class="d-flex flex-column lh-sm">
                    <span class="fw-semibold">{{ $identity->libraryName() }}</span>
                    <span class="small text-muted">{{ $identity->schoolName() }}</span>
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
                            <a class="nav-link {{ request()->routeIs('catalog.*', 'borrower.requests.create') ? 'active' : '' }}" href="{{ route('catalog.index') }}">
                                Katalog
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('borrower.loans.index') ? 'active' : '' }}" href="{{ route('borrower.loans.index') }}">
                                Pinjaman Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('borrower.loans.history') ? 'active' : '' }}" href="{{ route('borrower.loans.history') }}">Riwayat</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('borrower.profile.*') ? 'active' : '' }}" href="{{ route('borrower.profile.edit') }}">Profil</a>
                        </li>
                        <li class="nav-item dropdown" data-notifications data-mode="borrower" data-endpoint="{{ route('notifications.status') }}">
                            <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi">
                                <i class="bi bi-bell" aria-hidden="true"></i>
                                <span class="d-lg-none ms-1">Notifikasi</span>
                                <span class="badge rounded-pill text-bg-danger d-none ms-1" data-notification-badge>0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                                <div class="notification-header">
                                    <span class="fw-semibold">Notifikasi</span>
                                </div>
                                <div class="notification-body" data-notification-body>
                                    <div class="notification-empty">Memuat notifikasi...</div>
                                </div>
                                <div class="notification-footer" data-notification-updated></div>
                            </div>
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
            <span>&copy; {{ date('Y') }} {{ $identity->schoolName() }}</span>
            <span>{{ $identity->libraryName() }}</span>
        </div>
    </footer>

    @include('partials.confirm-modal')
    @include('partials.flash')
    @stack('scripts')
</body>
</html>
