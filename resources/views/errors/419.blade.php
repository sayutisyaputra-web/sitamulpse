<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Kedaluwarsa | {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background: #f1f5f9; color: #0f172a; }
        .atas { padding: 2.5rem 1rem 6rem; text-align: center; color: #fff; background: linear-gradient(135deg, #141c55, #1e2a78 55%, #2f4bd0); }
        .logo { display: inline-block; padding: .5rem .9rem; border-radius: .9rem; background: #fff; }
        .logo img { display: block; height: 48px; width: auto; }
        .atas h1 { margin: .8rem 0 0; font-size: 1.1rem; font-weight: 700; }
        .garis { width: 48px; height: 4px; margin: .8rem auto 0; border-radius: 999px; background: #facc15; }
        .kartu { max-width: 26rem; margin: -4rem auto 2rem; padding: 2rem 1.5rem; text-align: center; background: #fff; border: 1px solid #e2e8f0; border-radius: 1.25rem; box-shadow: 0 20px 45px -15px rgba(30, 42, 120, .35); }
        .kode { font-size: 3rem; font-weight: 800; color: #1e2a78; line-height: 1; }
        .kartu h2 { margin: .75rem 0 0; font-size: 1.2rem; }
        .kartu p { margin: .5rem 0 0; font-size: .9rem; color: #64748b; }
        .aksi { display: flex; flex-wrap: wrap; justify-content: center; gap: .6rem; margin-top: 1.5rem; }
        .tombol { display: inline-block; padding: .65rem 1.1rem; border-radius: .75rem; font-size: .9rem; font-weight: 600; text-decoration: none; background: #1e2a78; color: #fff; }
        .tombol:hover { background: #141c55; }
    </style>
</head>
<body>
    <header class="atas">
        <div class="logo"><img src="{{ asset('images/logo.png') }}" alt="Logo"></div>
        <h1>{{ config('app.name') }}</h1>
        <div class="garis"></div>
    </header>

    <main class="kartu">
        <div class="kode">419</div>
        <h2>Halaman sudah kedaluwarsa</h2>
        <p>Halaman ini terlalu lama terbuka sehingga sesinya berakhir. Muat ulang halaman, lalu coba lagi. Isian yang belum terkirim perlu diisi ulang.</p>

        <div class="aksi">
            <a href="{{ url('/') }}" class="tombol">Muat ulang halaman</a>
        </div>
    </main>
</body>
</html>
