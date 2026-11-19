@extends('layouts.print-card')

@section('title', 'Kartu Anggota')

@section('content')
    @if ($skippedCount > 0)
        <div class="no-print mb-3 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="alert">
            {{ $skippedCount }} anggota dilewati karena nomor induk kosong atau mengandung karakter yang tidak didukung barcode.
        </div>
    @endif

    <p class="no-print mb-3 text-center text-xs text-slate-500">
        Pratinjau {{ count($cards) }} kartu. Setiap lembar A4 memuat hingga 8 kartu (2 &times; 4).
        <span class="block md:hidden">Geser ke samping untuk melihat seluruh lembar.</span>
    </p>

    <div class="member-card-sheets">
        @foreach (array_chunk($cards, 8) as $sheet)
            <div class="member-card-sheet {{ count($sheet) === 1 ? 'member-card-sheet--single' : '' }}">
                @foreach ($sheet as $card)
                    <x-member-card :user="$card['user']" :barcode="$card['barcode']" />
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
