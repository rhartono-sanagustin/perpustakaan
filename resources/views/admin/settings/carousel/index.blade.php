@extends('layouts.admin')

@section('title', 'Carousel Beranda')
@section('breadcrumb', 'Manajemen / Pengaturan / Carousel Beranda')

@section('content')
    <x-page-header
        title="Carousel Beranda"
        subtitle="Kelola gambar yang tampil pada carousel di landing page."
    >
        <x-slot:actions>
            <flux:button variant="primary" icon="plus" href="{{ route('admin.settings.carousel.create') }}">
                Tambah Slide
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    @include('admin.settings._nav')

    <flux:callout icon="information-circle" class="mb-4">
        <flux:callout.heading>{{ $activeCount }} dari {{ $maxActive }} slide aktif</flux:callout.heading>
        <flux:callout.text>Slide nonaktif tetap tersimpan, tetapi tidak tampil di beranda. Urutan slide dapat diubah dengan tombol naik/turun.</flux:callout.text>
    </flux:callout>

    @if ($slides->isEmpty())
        <flux:card>
            <x-empty-state
                icon="images"
                title="Belum ada slide carousel"
                message="Tambahkan gambar untuk ditampilkan di landing page."
            >
                <flux:button variant="primary" icon="plus" href="{{ route('admin.settings.carousel.create') }}">Tambah Slide</flux:button>
            </x-empty-state>
        </flux:card>
    @else
        <flux:card class="p-0!">
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col" class="w-20 text-center">Urutan</th>
                            <th scope="col" class="w-36">Gambar</th>
                            <th scope="col">Informasi Slide</th>
                            <th scope="col" class="w-28">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($slides as $slide)
                            <tr>
                                <td class="text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="font-semibold">{{ $loop->iteration }}</span>
                                        <div class="inline-flex overflow-hidden rounded-md border border-zinc-200 dark:border-zinc-700">
                                            <form method="POST" action="{{ route('admin.settings.carousel.move', $slide) }}">
                                                @csrf
                                                <input type="hidden" name="direction" value="up">
                                                <flux:button type="submit" size="xs" variant="subtle" icon="arrow-up" aria-label="Naikkan slide {{ $loop->iteration }}" :disabled="$loop->first" class="rounded-none border-0" />
                                            </form>
                                            <form method="POST" action="{{ route('admin.settings.carousel.move', $slide) }}">
                                                @csrf
                                                <input type="hidden" name="direction" value="down">
                                                <flux:button type="submit" size="xs" variant="subtle" icon="arrow-down" aria-label="Turunkan slide {{ $loop->iteration }}" :disabled="$loop->last" class="rounded-none border-0" />
                                            </form>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <img
                                        src="{{ $slide->imageUrl() }}"
                                        alt=""
                                        class="h-[73px] w-[130px] rounded-md border border-zinc-200 object-cover dark:border-zinc-700"
                                    >
                                </td>
                                <td>
                                    <div class="font-semibold">{{ $slide->title ?: 'Tanpa judul' }}</div>
                                    @if ($slide->caption)
                                        <div class="max-w-md truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $slide->caption }}</div>
                                    @endif
                                    @if ($slide->link_url)
                                        <div class="mt-1 flex max-w-md items-center gap-1 truncate text-xs text-zinc-500 dark:text-zinc-400">
                                            <flux:icon.link class="size-3.5 shrink-0" />{{ $slide->link_url }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($slide->is_active)
                                        <flux:badge color="green" size="sm">Aktif</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">Nonaktif</flux:badge>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-wrap justify-end gap-1">
                                        <flux:button size="sm" variant="outline" icon="pencil" href="{{ route('admin.settings.carousel.edit', $slide) }}">
                                            Edit
                                        </flux:button>

                                        <form method="POST" action="{{ route('admin.settings.carousel.toggle', $slide) }}">
                                            @csrf
                                            <flux:button type="submit" size="sm" variant="outline" :icon="$slide->is_active ? 'pause-circle' : 'play-circle'">
                                                {{ $slide->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </flux:button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.settings.carousel.destroy', $slide) }}" data-confirm="Hapus slide &quot;{{ $slide->title ?: 'Tanpa judul' }}&quot;? Gambar juga akan dihapus.">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" variant="danger" icon="trash">
                                                Hapus
                                            </flux:button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </flux:card>
    @endif
@endsection
