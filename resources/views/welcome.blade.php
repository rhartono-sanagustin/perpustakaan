@extends('layouts.public')
@inject('identity', 'App\Services\LibraryIdentity')

@section('title', 'Beranda')

@section('content')
    @include('partials.landing-carousel', ['carouselSlides' => $carouselSlides ?? collect()])

    <section class="mx-auto w-full max-w-7xl px-4 py-8 lg:px-8 lg:py-12">
        <div class="grid items-center gap-8 lg:grid-cols-2 lg:gap-12">
            <div>
                <flux:badge color="zinc" class="mb-4">Sistem Informasi Perpustakaan Sekolah</flux:badge>

                <flux:heading size="2xl" level="1" class="max-w-2xl text-3xl! sm:text-4xl!">
                    Temukan buku, ajukan pinjaman, dan pantau koleksi dalam satu tempat.
                </flux:heading>

                <flux:text class="mt-4 max-w-xl text-base!">
                    {{ $identity->tagline() }} Kelola koleksi, peminjaman, dan laporan perpustakaan
                    sekolah dengan alur yang jelas dan mudah digunakan.
                </flux:text>

                <div class="mt-6 flex flex-wrap gap-3">
                    <flux:button variant="primary" icon="magnifying-glass" href="{{ route('catalog.index') }}" class="h-11 px-5">
                        Jelajahi Katalog
                    </flux:button>
                    <flux:button variant="outline" icon="arrow-right-start-on-rectangle" href="{{ route('login') }}" class="h-11 px-5">
                        Masuk
                    </flux:button>
                </div>

                <p class="mt-4 text-xs text-zinc-500 dark:text-zinc-400">
                    Katalog dapat diakses tanpa login. Pengajuan pinjaman memerlukan akun aktif.
                </p>
            </div>

            <flux:card class="p-5!">
                <div class="mb-5 flex items-center gap-3">
                    <x-library-logo size="md" />
                    <div>
                        <div class="font-semibold">{{ $identity->libraryName() }}</div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $identity->schoolName() }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @foreach ([
                        ['icon' => 'magnifying-glass', 'title' => 'Cari koleksi', 'text' => 'Temukan buku berdasarkan judul, penulis, atau ISBN.', 'color' => 'text-sky-600 dark:text-sky-400'],
                        ['icon' => 'book-open', 'title' => 'Ajukan pinjaman', 'text' => 'Kirim pengajuan dan pantau statusnya.', 'color' => 'text-emerald-600 dark:text-emerald-400'],
                        ['icon' => 'calendar-days', 'title' => 'Pantau jatuh tempo', 'text' => 'Lihat tanggal kembali dan status pinjaman.', 'color' => 'text-amber-600 dark:text-amber-400'],
                        ['icon' => 'chart-bar', 'title' => 'Laporan rapi', 'text' => 'Data sirkulasi dan inventaris siap diekspor.', 'color' => 'text-cyan-600 dark:text-cyan-400'],
                    ] as $feature)
                        <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                            <flux:icon :icon="$feature['icon']" variant="outline" class="size-6 {{ $feature['color'] }}" />
                            <div class="mt-2 text-sm font-semibold">{{ $feature['title'] }}</div>
                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $feature['text'] }}</div>
                        </div>
                    @endforeach
                </div>
            </flux:card>
        </div>
    </section>

    @if ($visitKioskEnabled ?? false)
        <section class="mx-auto w-full max-w-7xl px-4 pb-8 lg:px-8" aria-labelledby="kiosk-heading">
            <flux:card class="border-s-4! border-s-[var(--color-accent)]!">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                    <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700 dark:bg-sky-400/15 dark:text-sky-300">
                        <flux:icon.qr-code class="size-6" />
                    </span>
                    <div class="flex-1">
                        <flux:heading size="lg" id="kiosk-heading">Berkunjung ke perpustakaan?</flux:heading>
                        <flux:text class="mt-1">
                            Scan kartu anggota atau ketik nomor induk di kiosk kunjungan untuk mencatat kehadiran
                            tanpa perlu login.
                        </flux:text>
                    </div>
                    <flux:button variant="primary" icon="qr-code" href="{{ route('visits.kiosk') }}" class="shrink-0">
                        Buka Kiosk Kunjungan
                    </flux:button>
                </div>
            </flux:card>
        </section>
    @endif

    <section id="fitur" class="mx-auto w-full max-w-7xl px-4 py-8 lg:px-8">
        <div class="mb-6 text-center">
            <flux:heading size="xl" level="2">Dirancang untuk seluruh warga sekolah</flux:heading>
            <flux:text class="mt-1">Satu alur kerja untuk admin, guru, dan siswa.</flux:text>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach ([
                ['icon' => 'shield-check', 'title' => 'Akses sesuai peran', 'text' => 'Admin mengelola operasional, guru dan siswa mengakses katalog serta pinjaman miliknya.', 'box' => 'bg-sky-100 text-sky-700 dark:bg-sky-400/15 dark:text-sky-300'],
                ['icon' => 'rectangle-stack', 'title' => 'Inventaris eksemplar', 'text' => 'Ketersediaan dihitung dari eksemplar yang benar-benar siap dipinjam.', 'box' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300'],
                ['icon' => 'clock', 'title' => 'Transaksi terlacak', 'text' => 'Setiap pengajuan, penyerahan, dan pengembalian tercatat dengan status yang jelas.', 'box' => 'bg-amber-100 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300'],
            ] as $feature)
                <flux:card class="h-full">
                    <span class="mb-3 inline-flex size-11 items-center justify-center rounded-lg {{ $feature['box'] }}">
                        <flux:icon :icon="$feature['icon']" class="size-5" />
                    </span>
                    <flux:heading size="lg" level="3">{{ $feature['title'] }}</flux:heading>
                    <flux:text class="mt-2">{{ $feature['text'] }}</flux:text>
                </flux:card>
            @endforeach
        </div>
    </section>
@endsection
