@extends('layouts.admin')

@section('title', 'Pengguna')
@section('breadcrumb', 'Manajemen / Pengguna')

@section('content')
    <x-page-header
        title="Pengguna"
        subtitle="Kelola akun admin, guru, dan siswa beserta status aksesnya."
    >
        <x-slot:actions>
            <flux:button variant="primary" icon="user-plus" href="{{ route('admin.users.create') }}">
                Tambah Pengguna
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-2 gap-2 md:grid-cols-4">
        @foreach ([
            ['value' => $counts['total'], 'label' => 'Total pengguna'],
            ['value' => $counts['aktif'], 'label' => 'Akun aktif'],
            ['value' => $counts['guru'], 'label' => 'Guru'],
            ['value' => $counts['siswa'], 'label' => 'Siswa'],
        ] as $stat)
            <flux:card class="py-3!">
                <div class="text-2xl font-bold">{{ $stat['value'] }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $stat['label'] }}</div>
            </flux:card>
        @endforeach
    </div>

    <flux:card class="mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid items-end gap-3 md:grid-cols-2 xl:grid-cols-12">
            <div class="xl:col-span-6">
                <flux:field>
                    <flux:label for="q">Cari pengguna</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Nama, email, nomor induk, kelas, atau unit kerja" />
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

            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label for="status">Status</flux:label>
                    <flux:select id="status" name="status">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption->value }}" @selected($status === $statusOption)>{{ $statusOption->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex gap-2 xl:col-span-2">
                <flux:button type="submit" variant="primary" class="flex-1">Terapkan</flux:button>
                @if ($search !== '' || $role !== null || $status !== null)
                    <flux:button variant="outline" href="{{ route('admin.users.index') }}" icon="arrow-path" aria-label="Reset filter" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Daftar Pengguna</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $users->total() }} akun</flux:badge>
        </div>

        @if ($users->isEmpty())
            <x-empty-state icon="people" title="Tidak ada pengguna" message="Tidak ada akun yang sesuai dengan filter saat ini.">
                <flux:button variant="outline" size="sm" href="{{ route('admin.users.index') }}">Reset Filter</flux:button>
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
                            <th scope="col">Status</th>
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
                                <td>
                                    <x-status-badge :label="$user->statusLabel()" :variant="$user->status->badgeVariant()" />
                                </td>
                                <td class="text-end">
                                    @if (auth()->user()->is($user))
                                        <flux:badge color="zinc" size="sm">Akun Anda</flux:badge>
                                    @else
                                        <div class="flex justify-end gap-1">
                                            @if ($user->isBorrower() && $user->isActive())
                                                <flux:button size="sm" variant="outline" href="{{ route('admin.users.card', $user) }}" target="_blank" rel="noopener" icon="identification" title="Cetak kartu anggota" aria-label="Cetak kartu {{ $user->name }}" />
                                            @endif

                                            @can('update', $user)
                                                <flux:button size="sm" variant="outline" href="{{ route('admin.users.edit', $user) }}" icon="pencil" title="Edit pengguna" aria-label="Edit {{ $user->name }}" />
                                            @endcan

                                            @can('toggleStatus', $user)
                                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" data-confirm="Ubah status akun {{ $user->name }} menjadi {{ $user->isActive() ? 'nonaktif' : 'aktif' }}?">
                                                    @csrf
                                                    <flux:button type="submit" size="sm" variant="outline" :icon="$user->isActive() ? 'user-minus' : 'check-circle'" title="{{ $user->isActive() ? 'Nonaktifkan akun' : 'Aktifkan akun' }}" aria-label="{{ $user->isActive() ? 'Nonaktifkan' : 'Aktifkan' }} {{ $user->name }}" />
                                                </form>
                                            @endcan

                                            @can('delete', $user)
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Hapus pengguna {{ $user->name }}? Akun dengan riwayat transaksi akan dinonaktifkan, bukan dihapus.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <flux:button type="submit" size="sm" variant="danger" icon="trash" title="Hapus pengguna" aria-label="Hapus {{ $user->name }}" />
                                                </form>
                                            @endcan
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $users->firstItem() }}&ndash;{{ $users->lastItem() }} dari {{ $users->total() }} akun</span>
                {{ $users->links() }}
            </div>
        @endif
    </flux:card>
@endsection
