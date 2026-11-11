@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('breadcrumb', 'Manajemen / Pengaturan')

@section('content')
    <x-page-header
        title="Pengaturan Sirkulasi"
        subtitle="Atur kuota, durasi peminjaman, dan batas pengambilan buku."
    />

    <div class="alert alert-info d-flex align-items-start gap-2" role="status">
        <i class="bi bi-info-circle mt-1" aria-hidden="true"></i>
        <div>
            <div class="fw-semibold">Perubahan berlaku untuk transaksi baru</div>
            <div class="small">Pinjaman yang sudah berjalan tetap memakai durasi dan kuota yang tersimpan pada transaksi tersebut.</div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" data-loading>
        @csrf
        @method('PUT')

        @php($groups = collect($definitions)->groupBy('group'))

        <div class="row g-3">
            @foreach ($groups as $group => $items)
                <div class="col-12 col-xl-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h2 class="h6 mb-0">{{ $group }}</h2>
                        </div>
                        <div class="card-body">
                            @foreach ($items as $key => $definition)
                                @php($setting = $settings->get($key))
                                <div class="{{ $loop->last ? '' : 'mb-4' }}">
                                    <label for="{{ $key }}" class="form-label">{{ $definition['label'] }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input
                                            type="number"
                                            id="{{ $key }}"
                                            name="{{ $key }}"
                                            value="{{ old($key, $setting?->value ?? config('library.loan_defaults.'.$key)) }}"
                                            class="form-control @error($key) is-invalid @enderror"
                                            min="{{ $definition['min'] }}"
                                            max="{{ $definition['max'] }}"
                                            step="1"
                                            required
                                        >
                                        <span class="input-group-text">{{ $definition['suffix'] }}</span>
                                        @error($key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">{{ $definition['description'] }} Rentang {{ $definition['min'] }}&ndash;{{ $definition['max'] }} {{ $definition['suffix'] }}.</div>

                                    @if ($setting)
                                        <div class="small text-muted mt-1">
                                            Terakhir diperbarui {{ $setting->updated_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}
                                            @if ($setting->editor)
                                                oleh {{ $setting->editor->name }}
                                            @endif
                                        </div>
                                    @else
                                        <div class="small text-muted mt-1">Masih menggunakan nilai awal.</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mt-3">
            <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div class="small text-muted">
                    Pengaturan denda belum tersedia karena belum diterapkan pada MVP.
                </div>
                <button type="submit" class="btn btn-primary" data-loading-text="Menyimpan...">
                    <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
@endsection
