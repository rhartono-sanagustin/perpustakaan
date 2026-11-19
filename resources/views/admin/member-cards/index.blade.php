@extends('layouts.admin')

@section('title', 'Kartu Anggota')
@section('breadcrumb', 'Manajemen / Kartu Anggota')

@section('content')
    <x-page-header
        title="Kartu Anggota"
        subtitle="Cetak kartu anggota guru dan siswa beserta barcode nomor induk untuk pemindaian kunjungan."
    >
        <x-slot:actions>
            @if ($users->total() > 0)
                <flux:button variant="primary" icon="printer" href="{{ route('admin.member-cards.print', request()->query()) }}" target="_blank" rel="noopener">
                    Cetak Massal
                </flux:button>
            @endif
            <flux:button variant="outline" icon="users" href="{{ route('admin.users.index') }}">
                Kelola Pengguna
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <flux:callout icon="information-circle" class="mb-4">
        <flux:callout.text>
            Kartu hanya dapat dicetak untuk akun guru atau siswa yang aktif dan memiliki nomor induk.
            Setiap lembar A4 memuat hingga 8 kartu (2 &times; 4).
        </flux:callout.text>
    </flux:callout>

    <flux:card class="mb-4">
        <form method="GET" action="{{ route('admin.member-cards.index') }}" class="grid items-end gap-3 md:grid-cols-2 xl:grid-cols-12">
            <div class="xl:col-span-4">
                <flux:field>
                    <flux:label for="q">Cari anggota</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Nama, nomor induk, atau email" />
                </flux:field>
            </div>

            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label for="role">Peran</flux:label>
                    <flux:select id="role" name="role">
                        <option value="">Semua peran</option>
                        @foreach ($roles as $roleOption)
                            <option value="{{ $roleOption->value }}" @selected($role === $roleOption)>{{ $roleOption->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="xl:col-span-3">
                <flux:field>
                    <flux:label for="kelas">Kelas / Unit kerja</flux:label>
                    <flux:input type="text" id="kelas" name="kelas" value="{{ $kelas }}" list="kelas-options" placeholder="Contoh: XI IPA 1" />
                </flux:field>
                <datalist id="kelas-options">
                    @foreach ($classes->merge($units)->unique() as $option)
                        <option value="{{ $option }}"></option>
                    @endforeach
                </datalist>
            </div>

            <div class="flex gap-2 xl:col-span-3">
                <flux:button type="submit" variant="primary" class="flex-1">Terapkan</flux:button>
                @if ($search !== '' || $role !== null || $kelas !== '')
                    <flux:button variant="outline" href="{{ route('admin.member-cards.index') }}" icon="arrow-path" aria-label="Reset filter" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Anggota yang Dapat Dicetak</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $users->total() }} anggota</flux:badge>
        </div>

        @if ($users->isEmpty())
            <x-empty-state icon="person-vcard" title="Tidak ada anggota" message="Tidak ada akun guru atau siswa aktif yang sesuai dengan filter saat ini.">
                <flux:button variant="outline" size="sm" href="{{ route('admin.member-cards.index') }}">Reset Filter</flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Nomor Induk</th>
                            <th scope="col">Peran</th>
                            <th scope="col">Kelas / Unit</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $user->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email ?? 'Email belum diisi' }}</div>
                                </td>
                                <td>{{ $user->nomor_induk }}</td>
                                <td>
                                    <flux:badge color="zinc" size="sm">{{ $user->roleLabel() }}</flux:badge>
                                </td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->kelas ?? $user->unit_kerja ?? '-' }}</td>
                                <td class="text-end">
                                    <flux:button size="sm" variant="outline" icon="printer" href="{{ route('admin.users.card', $user) }}" target="_blank" rel="noopener">
                                        Cetak
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $users->firstItem() }}&ndash;{{ $users->lastItem() }} dari {{ $users->total() }} anggota</span>
                {{ $users->links() }}
            </div>
        @endif
    </flux:card>
@endsection
