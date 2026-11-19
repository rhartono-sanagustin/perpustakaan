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
    @fluxAppearance
</head>
<body class="flex min-h-screen flex-col bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <header class="sticky top-0 z-30 border-b border-zinc-200 border-t-4 border-t-[var(--color-accent)] bg-white/90 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/90">
        <div class="mx-auto flex min-h-16 w-full max-w-7xl items-center gap-3 px-4 lg:px-8">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2">
                <x-library-logo size="sm" />
                <span class="hidden min-w-0 flex-col leading-tight sm:flex">
                    <span class="truncate text-sm font-semibold">{{ $identity->libraryName() }}</span>
                    <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $identity->schoolName() }}</span>
                </span>
                <span class="truncate text-sm font-semibold sm:hidden">{{ $identity->libraryName() }}</span>
            </a>

            <nav class="ms-auto flex items-center gap-1" aria-label="Navigasi utama">
                <flux:button variant="subtle" size="sm" icon="book-open" href="{{ route('catalog.index') }}" class="max-sm:[&>span]:hidden">
                    Katalog
                </flux:button>

                @if ($visitKioskEnabled ?? false)
                    <flux:button variant="subtle" size="sm" icon="qr-code" href="{{ route('visits.kiosk') }}" class="max-sm:[&>span]:hidden">
                        Kunjungan
                    </flux:button>
                @endif

                @include('partials.theme-switcher')

                @auth
                    <flux:button variant="primary" size="sm" icon="home" href="{{ route('dashboard') }}">
                        Dashboard
                    </flux:button>
                @else
                    <flux:button variant="primary" size="sm" icon="arrow-right-start-on-rectangle" href="{{ route('login') }}">
                        Masuk
                    </flux:button>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-zinc-200 bg-white py-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mx-auto flex w-full max-w-7xl flex-col justify-between gap-1 px-4 text-xs text-zinc-500 sm:flex-row lg:px-8 dark:text-zinc-400">
            <span>&copy; {{ date('Y') }} {{ $identity->schoolName() }}</span>
            <span>{{ $identity->libraryName() }}</span>
        </div>
    </footer>

    <flux:toast position="top end" />
    @include('partials.confirm-modal')
    @include('partials.flash')
    @stack('scripts')
    @fluxScripts
</body>
</html>
