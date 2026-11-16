@extends('layouts.print-card')

@section('title', 'Kartu Anggota')

@section('content')
    @if ($skippedCount > 0)
        <div class="alert alert-warning no-print" role="alert">
            {{ $skippedCount }} anggota dilewati karena nomor induk kosong atau mengandung karakter yang tidak didukung barcode.
        </div>
    @endif

    <p class="small text-muted text-center no-print mb-3">
        Pratinjau {{ count($cards) }} kartu. Setiap lembar A4 memuat hingga 8 kartu (2 &times; 4).
        <span class="d-md-none d-block">Geser ke samping untuk melihat seluruh lembar.</span>
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
