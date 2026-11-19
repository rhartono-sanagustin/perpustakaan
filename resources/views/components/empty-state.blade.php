@props(['icon' => 'inbox', 'title' => 'Belum ada data', 'message' => null])

<div {{ $attributes->class('flex flex-col items-center justify-center px-4 py-10 text-center') }}>
    <span class="mb-3 inline-flex size-14 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-white/5 dark:text-zinc-400">
        <x-icon :name="$icon" variant="outline" class="size-7" />
    </span>

    <flux:heading size="sm">{{ $title }}</flux:heading>

    @if ($message)
        <flux:text class="mt-1 max-w-md">{{ $message }}</flux:text>
    @endif

    @if (! $slot->isEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
