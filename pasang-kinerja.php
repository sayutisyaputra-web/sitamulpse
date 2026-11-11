<?php

/*
 * PEMASANG HALAMAN KINERJA PETUGAS
 * Jalankan SEKALI dari folder proyek:  php pasang-kinerja.php
 *
 *  - Halaman baru "Kinerja petugas" untuk pimpinan (admin dan superadmin juga bisa)
 *  - Grafik: batang bertumpuk per petugas, donat porsi layanan, garis tren harian,
 *    tabel peringkat dengan sparkline dan kelengkapan foto bukti check-out
 *  - Menambah menu "Kinerja petugas" di sidebar dan satu route
 *
 * File yang diubah dicadangkan dulu dengan akhiran .bak-kinerja
 */

$root = __DIR__;

if (! is_file($root.'/artisan')) {
    exit("Jalankan file ini dari folder proyek (C:\\laragon\\www\\buku-tamu).\n");
}

$berkas = [

'app/Http/Controllers/KinerjaPetugasController.php' => <<<'AKHIR_BERKAS'
<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Kunjungan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KinerjaPetugasController extends Controller
{
    private const WARNA = ['#1e2a78', '#2563eb', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#64748b'];

    // Maksimal rentang tanggal agar grafik tetap terbaca
    private const MAKS_HARI = 92;

    public function index(Request $request): View
    {
        $request->validate([
            'dari'      => ['nullable', 'date'],
            'sampai'    => ['nullable', 'date', 'after_or_equal:dari'],
            'bidang_id' => ['nullable', 'integer'],
        ]);

        $sampai = $request->filled('sampai')
            ? Carbon::parse($request->input('sampai'))->endOfDay()
            : now()->endOfDay();

        $dari = $request->filled('dari')
            ? Carbon::parse($request->input('dari'))->startOfDay()
            : $sampai->copy()->subDays(29)->startOfDay();

        if ($dari->greaterThan($sampai)) {
            $dari = $sampai->copy()->startOfDay();
        }

        if ($dari->diffInDays($sampai) > self::MAKS_HARI - 1) {
            $dari = $sampai->copy()->subDays(self::MAKS_HARI - 1)->startOfDay();
        }

        $tanggal = [];
        for ($t = $dari->copy(); $t->lte($sampai); $t->addDay()) {
            $tanggal[] = $t->toDateString();
        }

        $bidangId = $request->filled('bidang_id') ? (int) $request->input('bidang_id') : null;

        $kunjungans = Kunjungan::query()
            ->whereNotNull('petugas_id')
            ->whereBetween('waktu_masuk', [$dari, $sampai])
            ->when($bidangId, fn ($q) => $q->where('bidang_id', $bidangId))
            ->get(['id', 'petugas_id', 'bidang_id', 'status', 'waktu_masuk', 'waktu_keluar', 'foto_keluar_path']);

        $tanpaPetugas = Kunjungan::query()
            ->whereNull('petugas_id')
            ->whereBetween('waktu_masuk', [$dari, $sampai])
            ->when($bidangId, fn ($q) => $q->where('bidang_id', $bidangId))
            ->count();

        $perPetugas = $kunjungans->groupBy('petugas_id');
        $semuaDurasi = collect();
        $baris = [];

        foreach (User::where('role', 'petugas')->orderBy('name')->get(['id', 'name', 'foto', 'aktif']) as $p) {
            $data = $perPetugas->get($p->id, collect());

            // Petugas nonaktif hanya tampil bila punya kunjungan pada periode ini
            if (! $p->aktif && $data->isEmpty()) {
                continue;
            }

            $ditolak = $data->where('status', 'ditolak')->count();

            $tuntas = $data->filter(fn ($k) => $k->waktu_keluar !== null && $k->status !== 'ditolak');
            $selesai = $tuntas->count();
            $melayani = $data->count() - $selesai - $ditolak;

            $durasi = $tuntas->map(fn ($k) => abs((int) $k->waktu_masuk->diffInMinutes($k->waktu_keluar)));
            $semuaDurasi = $semuaDurasi->concat($durasi);

            $denganFoto = $tuntas->filter(fn ($k) => ! empty($k->foto_keluar_path))->count();

            $perHari = $data->groupBy(fn ($k) => $k->waktu_masuk->toDateString())->map->count();

            $baris[] = [
                'id'         => $p->id,
                'nama'       => $p->name,
                'foto'       => $p->foto_url,
                'aktif'      => (bool) $p->aktif,
                'total'      => $data->count(),
                'selesai'    => $selesai,
                'melayani'   => max(0, $melayani),
                'ditolak'    => $ditolak,
                'durasi'     => $durasi->isNotEmpty() ? (int) round($durasi->avg()) : null,
                'denganFoto' => $denganFoto,
                'bukti'      => $selesai > 0 ? (int) round($denganFoto / $selesai * 100) : null,
                'harian'     => array_map(fn ($tgl) => (int) $perHari->get($tgl, 0), $tanggal),
            ];
        }

        usort($baris, fn ($a, $b) => [$b['total'], $a['nama']] <=> [$a['total'], $b['nama']]);

        foreach ($baris as $i => $b) {
            $baris[$i]['warna'] = self::WARNA[$i % count(self::WARNA)];
        }

        $total = array_sum(array_column($baris, 'total'));
        $selesaiSemua = array_sum(array_column($baris, 'selesai'));
        $fotoSemua = array_sum(array_column($baris, 'denganFoto'));

        return view('kinerja.index', [
            'baris'    => $baris,
            'tanggal'  => $tanggal,
            'dari'     => $dari,
            'sampai'   => $sampai,
            'bidangs'  => Bidang::orderBy('nama')->get(['id', 'nama']),
            'bidangId' => $bidangId,
            'ringkas'  => [
                'total'          => $total,
                'selesai'        => $selesaiSemua,
                'melayani'       => array_sum(array_column($baris, 'melayani')),
                'ditolak'        => array_sum(array_column($baris, 'ditolak')),
                'rataDurasi'     => $semuaDurasi->isNotEmpty() ? (int) round($semuaDurasi->avg()) : null,
                'denganFoto'     => $fotoSemua,
                'persenBukti'    => $selesaiSemua > 0 ? (int) round($fotoSemua / $selesaiSemua * 100) : null,
                'jumlahPetugas'  => count($baris),
                'tanpaPetugas'   => $tanpaPetugas,
            ],
        ]);
    }
}
AKHIR_BERKAS,

'resources/views/kinerja/index.blade.php' => <<<'AKHIR_BERKAS'
@extends('layouts.app')

@section('title', 'Kinerja Petugas | Buku Tamu')

@section('content')
@php
    $total = $ringkas['total'];
    $ada = $total > 0;
    $n = count($tanggal);
    $persenSelesai = $total > 0 ? (int) round($ringkas['selesai'] / $total * 100) : 0;

    $teksDurasi = function (?int $menit) {
        if ($menit === null) {
            return '-';
        }

        return $menit >= 60 ? intdiv($menit, 60) . ' j ' . ($menit % 60) . ' m' : $menit . ' menit';
    };

    $rentang = [
        '7 hari'    => [now()->subDays(6)->toDateString(), now()->toDateString()],
        '30 hari'   => [now()->subDays(29)->toDateString(), now()->toDateString()],
        '90 hari'   => [now()->subDays(89)->toDateString(), now()->toDateString()],
        'Bulan ini' => [now()->startOfMonth()->toDateString(), now()->toDateString()],
    ];

    $kartu = [
        [
            'label' => 'Tamu dilayani',
            'nilai' => $total,
            'ket'   => $ringkas['jumlahPetugas'] . ' petugas aktif melayani',
            'latar' => 'bg-brand-50 text-brand-700',
            'ikon'  => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        ],
        [
            'label' => 'Layanan selesai',
            'nilai' => $ringkas['selesai'],
            'ket'   => $persenSelesai . '% dari seluruh tamu',
            'latar' => 'bg-emerald-50 text-emerald-700',
            'ikon'  => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'label' => 'Rata-rata durasi layanan',
            'nilai' => $teksDurasi($ringkas['rataDurasi']),
            'ket'   => 'dihitung dari masuk sampai check-out',
            'latar' => 'bg-amber-50 text-amber-700',
            'ikon'  => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'label' => 'Kelengkapan foto bukti',
            'nilai' => $ringkas['persenBukti'] === null ? '-' : $ringkas['persenBukti'] . '%',
            'ket'   => $ringkas['denganFoto'] . ' dari ' . $ringkas['selesai'] . ' check-out berfoto',
            'latar' => 'bg-sky-50 text-sky-700',
            'ikon'  => 'M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316zM16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z',
        ],
    ];
@endphp

{{-- Judul dan rentang cepat --}}
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="ui-title">Kinerja petugas</h1>
        <p class="mt-1 text-sm text-slate-500">
            {{ $dari->copy()->locale('id')->translatedFormat('d F Y') }} &ndash; {{ $sampai->copy()->locale('id')->translatedFormat('d F Y') }}
        </p>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach ($rentang as $label => $tgl)
            @php($rentangAktif = $dari->toDateString() === $tgl[0] && $sampai->toDateString() === $tgl[1])
            <a href="{{ route('kinerja.index', array_filter(['dari' => $tgl[0], 'sampai' => $tgl[1], 'bidang_id' => $bidangId])) }}"
               class="ui-btn !px-3 !py-1.5 {{ $rentangAktif ? 'bg-brand-700 text-white' : 'ui-btn-secondary' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

@if ($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

{{-- Filter --}}
<form method="GET" action="{{ route('kinerja.index') }}" class="ui-card mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
    <div>
        <label class="ui-label">Dari tanggal</label>
        <input type="date" name="dari" value="{{ $dari->toDateString() }}" class="ui-field">
    </div>
    <div>
        <label class="ui-label">Sampai tanggal</label>
        <input type="date" name="sampai" value="{{ $sampai->toDateString() }}" class="ui-field">
    </div>
    <div class="lg:col-span-2">
        <label class="ui-label">Bidang</label>
        <select name="bidang_id" class="ui-field">
            <option value="">Semua bidang</option>
            @foreach ($bidangs as $bidang)
                <option value="{{ $bidang->id }}" @selected($bidangId === $bidang->id)>{{ $bidang->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button class="ui-btn ui-btn-primary">Terapkan</button>
        <a href="{{ route('kinerja.index') }}" class="ui-btn ui-btn-secondary">Reset</a>
    </div>
</form>

{{-- Kartu ringkasan --}}
<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ($kartu as $k)
        <div class="ui-card flex items-center gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $k['latar'] }}">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $k['ikon'] }}" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500">{{ $k['label'] }}</p>
                <p class="mt-0.5 text-2xl font-semibold text-slate-900">{{ $k['nilai'] }}</p>
                <p class="text-xs text-slate-400">{{ $k['ket'] }}</p>
            </div>
        </div>
    @endforeach
</div>

@if ($ringkas['tanpaPetugas'] > 0)
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
        {{ $ringkas['tanpaPetugas'] }} kunjungan pada periode ini masuk antrean bidang tanpa petugas (semua petugas sedang melayani), sehingga tidak masuk hitungan di bawah.
    </div>
@endif

@if (! $ada)
    <div class="ui-card py-14 text-center">
        <p class="text-sm font-medium text-slate-700">Belum ada kunjungan yang ditangani petugas pada periode ini</p>
        <p class="mt-1 text-sm text-slate-500">Ubah rentang tanggal atau bidang untuk melihat kinerja petugas.</p>
    </div>
@else
    @php
        $maksTotal = max(1, collect($baris)->max('total'));
    @endphp

    <div class="mb-6 grid gap-6 lg:grid-cols-3">
        {{-- Batang bertumpuk: beban dan hasil per petugas --}}
        <div class="ui-card lg:col-span-2">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-sm font-semibold text-brand-900">Beban dan hasil layanan per petugas</h2>
                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm" style="background:#10b981"></span>Selesai</span>
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm" style="background:#f59e0b"></span>Belum selesai</span>
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm" style="background:#ef4444"></span>Ditolak</span>
                </div>
            </div>

            @foreach ($baris as $b)
                <div class="mb-4 last:mb-0">
                    <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                        <span class="flex min-w-0 items-center gap-2 text-slate-700">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $b['warna'] }}"></span>
                            <span class="truncate">{{ $b['nama'] }}</span>
                        </span>
                        <span class="shrink-0 text-slate-500">
                            <span class="font-semibold text-slate-900">{{ $b['total'] }}</span> tamu
                        </span>
                    </div>

                    <div class="h-3 rounded-full bg-slate-100">
                        <div class="flex h-3 overflow-hidden rounded-full" style="width: {{ round($b['total'] / $maksTotal * 100) }}%">
                            @if ($b['total'] > 0)
                                <div style="width: {{ $b['selesai'] / $b['total'] * 100 }}%; background:#10b981" title="Selesai: {{ $b['selesai'] }}"></div>
                                <div style="width: {{ $b['melayani'] / $b['total'] * 100 }}%; background:#f59e0b" title="Belum selesai: {{ $b['melayani'] }}"></div>
                                <div style="width: {{ $b['ditolak'] / $b['total'] * 100 }}%; background:#ef4444" title="Ditolak: {{ $b['ditolak'] }}"></div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Donat: porsi layanan --}}
        <div class="ui-card">
            <h2 class="mb-4 text-sm font-semibold text-brand-900">Porsi layanan per petugas</h2>

            @php
                $jari = 15.91549430918954;
                $kumulatif = 0;
            @endphp

            <div class="flex justify-center">
                <svg viewBox="0 0 42 42" class="h-44 w-44" role="img" aria-label="Porsi layanan per petugas">
                    <circle cx="21" cy="21" r="{{ $jari }}" fill="none" stroke="#f1f5f9" stroke-width="5"></circle>
                    @foreach ($baris as $b)
                        @if ($b['total'] > 0)
                            @php($porsi = $b['total'] / $total * 100)
                            <circle cx="21" cy="21" r="{{ $jari }}" fill="none" stroke="{{ $b['warna'] }}" stroke-width="5"
                                    stroke-dasharray="{{ round($porsi, 2) }} {{ round(100 - $porsi, 2) }}"
                                    stroke-dashoffset="{{ round(25 - $kumulatif, 2) }}">
                                <title>{{ $b['nama'] }}: {{ $b['total'] }} tamu ({{ round($porsi) }}%)</title>
                            </circle>
                            @php($kumulatif += $porsi)
                        @endif
                    @endforeach
                    <text x="21" y="21.5" text-anchor="middle" font-size="7" font-weight="700" fill="#1e2a78">{{ $total }}</text>
                    <text x="21" y="26.5" text-anchor="middle" font-size="2.8" fill="#64748b">tamu</text>
                </svg>
            </div>

            <ul class="mt-4 space-y-2 text-sm">
                @foreach ($baris as $b)
                    <li class="flex items-center justify-between gap-3">
                        <span class="flex min-w-0 items-center gap-2 text-slate-600">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $b['warna'] }}"></span>
                            <span class="truncate">{{ $b['nama'] }}</span>
                        </span>
                        <span class="shrink-0 text-slate-500">
                            <span class="font-semibold text-slate-900">{{ $b['total'] }}</span>
                            &middot; {{ $total > 0 ? round($b['total'] / $total * 100) : 0 }}%
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Garis: tren harian --}}
    @php
        $lebar = 760;
        $tinggi = 250;
        $kiri = 36;
        $kanan = 14;
        $atas = 12;
        $bawah = 28;
        $lebarPlot = $lebar - $kiri - $kanan;
        $tinggiPlot = $tinggi - $atas - $bawah;

        $maksHari = max(1, collect($baris)->flatMap(fn ($b) => $b['harian'])->max());
        $skala = max(4, (int) ceil($maksHari / 4) * 4);
        $langkah = max(1, (int) ceil($n / 8));

        $px = fn ($i) => $kiri + ($n > 1 ? $i / ($n - 1) * $lebarPlot : $lebarPlot / 2);
        $py = fn ($v) => $atas + $tinggiPlot - ($v / $skala * $tinggiPlot);

        $seri = array_slice(array_values(array_filter($baris, fn ($b) => $b['total'] > 0)), 0, 6);
    @endphp

    <div class="ui-card mb-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-sm font-semibold text-brand-900">Tren kunjungan harian per petugas</h2>
            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
                @foreach ($seri as $s)
                    <span class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-sm" style="background: {{ $s['warna'] }}"></span>{{ $s['nama'] }}
                    </span>
                @endforeach
            </div>
        </div>

        <svg viewBox="0 0 {{ $lebar }} {{ $tinggi }}" class="h-auto w-full" role="img" aria-label="Tren kunjungan harian per petugas">
            @foreach ([0, 1, 2, 3, 4] as $g)
                @php($nilaiGaris = (int) round($skala * $g / 4))
                <line x1="{{ $kiri }}" x2="{{ $lebar - $kanan }}" y1="{{ round($py($nilaiGaris), 1) }}" y2="{{ round($py($nilaiGaris), 1) }}"
                      stroke="#e2e8f0" stroke-dasharray="{{ $g === 0 ? '0' : '4 4' }}"></line>
                <text x="{{ $kiri - 8 }}" y="{{ round($py($nilaiGaris) + 3, 1) }}" text-anchor="end" font-size="10" fill="#94a3b8">{{ $nilaiGaris }}</text>
            @endforeach

            @foreach ($tanggal as $i => $tgl)
                @if ($i % $langkah === 0)
                    <text x="{{ round($px($i), 1) }}" y="{{ $tinggi - 8 }}" text-anchor="middle" font-size="10" fill="#94a3b8">{{ \Carbon\Carbon::parse($tgl)->format('d/m') }}</text>
                @endif
            @endforeach

            @foreach ($seri as $s)
                @php($titikSeri = collect($s['harian'])->map(fn ($v, $i) => round($px($i), 1) . ',' . round($py($v), 1))->implode(' '))
                <polyline points="{{ $titikSeri }}" fill="none" stroke="{{ $s['warna'] }}" stroke-width="2.5"
                          stroke-linejoin="round" stroke-linecap="round"></polyline>

                @foreach ($s['harian'] as $i => $v)
                    @if ($v > 0)
                        <circle cx="{{ round($px($i), 1) }}" cy="{{ round($py($v), 1) }}" r="3.5" fill="#fff" stroke="{{ $s['warna'] }}" stroke-width="2">
                            <title>{{ $s['nama'] }}, {{ \Carbon\Carbon::parse($tanggal[$i])->format('d/m/Y') }}: {{ $v }} tamu</title>
                        </circle>
                    @endif
                @endforeach
            @endforeach
        </svg>

        @if (count($baris) > count($seri))
            <p class="mt-2 text-xs text-slate-400">Grafik menampilkan {{ count($seri) }} petugas dengan tamu terbanyak.</p>
        @endif
    </div>

    {{-- Tabel peringkat --}}
    <div class="ui-card overflow-hidden !p-0">
        <div class="px-5 py-4">
            <h2 class="text-sm font-semibold text-brand-900">Peringkat dan rincian petugas</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-y border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">Petugas</th>
                        <th class="px-5 py-3 text-right font-medium">Tamu</th>
                        <th class="px-5 py-3 text-right font-medium">Selesai</th>
                        <th class="px-5 py-3 text-right font-medium">Belum selesai</th>
                        <th class="px-5 py-3 text-right font-medium">Ditolak</th>
                        <th class="px-5 py-3 font-medium">Rata-rata durasi</th>
                        <th class="px-5 py-3 font-medium">Foto bukti</th>
                        <th class="px-5 py-3 font-medium">Tren</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($baris as $b)
                        @php
                            $sm = max(1, max($b['harian']));
                            $titik = [];
                            foreach ($b['harian'] as $i => $v) {
                                $sx = $n > 1 ? round($i / ($n - 1) * 100, 1) : 50;
                                $sy = round(28 - ($v / $sm) * 24 - 2, 1);
                                $titik[] = $sx . ',' . $sy;
                            }

                            $warnaBukti = $b['bukti'] === null ? '#cbd5e1' : ($b['bukti'] >= 90 ? '#10b981' : ($b['bukti'] >= 60 ? '#f59e0b' : '#ef4444'));
                        @endphp
                        <tr class="align-middle hover:bg-brand-50/40">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-700">{{ $loop->iteration }}</span>
                                    <img src="{{ $b['foto'] }}" alt="Foto {{ $b['nama'] }}" class="h-9 w-9 shrink-0 rounded-full object-cover ring-1 ring-slate-200">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-slate-900">{{ $b['nama'] }}</p>
                                        @if (! $b['aktif'])
                                            <p class="text-xs text-slate-400">Nonaktif</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-900">{{ $b['total'] }}</td>
                            <td class="px-5 py-3 text-right text-emerald-700">{{ $b['selesai'] }}</td>
                            <td class="px-5 py-3 text-right text-amber-700">{{ $b['melayani'] }}</td>
                            <td class="px-5 py-3 text-right text-red-600">{{ $b['ditolak'] }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-slate-700">{{ $teksDurasi($b['durasi']) }}</td>
                            <td class="px-5 py-3">
                                @if ($b['bukti'] === null)
                                    <span class="text-slate-400">-</span>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-24 rounded-full bg-slate-100">
                                            <div class="h-2 rounded-full" style="width: {{ $b['bukti'] }}%; background: {{ $warnaBukti }}"></div>
                                        </div>
                                        <span class="text-xs font-medium text-slate-600">{{ $b['bukti'] }}%</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <svg viewBox="0 0 100 28" preserveAspectRatio="none" class="h-7 w-28" aria-hidden="true">
                                    <polyline points="{{ implode(' ', $titik) }}" fill="none" stroke="{{ $b['warna'] }}" stroke-width="2"
                                              stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke"></polyline>
                                </svg>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
AKHIR_BERKAS,

];

