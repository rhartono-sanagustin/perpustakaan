@props(['title', 'subtitle' => null])

<div {{ $attributes->class('mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between') }}>
    <div class="min-w-0">
        <flux:heading size="xl">{{ $title }}</flux:heading>
        @if ($subtitle)
            <flux:text class="mt-1">{{ $subtitle }}</flux:text>
        @endif
    </div>

    @if (isset($actions) && ! $actions->isEmpty())
        <div class="flex flex-wrap gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
