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
    <nav class="navbar navbar-expand borrower-navbar">
        <div class="container-xl">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-mark brand-mark-sm"><i class="bi bi-book-half" aria-hidden="true"></i></span>
                <span class="d-none d-sm-flex flex-column lh-sm">
                    <span class="fw-semibold">{{ config('library.name') }}</span>
                    <span class="small text-muted">{{ config('library.school_name') }}</span>
                </span>
                <span class="d-sm-none fw-semibold">{{ config('library.name') }}</span>
            </a>

            <div class="ms-auto d-flex align-items-center gap-1 gap-sm-2">
                <a href="{{ route('catalog.index') }}" class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}" aria-label="Katalog">
                    <i class="bi bi-journal-bookmark d-sm-none" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline">Katalog</span>
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>Masuk
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="border-top bg-white py-3 mt-auto">
        <div class="container-xl d-flex flex-column flex-sm-row justify-content-between gap-2 small text-muted">
            <span>&copy; {{ date('Y') }} {{ config('library.school_name') }}</span>
            <span>{{ config('library.name') }}</span>
        </div>
    </footer>

    @include('partials.confirm-modal')
    @include('partials.flash')
    @stack('scripts')
</body>
</html>
