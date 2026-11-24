@if (is_array($report))
    <flux:card>
        <div class="flex flex-wrap items-center justify-between gap-2">
            <flux:heading size="lg" level="2">Hasil Impor</flux:heading>
            <flux:badge color="zinc" size="sm">{{ $report['filename'] }}</flux:badge>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-3">
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 dark:border-green-900/50 dark:bg-green-950/30">
                <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $report['created'] }}</div>
                <div class="text-xs text-green-700/80 dark:text-green-300/80">Dibuat</div>
            </div>
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-900/50 dark:bg-blue-950/30">
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $report['updated'] }}</div>
                <div class="text-xs text-blue-700/80 dark:text-blue-300/80">Diperbarui</div>
            </div>
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-900/50 dark:bg-red-950/30">
                <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $report['failed'] }}</div>
                <div class="text-xs text-red-700/80 dark:text-red-300/80">Gagal</div>
            </div>
        </div>

        @if (count($report['errors']) > 0)
            <div class="mt-4 overflow-x-auto" data-scrollable-region>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col" class="w-20">Baris</th>
                            <th scope="col">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report['errors'] as $error)
                            <tr>
                                <td class="font-mono text-xs">{{ $error['line'] }}</td>
                                <td>{{ $error['message'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($report['truncated'])
                <flux:text class="mt-3 text-sm">
                    Sebagian pesan tidak ditampilkan karena melebihi batas laporan. Perbaiki baris yang tercantum, lalu impor ulang.
                </flux:text>
            @endif
        @else
            <flux:text class="mt-4">Tidak ada baris yang gagal diproses.</flux:text>
        @endif

        @if (($report['total'] ?? 0) === 0)
            <flux:text class="mt-2">Berkas tidak berisi baris data.</flux:text>
        @endif
    </flux:card>
@endif
