@php
    $flashMessages = [];

    foreach ([
        'status' => 'success',
        'success' => 'success',
        'error' => 'danger',
        'warning' => 'warning',
        'info' => 'info',
    ] as $key => $variant) {
        if (session()->has($key)) {
            $flashMessages[] = [
                'variant' => $variant,
                'message' => session($key),
            ];
        }
    }
@endphp

@if (count($flashMessages) > 0)
    @php
        $toastCalls = collect($flashMessages)
            ->map(fn ($flash) => '$flux.toast({ text: '.Illuminate\Support\Js::from($flash['message']).', variant: '.Illuminate\Support\Js::from($flash['variant']).', duration: 5500 });')
            ->implode(' ');
    @endphp

    <div x-data x-init="{{ $toastCalls }}" hidden aria-hidden="true"></div>
@endif
