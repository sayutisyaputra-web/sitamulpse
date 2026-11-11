@php
    $galeriFoto = \App\Models\GaleriBeranda::tampil()->orderBy('urutan')->orderBy('id')->get();
@endphp

@if ($galeriFoto->isNotEmpty())
<style>
    .gl-wrap { max-width: 690px; margin: 24px auto 0; padding: 0 16px; }
    .gl-judul { font-size: 15px; font-weight: 700; color: #1e2a78; margin: 0 0 12px; }
    .gl-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
    .gl-item { padding: 0; border: 0; border-radius: 12px; overflow: hidden; background: #e2e8f0; cursor: pointer;
               box-shadow: 0 4px 16px rgba(30, 42, 120, .08); }
    .gl-item img { display: block; width: 100%; height: 110px; object-fit: cover; transition: transform .2s; }
    .gl-item:hover img { transform: scale(1.05); }
    .gl-layar { position: fixed; inset: 0; z-index: 50; display: flex; flex-direction: column; align-items: center;
                justify-content: center; padding: 16px; background: rgba(15, 23, 71, .88); cursor: pointer; }
    .gl-layar[hidden] { display: none; }
    .gl-layar img { max-width: 100%; max-height: 80vh; border-radius: 12px; }
    .gl-layar p { margin: 12px 0 0; color: #fff; font-size: 14px; text-align: center; }
</style>

<div class="gl-wrap" id="galeri-foto">
    <h2 class="gl-judul">Galeri Foto</h2>
    <div class="gl-grid">
        @foreach ($galeriFoto as $g)
            <button type="button" class="gl-item"
                    data-src="{{ $g->fotoUrl() }}" data-ket="{{ $g->keterangan }}">
                <img src="{{ $g->fotoUrl() }}" alt="{{ $g->keterangan }}" loading="lazy">
            </button>
        @endforeach
    </div>
</div>

<div class="gl-layar" id="gl-layar" hidden>
    <img id="gl-besar" src="" alt="">
    <p id="gl-ket"></p>
</div>

<script>
    (function () {
        var layar = document.getElementById('gl-layar');
        var besar = document.getElementById('gl-besar');
        var ket = document.getElementById('gl-ket');

        document.querySelectorAll('.gl-item').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                besar.src = tombol.dataset.src;
                ket.textContent = tombol.dataset.ket || '';
                layar.hidden = false;
            });
        });

        layar.addEventListener('click', function () { layar.hidden = true; });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { layar.hidden = true; }
        });
    })();
</script>
@endif