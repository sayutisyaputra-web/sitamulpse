<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
@php
    [$namaApp, $namaInstansi] = array_pad(explode(' - ', config('app.name'), 2), 2, '');
@endphp

<div class="flex min-h-screen flex-col lg:flex-row">
    {{-- Panel merek --}}
    <div class="relative overflow-hidden bg-brand-900 px-8 py-10 text-white lg:flex lg:w-1/2 lg:flex-col lg:justify-between lg:px-14 lg:py-14">
        <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-brand-600/30"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-12 h-64 w-64 rounded-full bg-brand-700/40"></div>

        <div class="relative flex items-center gap-4">
            <div class="flex h-14 w-20 shrink-0 items-center justify-center rounded-xl bg-white p-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="max-h-full w-auto">
            </div>
            <div class="leading-tight">
                <div class="text-lg font-semibold">{{ $namaApp }}</div>
                @if ($namaInstansi !== '')
                    <div class="text-sm text-brand-200">{{ $namaInstansi }}</div>
                @endif
            </div>
        </div>

        <div class="relative mt-10 lg:mt-0">
            <div class="mb-4 h-1 w-12 rounded bg-accent-400"></div>
            <h1 class="text-2xl font-semibold leading-snug lg:text-4xl">Pencatatan tamu yang rapi dan aman</h1>
            <p class="mt-3 max-w-md text-sm text-brand-200 lg:text-base">
                Masuk untuk mengelola kunjungan, memantau tamu yang sedang berada di kantor, dan membuat laporan.
            </p>
        </div>

        <p class="relative mt-10 hidden text-xs text-brand-300 lg:block">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </div>

    {{-- Form masuk --}}
    <div class="flex flex-1 items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-md">
            <h2 class="text-2xl font-semibold text-brand-900">Masuk</h2>
            <p class="mt-1 text-sm text-slate-500">Khusus petugas, admin, dan pimpinan.</p>

            @if ($errors->any())
                <div class="mt-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="email" class="ui-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="ui-field !py-2.5">
                </div>

                <div>
                    <label for="password" class="ui-label">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="ui-field !py-2.5 !pr-11">
                        <button type="button" id="lihat-password" aria-label="Tampilkan password"
                                class="absolute inset-y-0 right-0 mt-1 flex w-11 items-center justify-center text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="ingat" class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-500">
                    Ingat saya di perangkat ini
                </label>
<div class="cf-turnstile w-full" data-sitekey="{{ config('services.turnstile.site_key') }}" data-language="id" data-size="flexible" data-theme="light"></div>
                <button type="submit" class="ui-btn ui-btn-primary w-full !py-2.5">Masuk</button>
            </form>

            <p class="mt-8 text-center text-sm text-slate-500">
                Anda tamu?
                <a href="{{ route('kunjungan.create') }}" class="font-medium text-brand-700 hover:underline">Isi buku tamu di sini</a>
            </p>
        </div>
    </div>
</div>

<script>
    (function () {
        var tombol = document.getElementById('lihat-password');
        var kolom = document.getElementById('password');

        tombol.addEventListener('click', function () {
            var tampil = kolom.type === 'password';
            kolom.type = tampil ? 'text' : 'password';
            tombol.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
        });
    })();
</script>
</body>
</html>