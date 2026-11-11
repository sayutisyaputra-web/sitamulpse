<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $berita->judul }} | SITAMU LPSE</title>
    <style>
        body { margin: 0; font-family: system-ui, sans-serif; background: #f1f5f9; color: #1e293b; }
        .wrap { max-width: 760px; margin: 0 auto; padding: 24px 16px; }
        .kembali { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 14px; padding: 8px 16px;
                   font-size: 14px; font-weight: 600; color: #1e2a78; text-decoration: none;
                   background: #fff; border-radius: 999px; box-shadow: 0 2px 8px rgba(30, 42, 120, .12); }
        .kembali:hover { background: #eef2ff; }
        .kartu { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 4px 16px rgba(0, 0, 0, .06); }
        h1 { margin: 0 0 8px; font-size: 24px; color: #1e2a78; }
        .tgl { font-size: 13px; color: #64748b; margin-bottom: 16px; }
        .kartu img { display: block; max-width: 100%; max-height: 420px; margin: 0 auto 16px; border-radius: 12px; object-fit: contain; }
        .isi { line-height: 1.7; }
    </style>
</head>
<body>
<div class="wrap">
    <a href="{{ url('/') }}" class="kembali">&larr; Kembali</a>

    <div class="kartu">
        <h1>{{ $berita->judul }}</h1>
        <div class="tgl">{{ $berita->tanggal_terbit?->format('d/m/Y') }}</div>
        @if ($berita->gambarUrl())
            <img src="{{ $berita->gambarUrl() }}" alt="{{ $berita->judul }}">
        @endif
        <div class="isi">{!! nl2br(e($berita->isi)) !!}</div>
    </div>
</div>
</body>
</html>