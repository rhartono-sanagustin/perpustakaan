@inject('identity', 'App\Services\LibraryIdentity')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') &mdash; {{ $identity->libraryName() }}</title>
    <link rel="icon" href="{{ $identity->logoUrl() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="flex min-h-screen flex-col bg-zinc-100 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <div class="absolute end-4 top-4">
        @include('partials.theme-switcher')
    </div>

    <main class="flex flex-1 items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-6 flex items-center justify-center gap-3 text-center">
                <x-library-logo size="md" />
                <div class="text-start">
                    <div class="text-base font-semibold">{{ $identity->libraryName() }}</div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $identity->schoolName() }}</div>
                </div>
            </div>

            <flux:card class="border-t-4 border-t-[var(--color-accent)]">
                @yield('content')
            </flux:card>

            <p class="mt-6 text-center text-xs text-zinc-500 dark:text-zinc-400">
                &copy; {{ date('Y') }} {{ $identity->schoolName() }}
            </p>
        </div>
    </main>

    <flux:toast position="top end" />
    @include('partials.flash')
    @stack('scripts')
    @fluxScripts
</body>
</html>