// ---------------------------------------------------------------- menulis file
echo "\n== Memasang file ==\n";

foreach ($berkas as $rel => $isi) {
    $path = $root.'/'.$rel;

    if (! is_dir(dirname($path))) {
        mkdir(dirname($path), 0777, true);
    }

    if (is_file($path) && ! is_file($path.'.bak-kinerja')) {
        copy($path, $path.'.bak-kinerja');
    }

    file_put_contents($path, $isi."\n");
    echo "OK      $rel\n";
}

// ---------------------------------------------------------------- route
$web = $root.'/routes/web.php';

if (is_file($web)) {
    $teks = file_get_contents($web);

    if (str_contains($teks, 'kinerja.index')) {
        echo "LEWATI  routes/web.php (route kinerja sudah ada)\n";
    } else {
        $baru = preg_replace_callback(
            '/Route::middleware\(\'auth\'\)->group\(function \(\) \{/',
            function ($m) {
                return $m[0]
                    ."\n\n    // Kinerja petugas (pimpinan, admin, superadmin)"
                    ."\n    Route::get('/kinerja-petugas', [\\App\\Http\\Controllers\\KinerjaPetugasController::class, 'index'])"
                    ."\n        ->middleware('role:admin,pimpinan')"
                    ."\n        ->name('kinerja.index');\n";
            },
            $teks,
            1,
            $jumlah
        );

        if ($jumlah === 1) {
            if (! is_file($web.'.bak-kinerja')) {
                copy($web, $web.'.bak-kinerja');
            }
            file_put_contents($web, $baru);
            echo "OK      routes/web.php (route /kinerja-petugas)\n";
        } else {
            echo "!!      routes/web.php tidak diubah: kelompok route 'auth' tidak ditemukan.\n";
            echo "        Kirim file itu ke Claude.\n";
        }
    }
}

