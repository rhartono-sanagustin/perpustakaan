@props(['label', 'variant' => 'secondary', 'icon' => null])

@php
    $color = match ($variant) {
        'primary' => 'sky',
        'success' => 'green',
        'info' => 'cyan',
        'warning' => 'amber',
        'danger' => 'red',
        'secondary' => 'zinc',
        default => $variant,
    };
@endphp

<flux:badge :color="$color" size="sm" data-variant="{{ $variant }}" {{ $attributes }}>
    @if ($icon)
        <x-icon :name="$icon" variant="mini" class="size-3.5" />
    @endif
    {{ $label }}
</flux:badge>
