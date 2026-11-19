@props(['icon' => 'book', 'value' => '0', 'label' => '', 'variant' => 'primary', 'href' => null])

@php
    $iconClasses = match ($variant) {
        'success' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300',
        'info' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-400/15 dark:text-cyan-300',
        'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300',
        'danger' => 'bg-rose-100 text-rose-700 dark:bg-rose-400/15 dark:text-rose-300',
        'secondary' => 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300',
        default => 'bg-sky-100 text-sky-700 dark:bg-sky-400/15 dark:text-sky-300',
    };
@endphp

<flux:card class="relative h-full">
    <div class="flex items-center gap-3">
        <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-lg {{ $iconClasses }}">
            <x-icon :name="$icon" class="size-5" />
        </span>
        <div class="min-w-0">
            <div class="text-2xl leading-tight font-bold">{{ $value }}</div>
            <div class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ $label }}</div>
        </div>
    </div>

    @if ($href)
        <a href="{{ $href }}" class="absolute inset-0 rounded-xl" aria-label="{{ $label }}"></a>
    @endif
</flux:card>
