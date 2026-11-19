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
            <div class="mx-auto flex w-full max-w-6xl flex-col justify-between gap-3 px-4 py-3 sm:flex-row sm:items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-inherit">
                    <x-library-logo size="sm" />
                    <span class="flex flex-col leading-tight">
                        <span class="font-semibold">{{ $identity->libraryName() }}</span>
                        <span class="text-xs text-slate-500">{{ $identity->schoolName() }}</span>
                    </span>
                </a>

                <div class="text-left sm:text-right" aria-label="Waktu sekolah">
                    <div class="kiosk-clock-time" data-kiosk-clock>--:--:--</div>
                    <div class="kiosk-clock-date" data-kiosk-date>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</div>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-5 lg:py-8">
            @yield('content')
        </main>

        <footer class="kiosk-footer">
            <div class="mx-auto flex w-full max-w-6xl flex-col justify-between gap-2 px-4 py-3 text-xs text-slate-500 sm:flex-row">
                <span>&copy; {{ date('Y') }} {{ $identity->schoolName() }}</span>
                <a href="{{ route('home') }}" class="text-slate-500 underline-offset-2 hover:underline">Kembali ke beranda</a>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
