@extends('layouts.admin')

@section('title', 'Detail Permintaan')
@section('breadcrumb', 'Sirkulasi / Permintaan / Detail')

@section('content')
    <x-page-header title="Detail Permintaan" :subtitle="'#'.$loanRequest->reference.' · '.$loanRequest->book->title">
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.requests.index') }}">
                Kembali ke Antrean
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Tindakan belum dapat diproses:" />

    <div class="grid gap-4 xl:grid-cols-3">
        <div class="space-y-4 xl:col-span-2">
            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Data Peminjam</flux:heading>
                </div>
                <div class="grid gap-4 p-5 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div class="sm:col-span-2 lg:col-span-1">
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Nama</div>
                        <div class="font-semibold">{{ $loanRequest->user->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Peran</div>
                        <div>{{ $loanRequest->user->roleLabel() }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Nomor induk</div>
                        <div>{{ $loanRequest->user->nomor_induk }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Kelas / unit</div>
                        <div>{{ $loanRequest->user->kelas ?? $loanRequest->user->unit_kerja ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Status akun</div>
                        <div>
                            <x-status-badge :label="$loanRequest->user->statusLabel()" :variant="$loanRequest->user->isActive() ? 'success' : 'secondary'" />
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Telepon</div>
                        <div>{{ $loanRequest->user->no_telepon ?? '-' }}</div>
                    </div>
                    <div class="sm:col-span-2">
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Email</div>
                        <div class="break-all">{{ $loanRequest->user->email ?? '-' }}</div>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Buku yang Diajukan</flux:heading>
                </div>
                <div class="p-5">
                    <div class="flex gap-3">
                        <img src="{{ $loanRequest->book->cover_url }}" alt="Sampul {{ $loanRequest->book->title }}" class="h-[100px] w-[72px] rounded-md object-cover">
                        <div class="min-w-0">
                            <flux:heading size="lg" level="3" class="text-base!">{{ $loanRequest->book->title }}</flux:heading>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loanRequest->book->authors_label ?: 'Penulis tidak diketahui' }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loanRequest->book->category?->name ?? 'Tanpa kategori' }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">ISBN: {{ $loanRequest->book->isbn ?? 'Tidak tersedia' }}</div>
                        </div>
                    </div>

                    <flux:separator class="my-4" />

                    <div class="grid grid-cols-2 gap-4 text-center sm:grid-cols-4">
                        <div>
                            <div class="text-lg font-bold">{{ $availableCopies->count() }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Siap diserahkan</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold">{{ $approvedHolds }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Ditahan pengajuan lain</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold">{{ $quota['used'] }} / {{ $quota['max'] }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Kuota peminjam</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold">{{ $quota['duration_days'] }} hari</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Durasi pinjam</div>
                        </div>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Riwayat Proses</flux:heading>
                </div>
                <dl class="grid gap-x-4 gap-y-2 p-5 text-sm sm:grid-cols-4">
                    <dt class="text-zinc-500 dark:text-zinc-400">Diajukan</dt>
                    <dd class="sm:col-span-3">{{ $loanRequest->requested_at->locale('id')->translatedFormat('d F Y, H:i') }}</dd>

                    <dt class="text-zinc-500 dark:text-zinc-400">Disetujui</dt>
                    <dd class="sm:col-span-3">{{ $loanRequest->approved_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</dd>

                    <dt class="text-zinc-500 dark:text-zinc-400">Batas pengambilan</dt>
                    <dd class="sm:col-span-3">
                        @if ($loanRequest->expires_at)
                            <span class="{{ $loanRequest->isExpired() ? 'text-rose-600 dark:text-rose-400' : '' }}">
                                {{ $loanRequest->expires_at->locale('id')->translatedFormat('d F Y, H:i') }}
                                @if ($loanRequest->isExpired())
                                    (kedaluwarsa)
                                @endif
                            </span>
                        @else
                            -
                        @endif
                    </dd>

                    <dt class="text-zinc-500 dark:text-zinc-400">Ditolak</dt>
                    <dd class="sm:col-span-3">{{ $loanRequest->rejected_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</dd>

                    <dt class="text-zinc-500 dark:text-zinc-400">Dibatalkan</dt>
                    <dd class="sm:col-span-3">{{ $loanRequest->cancelled_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</dd>

                    <dt class="text-zinc-500 dark:text-zinc-400">Diproses oleh</dt>
                    <dd class="sm:col-span-3">{{ $loanRequest->processor?->name ?? '-' }}</dd>

                    @if ($loanRequest->notes)
                        <dt class="text-zinc-500 dark:text-zinc-400">Catatan peminjam</dt>
                        <dd class="sm:col-span-3">{{ $loanRequest->notes }}</dd>
                    @endif

                    @if ($loanRequest->rejection_reason)
                        <dt class="text-zinc-500 dark:text-zinc-400">Alasan penolakan</dt>
                        <dd class="sm:col-span-3">{{ $loanRequest->rejection_reason }}</dd>
                    @endif
                </dl>
            </flux:card>

            @if ($loanRequest->loan)
                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Transaksi Peminjaman</flux:heading>
                    </div>
                    <dl class="grid gap-x-4 gap-y-2 p-5 text-sm sm:grid-cols-4">
                        <dt class="text-zinc-500 dark:text-zinc-400">Nomor transaksi</dt>
                        <dd class="sm:col-span-3">#{{ $loanRequest->loan->reference }}</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Eksemplar</dt>
                        <dd class="sm:col-span-3">{{ $loanRequest->loan->copy?->inventory_code ?? '-' }}</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Tanggal pinjam</dt>
                        <dd class="sm:col-span-3">{{ $loanRequest->loan->borrowed_at->locale('id')->translatedFormat('d F Y') }}</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Jatuh tempo</dt>
                        <dd class="sm:col-span-3">{{ $loanRequest->loan->due_at->locale('id')->translatedFormat('d F Y') }}</dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">Status</dt>
                        <dd class="sm:col-span-3">
                            <x-status-badge :label="$loanRequest->loan->statusLabel()" :variant="$loanRequest->loan->statusVariant()" />
                        </dd>
                    </dl>
                </flux:card>
            @endif
        </div>

        <div class="space-y-4">
            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Status Pengajuan</flux:heading>
                </div>
                <div class="p-5">
                    <x-status-badge :label="$loanRequest->statusLabel()" :variant="$loanRequest->statusVariant()" />

                    @if ($loanRequest->isPending())
                        <flux:text class="mt-3">
                            Periksa kuota, status akun, dan ketersediaan eksemplar sebelum menyetujui. Persetujuan belum menandai buku sebagai dipinjam.
                        </flux:text>
                    @elseif ($loanRequest->isApproved() && ! $loanRequest->isExpired())
                        <flux:text class="mt-3">
                            Pengajuan disetujui. Catat checkout setelah buku benar-benar diserahkan kepada peminjam.
                        </flux:text>
                    @elseif ($loanRequest->isApproved() && $loanRequest->isExpired())
                        <flux:callout variant="warning" icon="exclamation-triangle" class="mt-3">
                            <flux:callout.text>Batas pengambilan sudah lewat. Checkout diblokir; batalkan pengajuan agar peminjam dapat mengajukan ulang.</flux:callout.text>
                        </flux:callout>
                    @endif
                </div>
            </flux:card>

            @if ($loanRequest->isPending())
                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Tindakan</flux:heading>
                    </div>
                    <div class="space-y-4 p-5">
                        <form method="POST" action="{{ route('admin.requests.approve', $loanRequest) }}" data-confirm="Setujui permintaan ini? Buku belum dipinjam sampai checkout dicatat." data-loading>
                            @csrf
                            <flux:button type="submit" variant="primary" icon="check-circle" class="w-full" data-loading-text="Menyetujui...">
                                Setujui Permintaan
                            </flux:button>
                        </form>

                        <flux:separator />

                        <form method="POST" action="{{ route('admin.requests.reject', $loanRequest) }}" data-confirm="Tolak permintaan ini? Alasan akan ditampilkan kepada peminjam." data-loading>
                            @csrf
                            <flux:field>
                                <flux:label for="rejection_reason">Alasan penolakan <span class="text-rose-600">*</span></flux:label>
                                <flux:textarea id="rejection_reason" name="rejection_reason" rows="3" maxlength="1000" required placeholder="Contoh: eksemplar sedang diperiksa.">{{ old('rejection_reason') }}</flux:textarea>
                                <flux:error name="rejection_reason" />
                            </flux:field>
                            <flux:button type="submit" variant="danger" icon="x-circle" class="mt-3 w-full" data-loading-text="Menolak...">
                                Tolak Permintaan
                            </flux:button>
                        </form>
                    </div>
                </flux:card>
            @endif

            @if ($loanRequest->isApproved())
                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Tindakan</flux:heading>
                    </div>
                    <div class="space-y-3 p-5">
                        @if (! $loanRequest->isExpired())
                            <flux:button variant="primary" icon="arrow-right-start-on-rectangle" href="{{ route('admin.requests.checkout', $loanRequest) }}" class="w-full">
                                Checkout Peminjaman
                            </flux:button>
                        @endif

                        <form method="POST" action="{{ route('admin.requests.cancel', $loanRequest) }}" data-confirm="Batalkan pengajuan ini? Peminjam harus mengajukan ulang jika masih membutuhkan buku.">
                            @csrf
                            <flux:button type="submit" variant="danger" class="w-full">Batalkan Pengajuan</flux:button>
                        </form>
                    </div>
                </flux:card>
            @endif

            <flux:card class="p-0!">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                    <flux:heading size="lg" level="2">Kebijakan &amp; Stok</flux:heading>
                </div>
                <dl class="grid gap-2 p-5 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-zinc-500 dark:text-zinc-400">Sisa kuota peminjam</dt>
                        <dd>{{ $quota['remaining'] }} eksemplar</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-zinc-500 dark:text-zinc-400">Durasi pinjam</dt>
                        <dd>{{ $quota['duration_days'] }} hari</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-zinc-500 dark:text-zinc-400">Batas pengambilan</dt>
                        <dd>{{ $quota['pickup_deadline_days'] }} hari</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-zinc-500 dark:text-zinc-400">Eksemplar siap serah</dt>
                        <dd>{{ $availableCopies->count() }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-zinc-500 dark:text-zinc-400">Ditahan pengajuan lain</dt>
                        <dd>{{ $approvedHolds }}</dd>
                    </div>
                </dl>
            </flux:card>
        </div>
    </div>
@endsection
