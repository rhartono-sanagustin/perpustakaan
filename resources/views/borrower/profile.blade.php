@extends('layouts.borrower')

@section('title', 'Profil')

@section('content')
    <x-page-header title="Profil" subtitle="Perbarui data kontak, kata sandi, dan preferensi tampilan akun Anda.">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrows-right-left" href="{{ route('borrower.loans.index') }}">
                Pinjaman Saya
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Periksa kembali data berikut:" />

    <div class="grid gap-4 xl:grid-cols-3">
        <div class="space-y-4">
            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Identitas Akun</flux:heading>
                </div>
                <div class="p-5">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-[var(--color-accent)] text-[var(--color-accent-foreground)]">
                            <flux:icon.user-circle variant="mini" class="size-6" />
                        </span>
                        <div class="min-w-0">
                            <div class="truncate font-semibold">{{ $user->name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->roleLabel() }}</div>
                        </div>
                    </div>

                    <dl class="grid grid-cols-3 gap-x-3 gap-y-2 text-sm">
                        <dt class="text-zinc-500 dark:text-zinc-400">Nomor induk</dt>
                        <dd class="col-span-2 text-end">{{ $user->nomor_induk }}</dd>

                        @if ($user->kelas)
                            <dt class="text-zinc-500 dark:text-zinc-400">Kelas</dt>
                            <dd class="col-span-2 text-end">{{ $user->kelas }}</dd>
                        @endif

                        @if ($user->unit_kerja)
                            <dt class="text-zinc-500 dark:text-zinc-400">Unit kerja</dt>
                            <dd class="col-span-2 text-end">{{ $user->unit_kerja }}</dd>
                        @endif

                        <dt class="text-zinc-500 dark:text-zinc-400">Status akun</dt>
                        <dd class="col-span-2 text-end">
                            <x-status-badge :label="$user->statusLabel()" :variant="$user->isActive() ? 'success' : 'secondary'" />
                        </dd>
                    </dl>
                </div>
            </flux:card>

            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Tampilan</flux:heading>
                </div>
                <div class="p-5" x-data>
                    <flux:switch
                        x-bind:checked="$flux.dark"
                        x-on:change="$flux.appearance = $event.target.checked ? 'dark' : 'light'"
                        label="Mode gelap"
                        description="Gunakan latar gelap agar nyaman membaca pada malam hari. Pilihan juga tersedia di menu tema pada header."
                    />
                </div>
            </flux:card>

            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Ketentuan Peminjaman</flux:heading>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-3 gap-x-3 gap-y-2 text-sm">
                        <dt class="text-zinc-500 dark:text-zinc-400">Kuota terpakai</dt>
                        <dd class="col-span-2 text-end">{{ $quota['used'] }} / {{ $quota['max'] }}</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Sisa kuota</dt>
                        <dd class="col-span-2 text-end">{{ $quota['remaining'] }} eksemplar</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Durasi pinjam</dt>
                        <dd class="col-span-2 text-end">{{ $quota['duration_days'] }} hari</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Batas pengambilan</dt>
                        <dd class="col-span-2 text-end">{{ $quota['pickup_deadline_days'] }} hari</dd>
                    </dl>
                </div>
            </flux:card>
        </div>

        <flux:card class="p-0! xl:col-span-2">
            <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Data Profil</flux:heading>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('borrower.profile.update') }}" data-loading>
                    @csrf
                    @method('PUT')

                    <div class="grid gap-5 sm:grid-cols-2">
                        <flux:field>
                            <flux:label for="name">Nama lengkap <span class="text-rose-600">*</span></flux:label>
                            <flux:input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="100" required autocomplete="name" />
                            <flux:error name="name" />
                        </flux:field>

                        <flux:field>
                            <flux:label for="email">Email</flux:label>
                            <flux:input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" autocomplete="email" />
                            <flux:description>Boleh dikosongkan jika tidak memiliki email.</flux:description>
                            <flux:error name="email" />
                        </flux:field>

                        <flux:field>
                            <flux:label for="no_telepon">Nomor telepon</flux:label>
                            <flux:input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" maxlength="20" autocomplete="tel" />
                            <flux:error name="no_telepon" />
                        </flux:field>
                    </div>

                    <flux:separator class="my-6" />

                    <flux:heading size="lg" level="2">Ganti Kata Sandi</flux:heading>
                    <flux:text class="mt-1">Kosongkan bagian ini jika tidak ingin mengganti kata sandi.</flux:text>

                    <div class="mt-4 grid gap-5 sm:grid-cols-3">
                        <flux:field>
                            <flux:label for="current_password">Kata sandi saat ini</flux:label>
                            <div class="relative">
                                <flux:input type="password" id="current_password" name="current_password" autocomplete="current-password" class="pr-10!" />
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center rounded-r-lg text-zinc-500 hover:text-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-accent)] dark:text-zinc-400 dark:hover:text-white"
                                    data-toggle-password="#current_password"
                                    aria-label="Tampilkan kata sandi saat ini"
                                >
                                    <flux:icon.eye variant="mini" class="size-4" data-password-show />
                                    <flux:icon.eye-slash variant="mini" class="hidden size-4" data-password-hide />
                                </button>
                            </div>
                            <flux:error name="current_password" />
                        </flux:field>

                        <flux:field>
                            <flux:label for="password">Kata sandi baru</flux:label>
                            <div class="relative">
                                <flux:input type="password" id="password" name="password" autocomplete="new-password" class="pr-10!" />
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center rounded-r-lg text-zinc-500 hover:text-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-accent)] dark:text-zinc-400 dark:hover:text-white"
                                    data-toggle-password="#password"
                                    aria-label="Tampilkan kata sandi baru"
                                >
                                    <flux:icon.eye variant="mini" class="size-4" data-password-show />
                                    <flux:icon.eye-slash variant="mini" class="hidden size-4" data-password-hide />
                                </button>
                            </div>
                            <flux:description>Minimal 8 karakter.</flux:description>
                            <flux:error name="password" />
                        </flux:field>

                        <flux:field>
                            <flux:label for="password_confirmation">Ulangi kata sandi baru</flux:label>
                            <div class="relative">
                                <flux:input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="pr-10!" />
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center rounded-r-lg text-zinc-500 hover:text-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-accent)] dark:text-zinc-400 dark:hover:text-white"
                                    data-toggle-password="#password_confirmation"
                                    aria-label="Tampilkan konfirmasi kata sandi"
                                >
                                    <flux:icon.eye variant="mini" class="size-4" data-password-show />
                                    <flux:icon.eye-slash variant="mini" class="hidden size-4" data-password-hide />
                                </button>
                            </div>
                        </flux:field>
                    </div>

                    <div class="mt-6 flex flex-col gap-2 sm:flex-row">
                        <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                            Simpan Profil
                        </flux:button>
                        <flux:button variant="outline" href="{{ route('borrower.dashboard') }}">Batal</flux:button>
                    </div>
                </form>
            </div>
        </flux:card>
    </div>
@endsection
