<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Terjadi Kesalahan') &mdash; {{ config('library.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset(config('library.logo')) }}">
    <style>
        :root {
            --primary: #1f4e79;
            --primary-hover: #173b5c;
            --text: #243244;
            --muted: #667085;
            --border: #e4e7ec;
            --bg: #f5f7fa;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            color: var(--text);
            background: var(--bg);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .card {
            width: 100%;
            max-width: 520px;
            padding: 2.5rem;
            text-align: center;
            background: #fff;
            border: 1px solid var(--border);
            border-top: 4px solid var(--primary);
            border-radius: .25rem;
        }

        .mark {
            width: 56px;
            height: 56px;
            margin: 0 auto 1.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .25rem;
            color: #fff;
            background: var(--primary);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .code {
            margin: 0;
            font-size: 3rem;
            line-height: 1;
            font-weight: 800;
            color: var(--primary);
        }

        h1 {
            margin: 1rem 0 .5rem;
            font-size: 1.35rem;
        }

        p {
            margin: 0 0 1.5rem;
            color: var(--muted);
            line-height: 1.6;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: .5rem;
        }

        .btn {
            display: inline-block;
            padding: .6rem 1rem;
            border-radius: .25rem;
            border: 1px solid var(--primary);
            color: #fff;
            background: var(--primary);
            text-decoration: none;
            font-size: .925rem;
            font-weight: 500;
        }

        .btn:hover { background: var(--primary-hover); border-color: var(--primary-hover); }

        .btn-outline {
            color: var(--primary);
            background: #fff;
        }

        .btn-outline:hover { color: #fff; }

        .brand {
            margin-top: 1.5rem;
            font-size: .8rem;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="mark" aria-hidden="true">&#128218;</div>
        <p class="code">@yield('code', '500')</p>
        <h1>@yield('title', 'Terjadi Kesalahan')</h1>
        <p>@yield('message', 'Maaf, terjadi kesalahan. Silakan coba lagi.')</p>

        <div class="actions">
            <a href="{{ url('/') }}" class="btn">Kembali ke Beranda</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-outline">Buka Dashboard</a>
            @endauth
        </div>

        <div class="brand">{{ config('library.name') }} &mdash; {{ config('library.school_name') }}</div>
    </main>
</body>
</html>
