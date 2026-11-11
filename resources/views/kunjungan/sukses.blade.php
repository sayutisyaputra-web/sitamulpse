@extends('layouts.tamu')

@section('title', 'Kunjungan Tercatat')

@section('content')
<div class="rounded-2xl bg-white p-8 text-center shadow-lg ring-1 ring-slate-200">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
        <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
    </div>

    <h1 class="mt-5 text-2xl font-semibold text-brand-900">Terima kasih, {{ session('nama') }}</h1>
    <p class="mt-2 text-sm text-slate-500">Kunjungan Anda sudah tercatat. Nomor kunjungan Anda:</p>

    <div class="mt-5 rounded-xl border-2 border-dashed border-brand-200 bg-brand-50 px-4 py-5">
        <p id="nomor-kunjungan" class="font-mono text-2xl font-bold tracking-wider text-brand-800 sm:text-3xl">{{ session('nomor_kunjungan') }}</p>
    </div>

    <button type="button" id="salin-nomor" class="ui-btn ui-btn-secondary mt-4">Salin nomor</button>

    <div class="mt-6 rounded-xl bg-slate-50 p-4 text-left text-sm text-slate-600">
        <p class="font-medium text-slate-800">Langkah selanjutnya</p>
        <ol class="mt-2 list-decimal space-y-1 pl-5">
            <li>Tunjukkan nomor ini kepada petugas.</li>
            <li>Tunggu hingga petugas memproses kunjungan Anda.</li>
            <li>Simpan atau foto layar ini, karena nomor tidak akan tampil lagi setelah halaman ditutup.</li>
        </ol>
    </div>

    <a href="{{ route('kunjungan.create') }}" class="ui-btn ui-btn-secondary mt-6">Isi buku tamu lagi</a>
</div>

<script>
    (function () {
        var tombol = document.getElementById('salin-nomor');
        var nomor = document.getElementById('nomor-kunjungan').textContent.trim();

        function berhasil() {
            tombol.textContent = 'Nomor disalin';
            setTimeout(function () { tombol.textContent = 'Salin nomor'; }, 2000);
        }

        function cadangan() {
            var area = document.createElement('textarea');
            area.value = nomor;
            area.style.position = 'fixed';
            area.style.opacity = '0';
            document.body.appendChild(area);
            area.select();
            try { document.execCommand('copy'); berhasil(); } catch (e) {}
            document.body.removeChild(area);
        }

        tombol.addEventListener('click', function () {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(nomor).then(berhasil, cadangan);
            } else {
                cadangan();
            }
        });
    })();
</script>
@endsection