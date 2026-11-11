@extends('layouts.tamu')

@section('title', 'Buku Tamu Terkunci')
@section('subjudul', 'Silakan hubungi petugas resepsionis.')

@section('content')
<div class="rounded-xl bg-white p-6 text-center shadow sm:p-8">
    <div style="display:inline-flex;align-items:center;justify-content:center;width:4rem;height:4rem;border-radius:999px;background:#eef2ff;color:#1e2a78;">
        <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>
        </svg>
    </div>

    <h2 style="margin:1rem 0 0;font-size:1.4rem;font-weight:800;color:#1e2a78;">Buku tamu terkunci</h2>
    <p style="margin:.4rem 0 0;font-size:.9rem;color:#64748b;">
        Mohon tunggu petugas untuk membuka buku tamu bagi Anda.
    </p>

    @if (session('pesan_kunci'))
        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
            {{ session('pesan_kunci') }}
        </div>
    @endif

    <form method="POST" action="{{ route('buku.buka') }}" style="max-width:20rem;margin:1.5rem auto 0;text-align:left;">
        @csrf
        <label for="pin" class="block text-sm font-medium text-slate-700">PIN petugas</label>
        <input id="pin" name="pin" type="password" inputmode="numeric" maxlength="6" autocomplete="off" autofocus required
               style="margin-top:.25rem;width:100%;text-align:center;letter-spacing:.5em;font-size:1.25rem;"
               class="rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('pin')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <button type="submit" class="mt-4 w-full rounded-lg bg-blue-600 py-2.5 font-semibold text-white hover:bg-blue-700">
            Buka buku tamu
        </button>
    </form>
</div>

@push('scripts')
<script>
    // Segarkan halaman secara berkala dan setelah komputer atau tablet tidur,
    // supaya token form tidak kedaluwarsa (penyebab error 419).
    (function () {
        var dimuat = Date.now();
        var batas = 5 * 60 * 1000;
        var pin = document.getElementById('pin');

        function segarkan() {
            if (pin && pin.value !== '') { return; }
            if (Date.now() - dimuat > batas) { window.location.reload(); }
        }

        setInterval(segarkan, 60 * 1000);
        window.addEventListener('focus', segarkan);
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') { segarkan(); }
        });
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) { window.location.reload(); }
        });
    })();
</script>
@endpush
@endsection
