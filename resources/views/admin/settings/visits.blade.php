@extends('layouts.admin')

@section('title', 'Pengaturan Kunjungan')
@section('breadcrumb', 'Manajemen / Pengaturan / Kunjungan')

@section('content')
    <x-page-header
        title="Pengaturan Kunjungan"
        subtitle="Atur apakah pengunjung dapat mencatat kunjungan sendiri melalui kiosk publik."
    />

    @include('admin.settings._nav')

    <x-form-errors />

    <div class="grid gap-3 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <form method="POST" action="{{ route('admin.settings.visits.update') }}" data-loading>
                @csrf
                @method('PUT')

                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Kiosk Kunjungan Mandiri</flux:heading>
                    </div>
                    <div class="p-5">
                        <flux:switch
                            id="visit_kiosk_enabled"
                            name="visit_kiosk_enabled"
                            value="1"
                            :checked="(bool) old('visit_kiosk_enabled', $enabled)"
                            label="Aktifkan kiosk kunjungan mandiri"
                            description="Saat aktif, pengunjung dapat membuka halaman kiosk tanpa login, memindai barcode kartu anggota atau mengetik nomor induk, lalu melihat konfirmasi nama, kelas/unit, dan waktu kunjungan. Kunjungan tercatat sebagai Mandiri."
                        />
                        <flux:error name="visit_kiosk_enabled" />

                        <flux:callout icon="information-circle" class="mt-4">
                            <flux:callout.heading>Hanya guru dan siswa aktif yang dapat mencatat kunjungan.</flux:callout.heading>
                            <flux:callout.text>
                                Scan berulang dalam 30 menit dianggap kunjungan yang sama. Akun nonaktif dan
                                akun admin akan ditolak. Halaman kiosk tidak diindeks mesin pencari dan tidak disimpan di cache.
                            </flux:callout.text>
                        </flux:callout>
                    </div>
                    <div class="flex flex-col justify-between gap-3 border-t border-zinc-100 px-5 py-4 sm:flex-row sm:items-center dark:border-white/10">
                        <flux:text>Perubahan langsung berlaku pada tautan beranda dan akses halaman kiosk.</flux:text>
                        <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                            Simpan Pengaturan
                        </flux:button>
                    </div>
                </flux:card>
            </form>
        </div>

        <flux:card class="p-0!">
            <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Status Kiosk</flux:heading>
            </div>
            <div class="p-5">
                <div class="mb-3 flex items-center gap-2">
                    <x-status-badge
                        :label="$enabled ? 'Aktif' : 'Nonaktif'"
                        :variant="$enabled ? 'success' : 'secondary'"
                        :icon="$enabled ? 'check-circle' : 'pause-circle'"
                    />
                </div>

                <div class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">
                    Alamat halaman kiosk:
                    <code class="mt-1 block break-all rounded bg-zinc-100 px-2 py-1 text-xs text-zinc-700 dark:bg-white/10 dark:text-zinc-200">{{ route('visits.kiosk') }}</code>
                </div>

                @if ($enabled)
                    <flux:button variant="outline" size="sm" icon="arrow-top-right-on-square" href="{{ route('visits.kiosk') }}" target="_blank" rel="noopener">
                        Buka Halaman Kiosk
                    </flux:button>
                @else
                    <flux:text>Aktifkan kiosk untuk membuka halaman dan menampilkan tautan di beranda.</flux:text>
                @endif

                <flux:separator class="my-4" />

                @if ($setting)
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        Terakhir diperbarui
                        {{ $setting->updated_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}
                        @if ($setting->editor)
                            oleh {{ $setting->editor->name }}
                        @endif
                    </div>
                @else
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Masih menggunakan nilai awal (aktif).</div>
                @endif
            </div>
        </flux:card>
    </div>
@endsection
