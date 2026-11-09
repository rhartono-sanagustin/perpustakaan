<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') &mdash; {{ config('library.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset(config('library.logo')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-card">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="text-decoration-none text-reset d-inline-flex align-items-center gap-3">
                <span class="brand-mark"><i class="bi bi-book-half" aria-hidden="true"></i></span>
                <span class="text-start">
                    <span class="d-block fw-semibold">{{ config('library.name') }}</span>
                    <span class="d-block small text-muted">{{ config('library.school_name') }}</span>
                </span>
            </a>
        </div>

        <div class="card">
            <div class="card-body p-4 p-md-5">
                @yield('content')
            </div>
        </div>

        <p class="text-center text-muted small mt-4 mb-0">
            &copy; {{ date('Y') }} {{ config('library.school_name') }}
        </p>
    </main>

    @include('partials.flash')
    @stack('scripts')
</body>
</html>
