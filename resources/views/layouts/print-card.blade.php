@inject('identity', 'App\Services\LibraryIdentity')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kartu Anggota') &mdash; {{ $identity->libraryName() }}</title>
    <link rel="icon" href="{{ $identity->logoUrl() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
    </style>
</head>
<body class="print-page member-card-page">
    <div class="print-toolbar no-print">
        <div>
            <div class="font-semibold">Pratinjau Kartu Anggota</div>
            <div class="text-xs text-slate-500">Cetak pada ukuran 100% agar barcode tetap terbaca.</div>
        </div>
        <div class="flex gap-2">
            <flux:button type="button" variant="primary" icon="printer" data-print>
                Cetak
            </flux:button>
            <flux:button type="button" variant="outline" icon="x-mark" data-close-print>
                Tutup
            </flux:button>
        </div>
    </div>

    <main class="member-card-sheet-wrap" data-scrollable-region>
        @yield('content')
    </main>
</body>
</html>
