@inject('identity', 'App\Services\LibraryIdentity')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-timezone="{{ config('app.timezone') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Kunjungan') &mdash; {{ $identity->libraryName() }}</title>
    <link rel="icon" href="{{ $identity->logoUrl() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="kiosk-body">
    <div class="kiosk-shell">
        <header class="kiosk-header">
            <div class="container-xl d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 py-3">
                <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-reset">
                    <x-library-logo size="sm" />
                    <span class="d-flex flex-column lh-sm">
                        <span class="fw-semibold">{{ $identity->libraryName() }}</span>
                        <span class="small text-muted">{{ $identity->schoolName() }}</span>
                    </span>
                </a>

                <div class="kiosk-clock text-sm-end" aria-label="Waktu sekolah">
                    <div class="kiosk-clock-time" data-kiosk-clock>--:--:--</div>
                    <div class="kiosk-clock-date" data-kiosk-date>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</div>
                </div>
            </div>
        </header>

        <main class="container-xl flex-grow-1 py-4 py-lg-5">
            @yield('content')
        </main>

        <footer class="kiosk-footer">
            <div class="container-xl d-flex flex-column flex-sm-row justify-content-between gap-2 small text-muted py-3">
                <span>&copy; {{ date('Y') }} {{ $identity->schoolName() }}</span>
                <a href="{{ route('home') }}" class="text-muted">Kembali ke beranda</a>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
