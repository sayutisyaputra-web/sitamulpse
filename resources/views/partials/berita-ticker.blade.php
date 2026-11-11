@php
    $beritaTicker = \App\Models\Berita::terbit()->latest('tanggal_terbit')->take(5)->get();
    $durasiTicker = max(20, $beritaTicker->count() * 10);
@endphp

@if ($beritaTicker->isNotEmpty())
<style>
    .tk-bar { display: flex; align-items: center; background: #0f1747; color: #fff; font-size: .8rem; overflow: hidden; }
    .tk-label { flex: none; padding: .55rem .9rem; background: #facc15; color: #141c55; font-weight: 800; letter-spacing: .06em; text-decoration: none; }
    .tk-jalur { flex: 1; overflow: hidden; white-space: nowrap; }
    .tk-isi { display: inline-block; padding-left: 100%; animation: tk-geser linear infinite; }
    .tk-jalur:hover .tk-isi { animation-play-state: paused; }
    .tk-isi a { color: #fff; text-decoration: none; margin-right: 2.5rem; }
    .tk-isi a:hover { text-decoration: underline; }
    .tk-isi a::before { content: "•"; color: #facc15; margin-right: .6rem; }
    @keyframes tk-geser { from { transform: translateX(0); } to { transform: translateX(-100%); } }
    @media (prefers-reduced-motion: reduce) {
        .tk-isi { animation: none; padding-left: .9rem; }
        .tk-jalur { overflow-x: auto; }
    }
</style>

<div class="tk-bar">
    <a href="#informasi-terbaru" class="tk-label">INFO</a>
    <div class="tk-jalur">
        <div class="tk-isi" style="animation-duration: {{ $durasiTicker }}s;">
            @foreach ($beritaTicker as $b)
                <a href="{{ route('berita.show', $b) }}">{{ $b->judul }}</a>
            @endforeach
        </div>
    </div>
</div>
@endif