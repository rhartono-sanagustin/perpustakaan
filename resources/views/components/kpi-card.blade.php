@props(['icon' => 'book', 'value' => '0', 'label' => '', 'variant' => 'primary', 'href' => null])

<div class="col">
    <div class="card kpi-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
            <span class="kpi-icon text-bg-{{ $variant }}">
                <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
            </span>
            <div class="min-w-0">
                <div class="kpi-value">{{ $value }}</div>
                <div class="small text-muted text-truncate">{{ $label }}</div>
            </div>
        </div>
        @if ($href)
            <a href="{{ $href }}" class="stretched-link" aria-label="{{ $label }}"></a>
        @endif
    </div>
</div>
