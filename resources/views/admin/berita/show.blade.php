<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $berita->judul }} | SITAMU LPSE</title>
    <style>
        body { margin:0; font-family: system-ui, sans-serif; background:#f1f5f9; color:#1e293b; }
        .wrap { max-width:760px; margin:0 auto; padding:24px 16px; }
        .kartu { background:#fff; border-radius:16px; padding:24px; box-shadow:0 4px 16px rgba(0,0,0,.06); }
        h1 { margin:0 0 8px; font-size:24px; color:#1e2a78; }
        .tgl { font-size:13px; color:#64748b; margin-bottom:16px; }
        img { max-width:100%; border-radius:12px; margin-bottom:16px; }
        a { color:#2563eb; }
    </style>
</head>
<body>
<div class="wrap">
    <p><a href="{{ url('/') }}">&larr; Kembali</a></p>
    <div class="kartu">
        <h1>{{ $berita->judul }}</h1>
        <div class="tgl">{{ $berita->tanggal_terbit?->format('d/m/Y') }}</div>
        @if ($berita->gambarUrl())
            <img src="{{ $berita->gambarUrl() }}" alt="{{ $berita->judul }}">
        @endif
        <div>{!! nl2br(e($berita->isi)) !!}</div>
    </div>
</div>
</body>
</html>