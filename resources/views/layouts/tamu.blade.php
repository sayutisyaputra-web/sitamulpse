@php
    $beranda = \App\Models\PengaturanBeranda::ambil();
    $logoTamu = $beranda->logoUrl() ?? asset('images/logo.png');
    $bannerTamu = $beranda->bannerUrl();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --navy: #1e2a78;
            --navy-gelap: #141c55;
            --biru: #2563eb;
            --kuning: #facc15;
        }

        body {
            background: linear-gradient(180deg, #e8edff 0%, #f4f6fb 340px);
            min-height: 100vh;
        }

        /* ===== Banner atas ===== */
        .tamu-banner {
            position: relative;
            overflow: hidden;
            padding: 2rem 1rem 5.5rem;
            text-align: center;
            color: #fff;
            background: linear-gradient(135deg, var(--navy-gelap), var(--navy) 55%, #2f4bd0);
        }
        .tamu-banner::before {
            content: "";
            position: absolute;
            top: -90px;
            right: -60px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(250, 204, 21, .18);
        }
        .tamu-banner::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }
        .tamu-banner-inner { position: relative; z-index: 1; }
        .tamu-logo {
            display: inline-block;
            padding: .6rem 1rem;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .25);
        }
        .tamu-logo img { display: block; height: 56px; width: auto; }
        .tamu-banner h1 {
            margin: .9rem 0 0;
            font-size: 1.3rem;
            font-weight: 700;
            line-height: 1.3;
        }
        .tamu-banner p { margin: .35rem 0 0; font-size: .9rem; color: #dbe4ff; }
        .tamu-garis {
            width: 56px;
            height: 4px;
            margin: .9rem auto 0;
            border-radius: 999px;
            background: var(--kuning);
        }

        /* ===== Isi halaman ===== */
        .tamu-main {
            position: relative;
            z-index: 2;
            max-width: 48rem;
            margin: -3.5rem auto 0;
            padding: 0 1rem;
        }
        .tamu-main > :first-child {
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            box-shadow: 0 20px 45px -15px rgba(30, 42, 120, .35);
        }

        /* ===== Kolom isian ===== */
        .tamu-main label:not(:has(input[type=checkbox])) {
            font-weight: 600;
            color: #334155;
        }
        .tamu-main input:not([type=checkbox]):not([type=radio]):not([type=file]):not([type=hidden]),
        .tamu-main select,
        .tamu-main textarea {
            padding: .65rem .85rem;
            border: 1px solid #cbd5e1;
            border-radius: .75rem;
            background-color: #fff;
            font-size: .95rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .tamu-main input:not([type=checkbox]):not([type=radio]):not([type=file]):not([type=hidden]):focus,
        .tamu-main select:focus,
        .tamu-main textarea:focus {
            outline: none;
            border-color: var(--biru);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .15);
        }

        /* ===== Tombol kirim ===== */
        .tamu-main button[type=submit] {
            padding: .85rem 1rem;
            border-radius: .85rem;
            background: linear-gradient(135deg, var(--biru), #1d4ed8);
            font-size: 1rem;
            font-weight: 700;
            box-shadow: 0 10px 20px -8px rgba(37, 99, 235, .7);
            transition: transform .15s, box-shadow .15s;
        }
        .tamu-main button[type=submit]:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 24px -8px rgba(37, 99, 235, .8);
        }

        /* ===== Footer ===== */
        .tamu-footer {
            padding: 1.5rem 1rem 2rem;
            text-align: center;
            font-size: .75rem;
            color: #94a3b8;
        }
        .tamu-footer a { color: #64748b; }
        .tamu-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
        @yield('ticker')
    <header class="tamu-banner"
        @if ($bannerTamu)
            style="background-image: linear-gradient(135deg, rgba(20, 28, 85, .85), rgba(30, 42, 120, .7)), url('{{ $bannerTamu }}'); background-size: cover; background-position: center;"
        @endif>
        <div class="tamu-banner-inner">
            <div class="tamu-logo">
                <img src="{{ $logoTamu }}" alt="Logo">
            </div>
            <h1>{{ $beranda->judul_utama ?: config('app.name') }}</h1>
            @hasSection('subjudul')
                <p>@yield('subjudul')</p>
            @endif
            <div class="tamu-garis"></div>
        </div>
    </header>

    <main class="tamu-main">
        @yield('content')
    </main>

    <footer class="tamu-footer">
        {{ $beranda->teks_footer ?: '© ' . date('Y') . ' ' . config('app.name') }}
        @guest
            &middot; <a href="{{ route('login') }}">Masuk petugas</a>
        @endguest
    </footer>

    @stack('scripts')
</body>
</html>