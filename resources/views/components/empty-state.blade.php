@props(['icon' => 'inbox', 'title' => 'Belum ada data', 'message' => null])

<div {{ $attributes->class(['empty-state']) }}>
    <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
    <h2 class="h6 mt-3 mb-1">{{ $title }}</h2>
    @if ($message)
        <p class="text-muted mb-0">{{ $message }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-3">{{ $slot }}</div>
    @endif
</div>
