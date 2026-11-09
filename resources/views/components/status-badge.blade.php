@props(['label', 'variant' => 'secondary', 'icon' => null])

<span {{ $attributes->class(['badge', 'text-bg-'.$variant, 'd-inline-flex', 'align-items-center', 'gap-1']) }}>
    @if ($icon)
        <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
    @endif
    {{ $label }}
</span>
