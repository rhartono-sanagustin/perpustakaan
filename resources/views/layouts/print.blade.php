<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cetak Laporan') &mdash; {{ config('library.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset(config('library.logo')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
    </style>
</head>
<body class="print-page">
    <div class="print-toolbar no-print">
        <div>
            <div class="fw-semibold">Pratinjau Cetak</div>
            <div class="small text-muted">Gunakan dialog cetak browser untuk menyimpan sebagai PDF atau mencetak.</div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" data-print>
                <i class="bi bi-printer me-1" aria-hidden="true"></i>Cetak
            </button>
            <button type="button" class="btn btn-outline-secondary" data-close-print>
                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Tutup
            </button>
        </div>
    </div>

    <main class="print-sheet">
        @yield('content')
    </main>
</body>
</html>
