@php
    $beritaTerbaru = \App\Models\Berita::terbit()->latest('tanggal_terbit')->take(3)->get();
@endphp

@if ($beritaTerbaru->isNotEmpty())
<style>
    .bt-wrap { max-width: 690px; margin: 24px auto 0; padding: 0 16px; }
    .bt-judul { font-size: 15px; font-weight: 700; color: #1e2a78; margin: 0 0 12px; }
    .bt-grid { display: grid; gap: 12px; }
    .bt-kartu { display: flex; gap: 12px; background: #fff; border-radius: 14px; padding: 12px;
                box-shadow: 0 4px 16px rgba(30, 42, 120, .08); text-decoration: none; color: inherit; }
    .bt-kartu img { width: 96px; height: 72px; object-fit: cover; border-radius: 10px; flex-shrink: 0; }
    .bt-isi h3 { margin: 0 0 4px; font-size: 14px; color: #1e2a78; }
    .bt-isi p { margin: 0; font-size: 12px; color: #64748b; }
    .bt-tgl { font-size: 11px; color: #94a3b8; margin-top: 4px; }
</style>

<div class="bt-wrap" id="informasi-terbaru">
    <h2 class="bt-judul">Informasi Terbaru</h2>
    <div class="bt-grid">
        @foreach ($beritaTerbaru as $b)
            <a class="bt-kartu" href="{{ route('berita.show', $b) }}">
                @if ($b->gambarUrl())
                    <img src="{{ $b->gambarUrl() }}" alt="">
                @endif
                <div class="bt-isi">
                    <h3>{{ $b->judul }}</h3>
                    <p>{{ $b->ringkasan ?: \Illuminate\Support\Str::limit(strip_tags($b->isi), 100) }}</p>
                    <div class="bt-tgl">{{ $b->tanggal_terbit?->format('d/m/Y') }}</div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif