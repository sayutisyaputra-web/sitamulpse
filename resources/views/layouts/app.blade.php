<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    @php
        $pengguna = auth()->user();
        $peran = $pengguna->role;
        $adminAtasan = in_array($peran, ['admin', 'superadmin']);
        $superadmin = $peran === 'superadmin';

        [$namaApp, $namaInstansi] = array_pad(
            explode(' - ', config('app.name'), 2),
            2,
            ''
        );

        $ikon = [
            'grafik' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
            'dashboard' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',

            'tamu' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',

            'bidang' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21',

            'akun' => 'M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z',

            'log' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z',

            'kunci' => 'M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z',

            'keluar' => 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75',

            'menu' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5',

            'centang' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ];

        $menu = [
            'Utama' => [
                [
                    'label' => 'Dashboard',
                    'rute' => 'dashboard',
                    'aktif' => 'dashboard',
                    'ikon' => 'dashboard',
                    'tampil' => true,
                ],
                [
                    'label' => 'Daftar tamu',
                    'rute' => 'petugas.kunjungan.index',
                    'aktif' => 'petugas.kunjungan.*',
                    'ikon' => 'tamu',
                    'tampil' => true,
                ],
            ],

            'Pemantauan' => [
                [
                    'label' => 'Kinerja petugas',
                    'rute' => 'kinerja.index',
                    'aktif' => 'kinerja.*',
                    'ikon' => 'grafik',
                    'tampil' => in_array($peran, ['admin', 'pimpinan', 'superadmin'], true),
                ],
            ],

            'Pengelolaan' => [
                [
                    'label' => 'Kelola bidang',
                    'rute' => 'admin.bidang.index',
                    'aktif' => 'admin.bidang.*',
                    'ikon' => 'bidang',
                    'tampil' => false, // disembunyikan: bidang digantikan layanan (data lama tetap ada)
                ],
                [
                    'label' => 'Petugas bidang',
                    'rute' => 'admin.petugas-bidang.index',
                    'aktif' => 'admin.petugas-bidang.*',
                    'ikon' => 'tamu',
                    'tampil' => false, // disembunyikan: diganti menu Petugas layanan
                ],
                [
                    'label' => 'Petugas layanan',
                    'rute' => 'admin.petugas-layanan.index',
                    'aktif' => 'admin.petugas-layanan.*',
                    'ikon' => 'tamu',
                    'tampil' => $adminAtasan,
                ],
                [
                    'label' => 'Kelola akun',
                    'rute' => 'admin.user.index',
                    'aktif' => 'admin.user.*',
                    'ikon' => 'akun',
                    'tampil' => $adminAtasan,
                ],
                [
                    'label' => 'Pengaturan beranda',
                    'rute' => 'beranda.pengaturan.edit',
                    'aktif' => 'beranda.*',
                    'ikon' => 'bidang',
                    'tampil' => $adminAtasan,
                ],
                [
                    'label' => 'Informasi berita',
                    'rute' => 'berita.kelola.index',
                    'aktif' => 'berita.kelola.*',
                    'ikon' => 'log',
                    'tampil' => $adminAtasan,
                ],
                [
                    'label' => 'Galeri foto',
                    'rute' => 'galeri.kelola.index',
                    'aktif' => 'galeri.kelola.*',
                    'ikon' => 'dashboard',
                    'tampil' => $adminAtasan,
                ],
            ],

            'Keamanan' => [
                [
                    'label' => 'Log aktivitas',
                    'rute' => 'admin.log.index',
                    'aktif' => 'admin.log.*',
                    'ikon' => 'log',
                    'tampil' => $superadmin,
                ],
            ],
        ];
    @endphp

    {{-- Lapisan gelap untuk menu HP --}}
    <div
        id="menu-lapisan"
        class="fixed inset-0 z-30 hidden bg-slate-900/50 lg:hidden">
    </div>

    {{-- Menu samping --}}
    <aside
        id="menu-samping"
        class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-brand-900 text-white shadow-xl transition-transform duration-200 lg:translate-x-0">

        {{-- Logo dan nama aplikasi --}}
        <div class="flex items-center gap-3 px-5 py-5">
            <div class="flex h-11 w-14 shrink-0 items-center justify-center rounded-lg bg-white p-1.5">
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Logo"
                    class="max-h-full w-auto">
            </div>

            <div class="min-w-0 leading-tight">
                <div class="truncate text-sm font-semibold">
                    {{ $namaApp }}
                </div>

                @if ($namaInstansi !== '')
                    <div class="truncate text-xs text-brand-200">
                        {{ $namaInstansi }}
                    </div>
                @endif
            </div>
        </div>

        <div class="mx-5 h-0.5 w-10 rounded bg-accent-400"></div>

        {{-- Navigasi --}}
        <nav class="mt-5 flex-1 space-y-5 overflow-y-auto px-3 pb-4">
            @foreach ($menu as $judul => $butir)
                @php($tampil = collect($butir)->where('tampil', true))

                @if ($tampil->isNotEmpty())
                    <div>
                        <div class="px-3 text-[11px] font-semibold uppercase tracking-wider text-brand-300">
                            {{ $judul }}
                        </div>

                        <ul class="mt-2 space-y-1">
                            @foreach ($tampil as $m)
                                @php($aktif = request()->routeIs($m['aktif']))

                                <li>
                                    <a
                                        href="{{ route($m['rute']) }}"
                                        class="flex items-center gap-3 rounded-lg border-l-4 px-3 py-2 text-sm transition {{ $aktif ? 'border-accent-400 bg-white/10 font-medium text-white' : 'border-transparent text-brand-100 hover:bg-white/5 hover:text-white' }}">

                                        <svg
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.6"
                                            stroke="currentColor"
                                            aria-hidden="true">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="{{ $ikon[$m['ikon']] }}" />
                                        </svg>

                                        {{ $m['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach
        </nav>

        {{-- Kartu pengguna --}}
        <div class="border-t border-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold">
                    {{ mb_strtoupper(mb_substr($pengguna->name, 0, 1)) }}
                </div>

                <div class="min-w-0">
                    <div class="truncate text-sm font-medium">
                        {{ $pengguna->name }}
                    </div>

                    <span class="mt-0.5 inline-block rounded-full px-2 py-0.5 text-[11px] font-medium {{ $superadmin ? 'bg-accent-400 text-brand-950' : 'bg-white/15 text-white' }}">
                        {{ ucfirst($peran) }}
                    </span>
                </div>
            </div>

            {{-- Menu profil, password, dan keluar --}}
            <div class="mt-3 space-y-1">

                {{-- PROFIL SAYA --}}
                <a
                    href="{{ route('profil.edit') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('profil.edit', 'profil.update') ? 'bg-white/10 text-white' : 'text-brand-100 hover:bg-white/5 hover:text-white' }}">

                    <svg
                        class="h-5 w-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.6"
                        stroke="currentColor"
                        aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="{{ $ikon['akun'] }}" />
                    </svg>

                    Profil Saya
                </a>

                {{-- GANTI PASSWORD --}}
                <a
                    href="{{ route('profil.password') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('profil.password', 'profil.password.update') ? 'bg-white/10 text-white' : 'text-brand-100 hover:bg-white/5 hover:text-white' }}">

                    <svg
                        class="h-5 w-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.6"
                        stroke="currentColor"
                        aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="{{ $ikon['kunci'] }}" />
                    </svg>

                    Ganti password
                </a>

                {{-- KELUAR --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-red-200 transition hover:bg-red-500/10 hover:text-red-100">

                        <svg
                            class="h-5 w-5 shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.6"
                            stroke="currentColor"
                            aria-hidden="true">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="{{ $ikon['keluar'] }}" />
                        </svg>

                        Keluar
                    </button>
                </form>

            </div>
        </div>
    </aside>

    {{-- Konten utama --}}
    <div class="lg:pl-64">

        {{-- Header khusus HP --}}
        <header class="sticky top-0 z-20 flex items-center gap-3 bg-brand-900 px-4 py-3 text-white shadow lg:hidden">
            <button
                type="button"
                id="menu-buka"
                class="rounded-lg p-1.5 hover:bg-white/10"
                aria-label="Buka menu">

                <svg
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    aria-hidden="true">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="{{ $ikon['menu'] }}" />
                </svg>
            </button>

            <span class="truncate text-sm font-semibold">
                {{ $namaApp }}
            </span>
        </header>

        <main class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">

            {{-- Notifikasi sukses --}}
            @if (session('pesan'))
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">

                    <svg
                        class="mt-0.5 h-5 w-5 shrink-0 text-green-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="{{ $ikon['centang'] }}" />
                    </svg>

                    <span>{{ session('pesan') }}</span>
                </div>
            @endif

            {{-- Isi halaman --}}
            @yield('content')

            {{-- Footer --}}
            <footer class="mt-10 border-t border-slate-200 pt-4 text-xs text-slate-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </footer>

        </main>
    </div>

    {{-- JavaScript menu HP --}}
    <script>
        (function () {
            var menu = document.getElementById('menu-samping');
            var lapisan = document.getElementById('menu-lapisan');
            var buka = document.getElementById('menu-buka');

            function atur(terbuka) {
                menu.classList.toggle('-translate-x-full', !terbuka);
                lapisan.classList.toggle('hidden', !terbuka);
            }

            if (buka) {
                buka.addEventListener('click', function () {
                    atur(true);
                });
            }

            if (lapisan) {
                lapisan.addEventListener('click', function () {
                    atur(false);
                });
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>
