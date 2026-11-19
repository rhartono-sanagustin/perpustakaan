@inject('identity', 'App\Services\LibraryIdentity')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cetak Laporan') &mdash; {{ $identity->libraryName() }}</title>
    <link rel="icon" href="{{ $identity->logoUrl() }}">
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
            <div class="font-semibold">Pratinjau Cetak</div>
            <div class="text-xs text-slate-500">Gunakan dialog cetak browser untuk menyimpan sebagai PDF atau mencetak.</div>
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

    <main class="print-sheet">
        @yield('content')
    </main>
</body>
</html>
