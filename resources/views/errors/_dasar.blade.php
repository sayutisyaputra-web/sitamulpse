<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul') | {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
@php
    try {
        $sudahMasuk = auth()->check();
    } catch (\Throwable $e) {
        $sudahMasuk = false;
    }

    [$namaApp, $namaInstansi] = array_pad(explode(' - ', config('app.name'), 2), 2, '');
@endphp

<div class="flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white text-center shadow-lg ring-1 ring-slate-200">
        <div class="bg-brand-900 px-6 py-6">
            <div class="mx-auto flex h-12 w-20 items-center justify-center rounded-lg bg-white p-1.5">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="max-h-full w-auto">
            </div>
            <div class="mt-3 text-sm font-semibold text-white">{{ $namaApp }}</div>
            @if ($namaInstansi !== '')
                <div class="text-xs text-brand-200">{{ $namaInstansi }}</div>
            @endif
            <div class="mx-auto mt-3 h-0.5 w-10 rounded bg-accent-400"></div>
        </div>

        <div class="px-8 py-10">
            <p class="text-6xl font-bold text-brand-700">@yield('kode')</p>
            <h1 class="mt-3 text-xl font-semibold text-brand-900">@yield('judul')</h1>
            <p class="mt-2 text-sm text-slate-500">@yield('pesan')</p>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ $sudahMasuk ? route('dashboard') : url('/') }}" class="ui-btn ui-btn-primary">
                    {{ $sudahMasuk ? 'Kembali ke dashboard' : 'Kembali ke beranda' }}
                </a>
                <button type="button" onclick="history.length > 1 ? history.back() : (window.location.href = '{{ url('/') }}')" class="ui-btn ui-btn-secondary">
                    Halaman sebelumnya
                </button>
            </div>
        </div>
    </div>
</div>
</body>
</html>