@inject('identity', 'App\Services\LibraryIdentity')
@php
    $user = auth()->user();
    $initials = \Illuminate\Support\Str::of($user->name)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('');
@endphp
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
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:sidebar.header>
            <flux:sidebar.brand :name="$identity->libraryName()" href="{{ route('borrower.dashboard') }}">
                <img src="{{ $identity->logoUrl() }}" alt="" class="h-6 w-6 object-contain">
            </flux:sidebar.brand>

            <flux:sidebar.collapse />
        </flux:sidebar.header>

        <div class="-mt-3 px-2 text-[0.7rem] leading-snug text-zinc-500 in-data-flux-sidebar-collapsed-desktop:hidden dark:text-zinc-400">
            {{ $identity->schoolName() }}
        </div>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="{{ route('borrower.dashboard') }}" :current="request()->routeIs('borrower.dashboard')">
                Beranda
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open" href="{{ route('catalog.index') }}" :current="request()->routeIs('catalog.*', 'borrower.requests.*')">
                Katalog Buku
            </flux:sidebar.item>

            <flux:sidebar.group
                expandable
                heading="Peminjaman Saya"
                icon="arrows-right-left"
                :expanded="request()->routeIs('borrower.loans.*')"
            >
                <flux:sidebar.item icon="clock" href="{{ route('borrower.loans.index') }}" :current="request()->routeIs('borrower.loans.index')">
                    Pinjaman Aktif
                </flux:sidebar.item>
                <flux:sidebar.item icon="arrow-uturn-left" href="{{ route('borrower.loans.history') }}" :current="request()->routeIs('borrower.loans.history')">
                    Riwayat Peminjaman
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group
                expandable
                heading="Akun Saya"
                icon="user-circle"
                :expanded="request()->routeIs('borrower.profile.*')"
            >
                <flux:sidebar.item icon="user-circle" href="{{ route('borrower.profile.edit') }}" :current="request()->routeIs('borrower.profile.*')">
                    Profil &amp; Keamanan
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="arrow-top-right-on-square" href="{{ route('home') }}">
                Situs Perpustakaan
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:dropdown position="top" align="start">
            <flux:sidebar.profile :name="$user->name" :initials="$initials" />

            <flux:menu>
                <flux:menu.heading>{{ $user->name }}</flux:menu.heading>
                <div class="px-2 pb-2 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $user->roleLabel() }} &middot; {{ $user->nomor_induk }}
                </div>
                <flux:menu.separator />
                <flux:menu.item href="{{ route('borrower.profile.edit') }}" icon="user-circle">
                    Profil Saya
                </flux:menu.item>
                <flux:menu.item href="{{ route('catalog.index') }}" icon="book-open">
                    Katalog Buku
                </flux:menu.item>
                <flux:menu.separator />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" variant="danger" class="w-full">
                        Keluar
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <flux:header class="sticky top-0 z-20 min-h-16 border-b border-zinc-200 bg-white/90 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/90">
        <div class="flex w-full items-center gap-2 px-4 lg:px-8">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <div class="min-w-0 flex-1">
                <flux:breadcrumbs class="text-sm">
                    <flux:breadcrumbs.item href="{{ route('borrower.dashboard') }}">Beranda</flux:breadcrumbs.item>
                    @unless (request()->routeIs('borrower.dashboard'))
                        <flux:breadcrumbs.item>@yield('title', 'Halaman')</flux:breadcrumbs.item>
                    @endunless
                </flux:breadcrumbs>
            </div>

            <div class="flex items-center gap-1">
                @include('partials.notifications-menu', [
                    'mode' => 'borrower',
                    'endpoint' => route('notifications.status'),
                ])

                @include('partials.theme-switcher')

                <flux:dropdown position="bottom" align="end">
                    <flux:profile
                        :name="$user->name"
                        :initials="$initials"
                        class="max-sm:[&>span]:hidden"
                        aria-label="Menu akun {{ $user->name }}"
                    />

                    <flux:menu>
                        <flux:menu.heading>{{ $user->name }}</flux:menu.heading>
                        <div class="px-2 pb-2 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $user->roleLabel() }} &middot; {{ $user->nomor_induk }}
                        </div>
                        <flux:menu.separator />
                        <flux:menu.item href="{{ route('borrower.profile.edit') }}" icon="user-circle">
                            Profil Saya
                        </flux:menu.item>
                        <flux:menu.item href="{{ route('borrower.loans.index') }}" icon="clock">
                            Pinjaman Saya
                        </flux:menu.item>
                        <flux:menu.item href="{{ route('home') }}" icon="arrow-top-right-on-square">
                            Lihat Situs
                        </flux:menu.item>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" variant="danger" class="w-full">
                                Keluar
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>
    </flux:header>

    <flux:main>
        @yield('content')

        <footer class="mt-8 border-t border-zinc-200 pt-4 text-xs text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
            &copy; {{ date('Y') }} {{ $identity->schoolName() }} &mdash; {{ $identity->libraryName() }}
        </footer>
    </flux:main>

    <flux:toast position="top end" />
    @include('partials.confirm-modal')
    @include('partials.flash')
    @stack('scripts')
    @fluxScripts
</body>
</html>
