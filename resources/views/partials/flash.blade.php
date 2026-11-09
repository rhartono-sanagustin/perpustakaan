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
    <div class="toast-container position-fixed top-0 end-0 p-3" aria-live="polite" aria-atomic="true">
        @foreach ($flashMessages as $flash)
            @php($isWarning = $flash['variant'] === 'warning')
            <div class="toast align-items-center border-0 text-bg-{{ $flash['variant'] }}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ $flash['message'] }}
                    </div>
                    <button type="button" class="btn-close {{ $isWarning ? '' : 'btn-close-white' }} me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
                </div>
            </div>
        @endforeach
    </div>
@endif
