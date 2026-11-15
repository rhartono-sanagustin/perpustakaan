@php
    $reportNav = [
        ['key' => 'ringkasan', 'route' => 'admin.reports.index', 'label' => 'Ringkasan', 'icon' => 'bar-chart'],
        ['key' => 'peminjaman', 'route' => 'admin.reports.loans', 'label' => 'Peminjaman', 'icon' => 'arrow-left-right'],
        ['key' => 'pengembalian', 'route' => 'admin.reports.returns', 'label' => 'Pengembalian', 'icon' => 'arrow-return-left'],
        ['key' => 'keterlambatan', 'route' => 'admin.reports.overdue', 'label' => 'Keterlambatan', 'icon' => 'exclamation-triangle'],
        ['key' => 'inventaris', 'route' => 'admin.reports.inventory', 'label' => 'Inventaris', 'icon' => 'box-seam'],
        ['key' => 'populer', 'route' => 'admin.reports.popular', 'label' => 'Buku Populer', 'icon' => 'star'],
    ];
@endphp

<ul class="nav nav-pills flex-column flex-sm-row gap-1 mb-3" aria-label="Jenis laporan">
    @foreach ($reportNav as $item)
        <li class="nav-item">
            <a class="nav-link {{ $activeReport === $item['key'] ? 'active' : '' }}" href="{{ route($item['route'], $filters->query()) }}">
                <i class="bi bi-{{ $item['icon'] }} me-1" aria-hidden="true"></i>{{ $item['label'] }}
            </a>
        </li>
    @endforeach
</ul>
