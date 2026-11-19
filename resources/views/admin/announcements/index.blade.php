@extends('layouts.admin')

@section('title', 'Pengumuman')
@section('breadcrumb', 'Manajemen / Pengumuman')

@section('content')
    <x-page-header
        title="Pengumuman"
        subtitle="Kelola informasi yang tampil di dashboard guru dan siswa."
    >
        <x-slot:actions>
            <flux:button variant="primary" icon="megaphone" href="{{ route('admin.announcements.create') }}">
                Tambah Pengumuman
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-2 gap-2">
        <flux:card class="py-3!">
            <div class="text-2xl font-bold">{{ $activeCount }}</div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Pengumuman aktif</div>
        </flux:card>
        <flux:card class="py-3!">
            <div class="text-2xl font-bold">{{ $draftCount }}</div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">Draf / nonaktif</div>
        </flux:card>
    </div>

    <flux:card class="mb-4">
        <form method="GET" action="{{ route('admin.announcements.index') }}" class="grid items-end gap-3 md:grid-cols-2 xl:grid-cols-12">
            <div class="xl:col-span-6">
                <flux:field>
                    <flux:label for="q">Cari pengumuman</flux:label>
                    <flux:input type="search" id="q" name="q" value="{{ $search }}" icon="magnifying-glass" placeholder="Judul atau isi pengumuman" />
                </flux:field>
            </div>

            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label for="status">Status</flux:label>
                    <flux:select id="status" name="status">
                        <option value="">Semua status</option>
                        <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                        <option value="draf" @selected($status === 'draf')>Draf / nonaktif</option>
                    </flux:select>
                </flux:field>
            </div>

            <div class="xl:col-span-2">
                <flux:field>
                    <flux:label for="audience">Audiens</flux:label>
                    <flux:select id="audience" name="audience">
                        <option value="">Semua audiens</option>
                        @foreach ($audiences as $audienceOption)
                            <option value="{{ $audienceOption->value }}" @selected($audience === $audienceOption)>{{ $audienceOption->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex gap-2 xl:col-span-2">
                <flux:button type="submit" variant="primary" class="flex-1">Terapkan</flux:button>
                @if ($search !== '' || $status !== '' || $audience !== null)
                    <flux:button variant="outline" href="{{ route('admin.announcements.index') }}" icon="arrow-path" aria-label="Reset filter" />
                @endif
            </div>
        </form>
    </flux:card>

    <flux:card class="p-0!">
        <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Daftar Pengumuman</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $announcements->total() }} pengumuman</flux:badge>
        </div>

        @if ($announcements->isEmpty())
            <x-empty-state icon="megaphone" title="Belum ada pengumuman" message="Pengumuman yang dibuat akan tampil di dashboard guru dan siswa.">
                <flux:button variant="primary" size="sm" href="{{ route('admin.announcements.create') }}">Tambah Pengumuman</flux:button>
            </x-empty-state>
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Judul / Isi</th>
                            <th scope="col">Audiens</th>
                            <th scope="col">Status</th>
                            <th scope="col">Terbit</th>
                            <th scope="col">Pembuat</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($announcements as $announcement)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $announcement->title }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ \Illuminate\Support\Str::limit($announcement->body, 120) }}</div>
                                </td>
                                <td>
                                    <x-status-badge :label="$announcement->audience->label()" :variant="$announcement->audience->badgeVariant()" />
                                </td>
                                <td>
                                    @if (! $announcement->is_active)
                                        <x-status-badge label="Draf" variant="secondary" />
                                    @elseif ($announcement->published_at?->isFuture())
                                        <x-status-badge label="Terjadwal" variant="info" />
                                    @else
                                        <x-status-badge label="Aktif" variant="success" />
                                    @endif
                                </td>
                                <td class="text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400">
                                    {{ $announcement->published_at?->locale('id')->translatedFormat('d M Y, H:i') ?? 'Belum dijadwalkan' }}
                                </td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ $announcement->creator?->name ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="flex justify-end gap-1">
                                        <flux:button size="sm" variant="outline" href="{{ route('admin.announcements.edit', $announcement) }}" icon="pencil" title="Edit pengumuman" aria-label="Edit {{ $announcement->title }}" />

                                        <form method="POST" action="{{ route('admin.announcements.toggle', $announcement) }}" data-confirm="{{ $announcement->is_active ? 'Nonaktifkan' : 'Aktifkan' }} pengumuman {{ $announcement->title }}?">
                                            @csrf
                                            <flux:button type="submit" size="sm" variant="outline" :icon="$announcement->is_active ? 'pause-circle' : 'play-circle'" title="{{ $announcement->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" aria-label="{{ $announcement->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $announcement->title }}" />
                                        </form>

                                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" data-confirm="Hapus pengumuman {{ $announcement->title }}? Tindakan ini tidak dapat dibatalkan.">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" variant="danger" icon="trash" title="Hapus pengumuman" aria-label="Hapus {{ $announcement->title }}" />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-white/10">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Menampilkan {{ $announcements->firstItem() }}&ndash;{{ $announcements->lastItem() }} dari {{ $announcements->total() }} pengumuman</span>
                {{ $announcements->links() }}
            </div>
        @endif
    </flux:card>
@endsection
