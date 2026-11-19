@php
    $reportNav = [
        ['key' => 'ringkasan', 'route' => 'admin.reports.index', 'label' => 'Ringkasan', 'icon' => 'chart-bar'],
        ['key' => 'peminjaman', 'route' => 'admin.reports.loans', 'label' => 'Peminjaman', 'icon' => 'arrows-right-left'],
        ['key' => 'pengembalian', 'route' => 'admin.reports.returns', 'label' => 'Pengembalian', 'icon' => 'arrow-uturn-left'],
        ['key' => 'keterlambatan', 'route' => 'admin.reports.overdue', 'label' => 'Keterlambatan', 'icon' => 'exclamation-triangle'],
        ['key' => 'inventaris', 'route' => 'admin.reports.inventory', 'label' => 'Inventaris', 'icon' => 'archive-box'],
        ['key' => 'populer', 'route' => 'admin.reports.popular', 'label' => 'Buku Populer', 'icon' => 'star'],
    ];
@endphp

<nav class="-mx-1 mb-3 flex gap-1 overflow-x-auto" aria-label="Jenis laporan">
    @foreach ($reportNav as $item)
        <a
            href="{{ route($item['route'], $filters->query()) }}"
            @class([
                'inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium transition',
                'bg-[var(--color-accent)] text-[var(--color-accent-foreground)] shadow-xs' => $activeReport === $item['key'],
                'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10' => $activeReport !== $item['key'],
            ])
            @if ($activeReport === $item['key']) aria-current="page" @endif
        >
            <flux:icon :icon="$item['icon']" variant="mini" class="size-4" />
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>
