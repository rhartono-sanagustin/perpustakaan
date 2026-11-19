@inject('identity', 'App\Services\LibraryIdentity')
@php
    $user = auth()->user();
    $initials = \Illuminate\Support\Str::of($user->name)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('');
    $breadcrumb = trim($__env->yieldContent('breadcrumb', 'Dashboard'));
    $crumbs = array_values(array_filter(array_map('trim', explode('/', $breadcrumb))));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') &mdash; {{ $identity->libraryName() }}</title>
    <link rel="icon" href="{{ $identity->logoUrl() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:sidebar.header>
            <flux:sidebar.brand :name="$identity->libraryName()" href="{{ route('admin.dashboard') }}">
                <img src="{{ $identity->logoUrl() }}" alt="" class="h-6 w-6 object-contain">
            </flux:sidebar.brand>

            <flux:sidebar.collapse />
        </flux:sidebar.header>

        <div class="-mt-3 px-2 text-[0.7rem] leading-snug text-zinc-500 in-data-flux-sidebar-collapsed-desktop:hidden dark:text-zinc-400">
            {{ $identity->schoolName() }}
        </div>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="{{ route('admin.dashboard') }}" :current="request()->routeIs('admin.dashboard')">
                Dashboard
            </flux:sidebar.item>

            <flux:sidebar.group
                expandable
                heading="Katalog Buku"
                icon="book-open"
                :expanded="request()->routeIs('admin.books.*', 'admin.categories.*', 'admin.authors.*', 'admin.publishers.*', 'admin.copies.*')"
            >
                <flux:sidebar.item icon="book-open" href="{{ route('admin.books.index') }}" :current="request()->routeIs('admin.books.index', 'admin.books.create', 'admin.books.edit')">
                    Daftar Buku
                </flux:sidebar.item>
                <flux:sidebar.item icon="tag" href="{{ route('admin.categories.index') }}" :current="request()->routeIs('admin.categories.*')">
                    Kategori
                </flux:sidebar.item>
                <flux:sidebar.item icon="pencil" href="{{ route('admin.authors.index') }}" :current="request()->routeIs('admin.authors.*')">
                    Penulis
                </flux:sidebar.item>
                <flux:sidebar.item icon="building-office" href="{{ route('admin.publishers.index') }}" :current="request()->routeIs('admin.publishers.*')">
                    Penerbit
                </flux:sidebar.item>
                <flux:sidebar.item icon="rectangle-stack" href="{{ route('admin.copies.index') }}" :current="request()->routeIs('admin.copies.*', 'admin.books.copies.*')">
                    Eksemplar / Inventaris
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group
                expandable
                heading="Sirkulasi"
                icon="arrows-right-left"
                :expanded="request()->routeIs('admin.requests.*', 'admin.loans.*', 'admin.returns.*', 'admin.visits.*')"
            >
                <flux:sidebar.item icon="inbox" href="{{ route('admin.requests.index') }}" :current="request()->routeIs('admin.requests.*')">
                    Permintaan Peminjaman
                </flux:sidebar.item>
                <flux:sidebar.item icon="arrows-right-left" href="{{ route('admin.loans.index') }}" :current="request()->routeIs('admin.loans.index')">
                    Peminjaman Aktif
                </flux:sidebar.item>
                <flux:sidebar.item icon="arrow-uturn-left" href="{{ route('admin.returns.index') }}" :current="request()->routeIs('admin.returns.*')">
                    Pengembalian
                </flux:sidebar.item>
                <flux:sidebar.item icon="clock" href="{{ route('admin.loans.history') }}" :current="request()->routeIs('admin.loans.history', 'admin.loans.return')">
                    Riwayat Transaksi
                </flux:sidebar.item>
                <flux:sidebar.item icon="qr-code" href="{{ route('admin.visits.index') }}" :current="request()->routeIs('admin.visits.*')">
                    Kunjungan
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group
                expandable
                heading="Laporan"
                icon="chart-bar"
                :expanded="request()->routeIs('admin.reports.*')"
            >
                <flux:sidebar.item icon="chart-bar" href="{{ route('admin.reports.index') }}" :current="request()->routeIs('admin.reports.index')">
                    Ringkasan
                </flux:sidebar.item>
                <flux:sidebar.item icon="document-text" href="{{ route('admin.reports.loans') }}" :current="request()->routeIs('admin.reports.loans')">
                    Peminjaman
                </flux:sidebar.item>
                <flux:sidebar.item icon="arrow-uturn-left" href="{{ route('admin.reports.returns') }}" :current="request()->routeIs('admin.reports.returns')">
                    Pengembalian
                </flux:sidebar.item>
                <flux:sidebar.item icon="exclamation-triangle" href="{{ route('admin.reports.overdue') }}" :current="request()->routeIs('admin.reports.overdue')">
                    Keterlambatan
                </flux:sidebar.item>
                <flux:sidebar.item icon="archive-box" href="{{ route('admin.reports.inventory') }}" :current="request()->routeIs('admin.reports.inventory')">
                    Inventaris
                </flux:sidebar.item>
                <flux:sidebar.item icon="star" href="{{ route('admin.reports.popular') }}" :current="request()->routeIs('admin.reports.popular')">
                    Buku Populer
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group
                expandable
                heading="Manajemen"
                icon="users"
                :expanded="request()->routeIs('admin.member-cards.*', 'admin.users.*', 'admin.announcements.*', 'admin.settings.*')"
            >
                <flux:sidebar.item icon="identification" href="{{ route('admin.member-cards.index') }}" :current="request()->routeIs('admin.member-cards.*')">
                    Kartu Anggota
                </flux:sidebar.item>
                <flux:sidebar.item icon="users" href="{{ route('admin.users.index') }}" :current="request()->routeIs('admin.users.*')">
                    Pengguna
                </flux:sidebar.item>
                <flux:sidebar.item icon="megaphone" href="{{ route('admin.announcements.index') }}" :current="request()->routeIs('admin.announcements.*')">
                    Pengumuman
                </flux:sidebar.item>
                <flux:sidebar.item icon="cog-6-tooth" href="{{ route('admin.settings.index') }}" :current="request()->routeIs('admin.settings.*')">
                    Pengaturan
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group expandable heading="Tautan Publik" icon="arrow-top-right-on-square">
                <flux:sidebar.item icon="book-open" href="{{ route('catalog.index') }}">
                    Katalog Publik
                </flux:sidebar.item>
                <flux:sidebar.item icon="qr-code" href="{{ route('visits.kiosk') }}">
                    Kiosk Kunjungan
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:dropdown position="top" align="start">
            <flux:sidebar.profile :name="$user->name" :initials="$initials" />

            <flux:menu>
                <flux:menu.heading>{{ $user->name }}</flux:menu.heading>
                <div class="px-2 pb-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $user->roleLabel() }} &middot; {{ $user->email ?? $user->nomor_induk }}</div>
                <flux:menu.separator />
                <flux:menu.item href="{{ route('admin.settings.index') }}" icon="cog-6-tooth">
                    Pengaturan
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
                    <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}">Admin</flux:breadcrumbs.item>
                    @foreach ($crumbs as $crumb)
                        <flux:breadcrumbs.item>{{ $crumb }}</flux:breadcrumbs.item>
                    @endforeach
                </flux:breadcrumbs>
            </div>

            <div class="flex items-center gap-1">
                @include('partials.notifications-menu', [
                    'mode' => 'admin',
                    'endpoint' => route('admin.notifications'),
                    'sound' => true,
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
                        <div class="px-2 pb-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $user->roleLabel() }} &middot; {{ $user->email ?? $user->nomor_induk }}</div>
                        <flux:menu.separator />
                        <flux:menu.item href="{{ route('admin.settings.index') }}" icon="cog-6-tooth">
                            Pengaturan
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
            {{ $identity->libraryName() }} &mdash; {{ $identity->schoolName() }}
        </footer>
    </flux:main>

    <flux:toast position="top end" />
    @include('partials.confirm-modal')
    @include('partials.flash')
    @stack('scripts')
    @fluxScripts
</body>
</html>
