@extends('layouts.kiosk')
@inject('identity', 'App\Services\LibraryIdentity')

@section('title', 'Kiosk Kunjungan')

@section('content')
    @php
        $resultStyles = [
            'success' => 'border-emerald-600 bg-emerald-50 text-emerald-900',
            'danger' => 'border-rose-600 bg-rose-50 text-rose-900',
            'warning' => 'border-amber-500 bg-amber-50 text-amber-900',
            'info' => 'border-sky-600 bg-sky-50 text-sky-900',
        ];
    @endphp

    <div class="kiosk-card">
        @if ($errors->any())
            <div class="kiosk-result rounded-lg border border-rose-600 border-l-4 bg-rose-50 px-4 py-3 text-rose-900" role="alert">
                <div class="flex items-start gap-3">
                    <flux:icon.exclamation-circle class="kiosk-result-icon size-8 shrink-0" />
                    <div>
                        <div class="kiosk-result-title">Data Belum Lengkap</div>
                        <div class="mt-1 text-sm">{{ $errors->first('nomor_induk') }}</div>
                    </div>
                </div>
            </div>
        @endif

        @if ($visitResult)
            <div
                class="kiosk-result rounded-lg border border-l-4 px-4 py-3 {{ $resultStyles[$visitResult['variant']] ?? $resultStyles['info'] }}"
                role="{{ $visitResult['variant'] === 'danger' ? 'alert' : 'status' }}"
                data-kiosk-result
            >
                <div class="flex items-start gap-3">
                    <x-icon :name="$visitResult['icon']" class="kiosk-result-icon size-8 shrink-0" />
                    <div class="min-w-0">
                        <div class="kiosk-result-title">{{ $visitResult['title'] }}</div>

                        @if ($visitResult['name'])
                            <div class="kiosk-result-name">{{ $visitResult['name'] }}</div>
                            <div class="kiosk-result-meta">
                                {{ $visitResult['nomor_induk'] }}
                                @if ($visitResult['role'])
                                    <span aria-hidden="true">&middot;</span> {{ $visitResult['role'] }}
                                @endif
                                @if ($visitResult['unit'])
                                    <span aria-hidden="true">&middot;</span> {{ $visitResult['unit'] }}
                                @endif
                                @if ($visitResult['time'])
                                    <span aria-hidden="true">&middot;</span> Pukul {{ $visitResult['time'] }}
                                @endif
                            </div>
                        @endif

                        <div class="mt-2 text-sm">{{ $visitResult['message'] }}</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="kiosk-scan-card rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
            <div class="mb-6 text-center">
                <span class="kiosk-scan-icon" aria-hidden="true">
                    <flux:icon.qr-code class="size-10" />
                </span>
                <h1 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">Selamat Datang di {{ $identity->libraryName() }}</h1>
                <p class="mt-1 text-slate-500">
                    Scan kartu anggota atau ketik nomor induk untuk mencatat kunjungan.
                </p>
            </div>

            <form method="POST" action="{{ route('visits.kiosk.store') }}" data-loading>
                @csrf

                <flux:field>
                    <flux:label for="nomor_induk" class="text-slate-700">Nomor induk</flux:label>
                    <flux:input
                        type="text"
                        id="nomor_induk"
                        name="nomor_induk"
                        value="{{ old('nomor_induk') }}"
                        class="kiosk-input"
                        icon="qr-code"
                        placeholder="Contoh: NIS001"
                        autocomplete="off"
                        autocapitalize="off"
                        spellcheck="false"
                        maxlength="50"
                        autofocus
                        required
                        data-kiosk-input
                    />
                    <flux:description>Arahkan kursor ke kolom ini lalu scan barcode pada kartu anggota. Scanner USB mengirim Enter otomatis.</flux:description>
                    <flux:error name="nomor_induk" />
                </flux:field>

                <flux:button type="submit" variant="primary" icon="check-circle" class="mt-4 h-12 w-full text-base!" data-loading-text="Mencatat...">
                    Catat Kunjungan
                </flux:button>
            </form>

            <div class="mt-5 space-y-2 border-t border-slate-200 pt-4 text-xs text-slate-500">
                <div class="flex items-start gap-2">
                    <flux:icon.clock class="mt-0.5 size-4 shrink-0" />
                    <span>Kunjungan yang sama dalam 30 menit tidak dicatat dua kali.</span>
                </div>
                <div class="flex items-start gap-2">
                    <flux:icon.user class="mt-0.5 size-4 shrink-0" />
                    <span>Hanya kartu guru dan siswa aktif yang dapat digunakan.</span>
                </div>
            </div>
        </div>

        <p class="mt-4 text-center text-xs text-slate-500">
            Butuh bantuan? Hubungi petugas perpustakaan.
        </p>
    </div>
@endsection
