@props(['title', 'subtitle' => null])

<div {{ $attributes->class(['d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4']) }}>
    <div>
        <h1 class="h3 mb-1">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-muted mb-0">{{ $subtitle }}</p>
        @endif
    </div>

    @if (isset($actions) && ! $actions->isEmpty())
        <div class="d-flex flex-wrap gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
