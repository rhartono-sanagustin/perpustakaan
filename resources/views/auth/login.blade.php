@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="mb-6">
        <flux:heading size="lg">Masuk ke Perpustakaan</flux:heading>
        <flux:text class="mt-1">Gunakan email atau nomor induk yang terdaftar.</flux:text>
    </div>

    <x-form-errors title="Periksa kembali data Anda." />

    <form method="POST" action="{{ route('login') }}" data-loading class="space-y-4">
        @csrf

        <flux:field>
            <flux:label for="login">Email atau Nomor Induk</flux:label>
            <flux:input
                id="login"
                name="login"
                type="text"
                value="{{ old('login') }}"
                placeholder="nama@sekolah.sch.id / NIS"
                icon="user"
                required
                autofocus
                autocomplete="username"
                aria-describedby="login-help"
            />
            <flux:description id="login-help">Gunakan akun yang diberikan oleh admin perpustakaan.</flux:description>
            <flux:error name="login" />
        </flux:field>

        <flux:field>
            <flux:label for="password">Kata Sandi</flux:label>
            <div class="relative">
                <flux:input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Masukkan kata sandi"
                    icon="lock-closed"
                    class="pe-12"
                    required
                    autocomplete="current-password"
                />
                <button
                    type="button"
                    class="absolute end-1 top-1/2 inline-flex size-8 -translate-y-1/2 items-center justify-center rounded-md text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-white/10 dark:hover:text-white"
                    data-toggle-password="#password"
                    aria-label="Tampilkan kata sandi"
                >
                    <flux:icon.eye data-password-show variant="mini" class="size-4" />
                    <flux:icon.eye-slash data-password-hide variant="mini" class="hidden size-4" />
                </button>
            </div>
            <flux:error name="password" />
        </flux:field>

        <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
            <flux:checkbox name="remember" value="1" :checked="(bool) old('remember')" label="Ingat saya" />
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Lupa kata sandi? Hubungi admin.</span>
        </div>

        <flux:button type="submit" variant="primary" icon="arrow-right-start-on-rectangle" class="w-full" data-loading-text="Memproses...">
            Masuk
        </flux:button>
    </form>

    <flux:separator class="my-6" />

    <p class="text-center text-xs text-zinc-500 dark:text-zinc-400">
        Belum punya akun? Hubungi admin perpustakaan untuk pendaftaran.
    </p>
@endsection