// ---------------------------------------------------------------- menu samping
$layout = $root.'/resources/views/layouts/app.blade.php';

if (is_file($layout)) {
    $teks = file_get_contents($layout);

    if (str_contains($teks, 'kinerja.index')) {
        echo "LEWATI  layouts/app.blade.php (menu kinerja sudah ada)\n";
    } else {
        $ikonGrafik = 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z';

        $n1 = 0;
        $n2 = 0;

        $baru = preg_replace_callback(
            '/\$ikon\s*=\s*\[/',
            fn ($m) => $m[0]."\n            'grafik' => '".$ikonGrafik."',",
            $teks,
            1,
            $n1
        );

        $baru = preg_replace_callback(
            '/(\r?\n)([ \t]*)(\'Pengelolaan\'\s*=>\s*\[)/',
            function ($m) {
                $e = $m[1];
                $s = $m[2];

                return $e.$s."'Pemantauan' => ["
                    .$e.$s."    ["
                    .$e.$s."        'label' => 'Kinerja petugas',"
                    .$e.$s."        'rute' => 'kinerja.index',"
                    .$e.$s."        'aktif' => 'kinerja.*',"
                    .$e.$s."        'ikon' => 'grafik',"
                    .$e.$s."        'tampil' => in_array(\$peran, ['admin', 'pimpinan', 'superadmin'], true),"
                    .$e.$s."    ],"
                    .$e.$s."],"
                    .$e
                    .$e.$s.$m[3];
            },
            $baru,
            1,
            $n2
        );

        if ($n1 === 1 && $n2 === 1) {
            if (! is_file($layout.'.bak-kinerja')) {
                copy($layout, $layout.'.bak-kinerja');
            }
            file_put_contents($layout, $baru);
            echo "OK      resources/views/layouts/app.blade.php (menu Kinerja petugas)\n";
        } else {
            echo "!!      layouts/app.blade.php tidak diubah: bagian menu tidak ditemukan.\n";
            echo "        Kirim file itu ke Claude agar menu bisa dipasang.\n";
        }
    }
}

// ---------------------------------------------------------------- perintah lanjutan
function jalankan(string $judul, string $perintah): void
{
    echo "\n== $judul ==\n";
    passthru($perintah, $kode);

    if ($kode !== 0) {
        echo "\n!! Langkah ini gagal (kode $kode). Salin pesan di atas dan kirim ke Claude.\n";
    }
}

$php = escapeshellarg(PHP_BINARY);

jalankan('Membersihkan cache', "$php artisan optimize:clear");
jalankan('Route kinerja', "$php artisan route:list --path=kinerja");
jalankan('Membangun tampilan (npm run build)', 'npm run build');

@unlink(__FILE__);

echo "\n== SELESAI ==\n";
echo "Masuk sebagai pimpinan: menu Kinerja petugas ada di sidebar (grup Pemantauan).\n";
echo "Cadangan file lama memakai akhiran .bak-kinerja\n";
