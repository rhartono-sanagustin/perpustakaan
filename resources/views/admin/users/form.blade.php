@extends('layouts.admin')

@section('title', $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('breadcrumb', 'Pengguna / Form')

@section('content')
    <x-page-header
        :title="$user->exists ? 'Edit Pengguna' : 'Tambah Pengguna'"
        :subtitle="$user->exists ? 'Perbarui data akun dan hak akses pengguna.' : 'Buat akun baru untuk admin, guru, atau siswa.'"
    >
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.users.index') }}">
                Kembali
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Periksa kembali data berikut:" />

    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" data-loading>
        @csrf
        @method($user->exists ? 'PUT' : 'POST')

        <div class="grid gap-4 xl:grid-cols-3">
            <flux:card class="p-0! xl:col-span-2">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Data Akun</flux:heading>
                </div>
                <div class="grid gap-5 p-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <flux:field>
                            <flux:label for="name">Nama lengkap <span class="text-rose-600">*</span></flux:label>
                            <flux:input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="255" required autofocus />
                            <flux:error name="name" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label for="nomor_induk">Nomor induk <span class="text-rose-600">*</span></flux:label>
                        <flux:input type="text" id="nomor_induk" name="nomor_induk" value="{{ old('nomor_induk', $user->nomor_induk) }}" maxlength="50" required />
                        <flux:description>NIS untuk siswa, NIP untuk guru, atau kode admin.</flux:description>
                        <flux:error name="nomor_induk" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="email">Email</flux:label>
                        <flux:input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="role">Peran <span class="text-rose-600">*</span></flux:label>
                        <flux:select id="role" name="role" required>
                            @foreach ($roles as $roleOption)
                                <option value="{{ $roleOption->value }}" @selected(old('role', $user->role?->value) === $roleOption->value)>{{ $roleOption->label() }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="role" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="status">Status akun <span class="text-rose-600">*</span></flux:label>
                        <flux:select id="status" name="status" required>
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption->value }}" @selected(old('status', $user->status?->value) === $statusOption->value)>{{ $statusOption->label() }}</option>
                            @endforeach
                        </flux:select>
                        <flux:description>Akun nonaktif tidak dapat login atau mengajukan peminjaman.</flux:description>
                        <flux:error name="status" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="kelas">Kelas</flux:label>
                        <flux:input type="text" id="kelas" name="kelas" value="{{ old('kelas', $user->kelas) }}" maxlength="50" placeholder="Contoh: XI IPA 1" />
                        <flux:description>Isi untuk siswa.</flux:description>
                        <flux:error name="kelas" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="unit_kerja">Unit kerja</flux:label>
                        <flux:input type="text" id="unit_kerja" name="unit_kerja" value="{{ old('unit_kerja', $user->unit_kerja) }}" maxlength="100" placeholder="Contoh: Matematika" />
                        <flux:description>Isi untuk guru atau admin.</flux:description>
                        <flux:error name="unit_kerja" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="no_telepon">Nomor telepon</flux:label>
                        <flux:input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" maxlength="30" placeholder="08xxxxxxxxxx" />
                        <flux:error name="no_telepon" />
                    </flux:field>

                    @unless ($user->exists)
                        <flux:field>
                            <flux:label for="password">Kata sandi <span class="text-rose-600">*</span></flux:label>
                            <div class="relative">
                                <flux:input type="password" id="password" name="password" minlength="8" required autocomplete="new-password" class="pr-10!" />
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center rounded-r-lg text-zinc-500 hover:text-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-accent)] dark:text-zinc-400 dark:hover:text-white"
                                    data-toggle-password="#password"
                                    aria-label="Tampilkan kata sandi"
                                >
                                    <flux:icon.eye variant="mini" class="size-4" data-password-show />
                                    <flux:icon.eye-slash variant="mini" class="hidden size-4" data-password-hide />
                                </button>
                            </div>
                            <flux:description>Minimal 8 karakter.</flux:description>
                            <flux:error name="password" />
                        </flux:field>

                        <flux:field>
                            <flux:label for="password_confirmation">Ulangi kata sandi <span class="text-rose-600">*</span></flux:label>
                            <div class="relative">
                                <flux:input type="password" id="password_confirmation" name="password_confirmation" minlength="8" required autocomplete="new-password" class="pr-10!" />
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center rounded-r-lg text-zinc-500 hover:text-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-accent)] dark:text-zinc-400 dark:hover:text-white"
                                    data-toggle-password="#password_confirmation"
                                    aria-label="Tampilkan kata sandi"
                                >
                                    <flux:icon.eye variant="mini" class="size-4" data-password-show />
                                    <flux:icon.eye-slash variant="mini" class="hidden size-4" data-password-hide />
                                </button>
                            </div>
                        </flux:field>
                    @endunless
                </div>
            </flux:card>

            <div class="space-y-4">
                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Ringkasan</flux:heading>
                    </div>
                    <div class="p-5">
                        <dl class="grid grid-cols-3 gap-x-3 gap-y-2 text-sm">
                            <dt class="text-zinc-500 dark:text-zinc-400">Peran</dt>
                            <dd class="col-span-2">{{ $user->exists ? $user->roleLabel() : 'Siswa' }}</dd>
                            <dt class="text-zinc-500 dark:text-zinc-400">Status</dt>
                            <dd class="col-span-2">{{ $user->exists ? $user->statusLabel() : 'Aktif' }}</dd>
                        </dl>

                        @if ($user->exists && $user->isBorrower() && $user->isActive())
                            <flux:button
                                variant="outline"
                                icon="identification"
                                href="{{ route('admin.users.card', $user) }}"
                                target="_blank"
                                rel="noopener"
                                class="mt-4 w-full"
                            >
                                Cetak Kartu Anggota
                            </flux:button>
                        @endif

                        <div class="mt-4 flex flex-col gap-2">
                            <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                                Simpan
                            </flux:button>
                            <flux:button variant="outline" href="{{ route('admin.users.index') }}">
                                Batal
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            </div>
        </div>
    </form>

    @if ($user->exists && auth()->user()->can('resetPassword', $user))
        <flux:card id="reset-password" class="mt-4 border-amber-300! dark:border-amber-500/40!">
            <div class="mb-4">
                <flux:heading size="lg" level="2">Reset Kata Sandi</flux:heading>
                <flux:text class="mt-1">
                    Masukkan kata sandi baru untuk {{ $user->name }}. Kata sandi tidak akan ditampilkan setelah disimpan.
                </flux:text>
            </div>

            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" data-loading>
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <flux:field>
                        <flux:label for="new_password">Kata sandi baru <span class="text-rose-600">*</span></flux:label>
                        <div class="relative">
                            <flux:input type="password" id="new_password" name="password" minlength="8" required autocomplete="new-password" class="pr-10!" />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 flex w-10 items-center justify-center rounded-r-lg text-zinc-500 hover:text-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-accent)] dark:text-zinc-400 dark:hover:text-white"
                                data-toggle-password="#new_password"
                                aria-label="Tampilkan kata sandi"
                            >
                                <flux:icon.eye variant="mini" class="size-4" data-password-show />
                                <flux:icon.eye-slash variant="mini" class="hidden size-4" data-password-hide />
                            </button>
                        </div>
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="new_password_confirmation">Ulangi kata sandi baru <span class="text-rose-600">*</span></flux:label>
                        <div class="relative">
                            <flux:input type="password" id="new_password_confirmation" name="password_confirmation" minlength="8" required autocomplete="new-password" class="pr-10!" />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 flex w-10 items-center justify-center rounded-r-lg text-zinc-500 hover:text-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-accent)] dark:text-zinc-400 dark:hover:text-white"
                                data-toggle-password="#new_password_confirmation"
                                aria-label="Tampilkan kata sandi"
                            >
                                <flux:icon.eye variant="mini" class="size-4" data-password-show />
                                <flux:icon.eye-slash variant="mini" class="hidden size-4" data-password-hide />
                            </button>
                        </div>
                    </flux:field>
                </div>

                <flux:button type="submit" variant="primary" icon="key" class="mt-4" data-loading-text="Mereset...">
                    Reset Kata Sandi
                </flux:button>
            </form>
        </flux:card>
    @endif
@endsection
