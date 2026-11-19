@extends('layouts.admin')

@section('title', 'Kunjungan')
@section('breadcrumb', 'Transaksi / Kunjungan')

@section('content')
    <x-page-header
        title="Kunjungan Perpustakaan"
        subtitle="Scan kartu anggota untuk mencatat kunjungan guru dan siswa."
    >
        <x-slot:actions>
            <flux:button variant="outline" icon="identification" href="{{ route('admin.member-cards.index') }}">
                Kartu Anggota
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-2 gap-2 md:grid-cols-4">
        @foreach ([
            ['value' => $counts['total'], 'label' => 'Kunjungan hari ini'],
            ['value' => $counts['unique'], 'label' => 'Pengunjung unik'],
            ['value' => $counts['guru'], 'label' => 'Kunjungan guru'],
            ['value' => $counts['siswa'], 'label' => 'Kunjungan siswa'],
        ] as $stat)
            <flux:card class="py-3!">
                <div class="text-2xl font-bold">{{ $stat['value'] }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $stat['label'] }}</div>
            </flux:card>
        @endforeach
    </div>

    <flux:card class="mb-4 p-0!">
        <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Scan Kartu Anggota</flux:heading>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('admin.visits.store') }}" class="grid items-end gap-3 lg:grid-cols-4" data-loading>
                @csrf

                <div class="lg:col-span-3">
                    <flux:field>
                        <flux:label for="nomor_induk">Nomor induk</flux:label>
                        <flux:input
                            type="text"
                            id="nomor_induk"
                            name="nomor_induk"
                            value="{{ old('nomor_induk') }}"
                            icon="qr-code"
                            size="lg"
                            placeholder="Scan barcode atau ketik nomor induk"
                            autocomplete="off"
                            maxlength="50"
                            autofocus
                            required
                        />
                        <flux:description>
                            Arahkan kursor ke kolom ini lalu scan barcode pada kartu anggota. Scanner USB mengirim Enter otomatis.
                        </flux:description>
                        <flux:error name="nomor_induk" />
                    </flux:field>
                </div>

                <flux:button type="submit" variant="primary" icon="check-circle" class="h-10 w-full" data-loading-text="Mencatat...">
                    Catat Kunjungan
                </flux:button>
            </form>
        </div>
    </flux:card>

    @if ($member)
        <flux:card class="mb-4 border-s-4! border-s-[var(--color-accent)]!">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 pb-4 dark:border-white/10">
                <flux:heading size="lg" level="2">Anggota yang Baru Dipindai</flux:heading>
                <flux:badge color="sky">{{ $member->roleLabel() }}</flux:badge>
            </div>
            <div class="grid gap-5 lg:grid-cols-3">
                <div>
                    <div class="text-lg font-semibold">{{ $member->name }}</div>
                    <div class="text-zinc-500 dark:text-zinc-400">{{ $member->nomor_induk }}</div>
                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $member->isGuru() ? ($member->unit_kerja ?? 'Unit kerja belum diisi') : ($member->kelas ?? 'Kelas belum diisi') }}
                    </div>
                    <div class="mt-2">
                        <x-status-badge :label="$member->statusLabel()" :variant="$member->status->badgeVariant()" />
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="mb-3 grid grid-cols-2 gap-2">
                        <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                            <div class="text-lg font-bold">{{ $member->loans->count() }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Pinjaman aktif</div>
                        </div>
                        <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                            <div class="text-lg font-bold text-rose-600 dark:text-rose-400">{{ $member->loans->filter(fn ($loan) => $loan->isOverdue())->count() }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Terlambat</div>
                        </div>
                    </div>

                    @if ($member->loans->isEmpty())
                        <flux:text>Tidak ada pinjaman aktif.</flux:text>
                    @else
                        <div class="overflow-x-auto" data-scrollable-region>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Buku</th>
                                        <th scope="col">Jatuh Tempo</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($member->loans as $loan)
                                        <tr>
                                            <td>
                                                <div>{{ $loan->copy->book->title }}</div>
                                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->copy->inventory_code }}</div>
                                            </td>
                                            <td class="text-xs">{{ $loan->due_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}</td>
                                            <td>
                                                <x-status-badge :label="$loan->statusLabel()" :variant="$loan->isOverdue() ? 'danger' : 'info'" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="mt-4 flex flex-wrap gap-2">
                        <flux:button size="sm" variant="outline" icon="printer" href="{{ route('admin.users.card', $member) }}" target="_blank" rel="noopener">
                            Cetak Kartu
                        </flux:button>
                        <flux:button size="sm" variant="outline" icon="arrows-right-left" href="{{ route('admin.loans.index', ['q' => $member->nomor_induk]) }}">
                            Lihat Pinjaman
                        </flux:button>
                    </div>
                </div>
            </div>
        </flux:card>
    @endif

    <flux:card class="p-0!">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <flux:heading size="lg" level="2">Kunjungan Hari Ini</flux:heading>
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('admin.visits.index') }}" class="flex items-center gap-2">
                    <label for="sumber" class="text-xs text-zinc-500 dark:text-zinc-400">Sumber</label>
                    <div class="w-40" data-auto-submit>
                        <flux:select id="sumber" name="sumber" size="sm">
                            <option value="">Semua sumber</option>
                            @foreach (\App\Enums\VisitSource::cases() as $source)
                                <option value="{{ $source->value }}" @selected($sourceFilter === $source->value)>
                                    {{ $source->label() }}
                                </option>
                            @endforeach
                        </flux:select>
                    </div>
                </form>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>

        @if ($visits->isEmpty())
            <x-empty-state
                icon="person-check"
                title="Belum ada kunjungan"
                message="Kunjungan yang tercatat hari ini akan tampil di sini."
            />
        @else
            <div class="overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Waktu</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Nomor Induk</th>
                            <th scope="col">Peran</th>
                            <th scope="col">Sumber</th>
                            <th scope="col">Kelas / Unit</th>
                            <th scope="col">Dicatat oleh</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visits as $visit)
                            <tr>
                                <td class="font-semibold">{{ $visit->visited_at->format('H:i') }}</td>
                                <td>{{ $visit->user->name }}</td>
                                <td>{{ $visit->user->nomor_induk }}</td>
                                <td>
                                    <flux:badge color="zinc" size="sm">{{ $visit->user->roleLabel() }}</flux:badge>
                                </td>
                                <td>
                                    <x-status-badge :label="$visit->source->label()" :variant="$visit->source->badgeVariant()" />
                                </td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ $visit->user->kelas ?? $visit->user->unit_kerja ?? '-' }}</td>
                                <td class="text-xs text-zinc-500 dark:text-zinc-400">{{ $visit->recorder?->name ?? '-' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.visits.destroy', $visit) }}" data-confirm="Batalkan kunjungan {{ $visit->user->name }} pukul {{ $visit->visited_at->format('H:i') }}?">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" size="sm" variant="danger" icon="x-mark" title="Batalkan kunjungan" aria-label="Batalkan kunjungan {{ $visit->user->name }}" />
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </flux:card>
@endsection
