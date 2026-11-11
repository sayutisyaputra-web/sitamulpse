@extends('layouts.app')

@section('title', 'Daftar Tamu | Buku Tamu')

@section('content')
@php
    $filterAktif = request()->only(['q', 'dari', 'sampai', 'layanan', 'status']);
    $bisaProses = auth()->user()->bisaProses();
    $idSaya = (int) auth()->id();
@endphp

<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="ui-title">Daftar tamu</h1>
        <p class="mt-1 text-sm text-slate-500">{{ number_format($kunjungans->total(), 0, ',', '.') }} kunjungan ditemukan</p>
        @if (auth()->user()->role === 'petugas')
            <p class="mt-0.5 text-xs text-slate-400">Ekspor Excel dan PDF hanya memuat tiket yang Anda ambil atau tangani.</p>
        @endif
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('petugas.kunjungan.export.excel', $filterAktif) }}" class="ui-btn ui-btn-secondary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Ekspor Excel
        </a>
        <a href="{{ route('petugas.kunjungan.export.pdf', $filterAktif) }}" class="ui-btn ui-btn-secondary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Ekspor PDF
        </a>
    </div>
</div>

<form method="GET" action="{{ route('petugas.kunjungan.index') }}" class="ui-card mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
    <div class="lg:col-span-2">
        <label class="ui-label">Cari</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama, no. kunjungan, instansi, no. identitas" class="ui-field">
    </div>
    <div>
        <label class="ui-label">Dari tanggal</label>
        <input type="date" name="dari" value="{{ request('dari') }}" class="ui-field">
    </div>
    <div>
        <label class="ui-label">Sampai tanggal</label>
        <input type="date" name="sampai" value="{{ request('sampai') }}" class="ui-field">
    </div>
    <div>
        <label class="ui-label">Layanan</label>
        <select name="layanan" class="ui-field">
            <option value="">Semua</option>
            @foreach ($layanans as $kategori => $daftarSub)
                <option value="{{ $kategori }}" @selected(request('layanan') === $kategori)>{{ $kategori }}{{ $daftarSub ? ' (semua)' : '' }}</option>
                @foreach ($daftarSub as $sub)
                    <option value="{{ $kategori }}|{{ $sub }}" @selected(request('layanan') === $kategori . '|' . $sub)>{{ $kategori }} - {{ $sub }}</option>
                @endforeach
            @endforeach
        </select>
    </div>
    <div>
        <label class="ui-label">Status</label>
        <select name="status" class="ui-field">
            <option value="">Semua</option>
            @foreach ($statuses as $kunci => $label)
                <option value="{{ $kunci }}" @selected(request('status') === $kunci)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-6">
        <button class="ui-btn ui-btn-primary">Terapkan</button>
        <a href="{{ route('petugas.kunjungan.index') }}" class="ui-btn ui-btn-secondary">Reset</a>
    </div>
</form>

@if ($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">Filter tidak valid, silakan periksa kembali.</div>
@endif

<div class="ui-card overflow-hidden !p-0">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-3 py-3 font-medium">No. kunjungan</th>
                    <th class="px-3 py-3 font-medium">Masuk / Jadwal</th>
                    <th class="px-3 py-3 font-medium">Nama / Instansi</th>
                    <th class="px-3 py-3 font-medium">Layanan</th>
                    <th class="px-3 py-3 font-medium">Media</th>
                    <th class="px-3 py-3 font-medium">Petugas</th>
                    <th class="px-3 py-3 font-medium">Status</th>
                    <th class="px-3 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($kunjungans as $k)
                    <tr class="hover:bg-brand-50/40">
                        <td class="whitespace-nowrap px-3 py-3 font-mono text-xs text-slate-600">{{ $k->nomor_kunjungan }}</td>
                        <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                            <div>{{ $k->waktu_masuk->format('d/m/Y H:i') }}</div>
                            @if ($k->waktu_kedatangan)
                                <div class="text-xs text-slate-400">Jadwal {{ $k->waktu_kedatangan->format('d/m/Y H:i') }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            <div class="font-medium text-slate-900">{{ $k->nama }}</div>
                            <div class="text-xs text-slate-500">{{ $k->instansi ?: '-' }}</div>
                        </td>
                        <td class="px-3 py-3 text-slate-600">
                            @if ($k->kategori_layanan)
                                <div class="font-medium text-slate-800">{{ $k->kategori_layanan }}</div>
                                @if ($k->sub_layanan)
                                    <div class="text-xs text-slate-500">{{ $k->sub_layanan }}</div>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-3 text-slate-600">{{ $k->media_pelaporan ?: '-' }}</td>
                        <td class="whitespace-nowrap px-3 py-3">
                            @if ($k->petugas)
                                <span class="font-medium text-slate-800">{{ (int) $k->petugas_id === $idSaya ? 'Anda' : $k->petugas->name }}</span>
                            @else
                                <span class="text-xs text-slate-400">Antrean</span>
                            @endif
                        </td>
                        <td class="px-3 py-3">@include('kunjungan._status', ['status' => $k->status])</td>
                        <td class="px-3 py-3">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                @if ($bisaProses && $k->status === 'menunggu' && (int) $k->petugas_id !== $idSaya)
                                    <form method="POST" action="{{ route('petugas.kunjungan.ambil', $k) }}">
                                        @csrf
                                        <button type="submit" class="ui-btn ui-btn-primary whitespace-nowrap !px-3 !py-1.5">Ambil tiket</button>
                                    </form>
                                @endif
                                <a href="{{ route('petugas.kunjungan.show', $k) }}" class="ui-btn ui-btn-secondary !px-3 !py-1.5">Detail</a>
                                @if ($bisaProses && ! in_array($k->status, ['ditolak', 'hangus'], true))
                                    <a href="{{ route('petugas.kunjungan.cetak', $k) }}" target="_blank" class="ui-btn ui-btn-secondary !px-3 !py-1.5">Cetak</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-14 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-medium text-slate-700">Tidak ada data kunjungan</p>
                            <p class="mt-1 text-sm text-slate-500">Coba ubah atau reset filter pencarian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($kunjungans->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
            {{ $kunjungans->links() }}
        </div>
    @endif
</div>
@endsection
