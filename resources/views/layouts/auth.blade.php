@inject('identity', 'App\Services\LibraryIdentity')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') &mdash; {{ $identity->libraryName() }}</title>
    <link rel="icon" href="{{ $identity->logoUrl() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-card">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="text-decoration-none text-reset d-inline-flex align-items-center gap-3">
                <x-library-logo size="md" />
                <span class="text-start">
                    <span class="d-block fw-semibold">{{ $identity->libraryName() }}</span>
                    <span class="d-block small text-muted">{{ $identity->schoolName() }}</span>
                </span>
            </a>
        </div>

        <div class="card">
            <div class="card-body p-4 p-md-5">
                @yield('content')
            </div>
        </div>

        <p class="text-center text-muted small mt-4 mb-0">
            &copy; {{ date('Y') }} {{ $identity->schoolName() }}
        </p>
    </main>

    @include('partials.flash')
    @stack('scripts')
</body>
</html>
