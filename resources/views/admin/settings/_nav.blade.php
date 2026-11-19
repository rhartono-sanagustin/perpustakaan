<nav class="-mx-1 mb-4 flex gap-1 overflow-x-auto border-b border-zinc-200 pb-px dark:border-zinc-700" aria-label="Navigasi pengaturan">
    @php
        $tabs = [
            ['route' => 'admin.settings.identity.edit', 'active' => request()->routeIs('admin.settings.identity.*'), 'icon' => 'building-office', 'label' => 'Identitas Sekolah'],
            ['route' => 'admin.settings.index', 'active' => request()->routeIs('admin.settings.index'), 'icon' => 'adjustments-horizontal', 'label' => 'Kebijakan Sirkulasi'],
            ['route' => 'admin.settings.visits.edit', 'active' => request()->routeIs('admin.settings.visits.*'), 'icon' => 'qr-code', 'label' => 'Kunjungan'],
            ['route' => 'admin.settings.carousel.index', 'active' => request()->routeIs('admin.settings.carousel.*'), 'icon' => 'photo', 'label' => 'Carousel Beranda'],
        ];
    @endphp

    @foreach ($tabs as $tab)
        <a
            href="{{ route($tab['route']) }}"
            @class([
                'inline-flex shrink-0 items-center gap-1.5 rounded-t-lg border-b-2 px-3 py-2 text-sm font-medium transition',
                'border-[var(--color-accent)] text-[var(--color-accent-content)]' => $tab['active'],
                'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-800 dark:text-zinc-400 dark:hover:border-zinc-600 dark:hover:text-white' => ! $tab['active'],
            ])
            @if ($tab['active']) aria-current="page" @endif
        >
            <flux:icon :icon="$tab['icon']" variant="mini" class="size-4" />
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
