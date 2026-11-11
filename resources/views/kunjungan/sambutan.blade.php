@extends('layouts.tamu')

@php
    $beranda = \App\Models\PengaturanBeranda::ambil();
    $daftarLayanan = $beranda->daftarLayananArray();
    $daftarLangkah = $beranda->daftarLangkahArray();
@endphp

@section('title', 'Selamat Datang')
@section('subjudul', $beranda->subjudul)
@section('ticker')
    @includeIf('partials.berita-ticker')
@endsection

@section('content')
<div class="sm-kartu">

    <div class="sm-ikon">
        <svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M7 11.5V5a2 2 0 0 1 4 0v5.5"/><path d="M11 10V4a2 2 0 0 1 4 0v7"/><path d="M15 11V6a2 2 0 0 1 4 0v8a7 7 0 0 1-7 7h-1a7 7 0 0 1-6-3.5L3 14a2 2 0 0 1 3.4-2L7 13"/>
        </svg>
    </div>

    <h2 class="sm-judul">{{ $beranda->judul_sambutan }}</h2>

    <p class="sm-teks">{{ $beranda->teks_sambutan }}</p>

    @if (count($daftarLayanan))
        <p class="sm-label">Layanan kami</p>
        <div class="sm-chip-wadah">
            @foreach ($daftarLayanan as $layanan)
                <span class="sm-chip">{{ $layanan }}</span>
            @endforeach
        </div>
    @endif

    @if (count($daftarLangkah))
        <p class="sm-label">Langkah singkat</p>
        <ol class="sm-langkah">
            @foreach ($daftarLangkah as $i => $langkah)
                <li><span>{{ $i + 1 }}</span> {{ $langkah }}</li>
            @endforeach
        </ol>
    @endif

    <a href="{{ route('kunjungan.create') }}" class="sm-tombol">Mulai isi buku tamu</a>

    <p class="sm-catatan">{{ $beranda->teks_privasi }}</p>
</div>

{{-- Berita terbaru (tidak error bila file partial belum dibuat) --}}
@includeIf('partials.berita-terbaru')
@includeIf('partials.galeri-beranda')

<style>
    .sm-kartu { padding: 2rem 1.5rem; text-align: center; background: #fff; border-radius: .75rem; box-shadow: 0 1px 3px rgba(15, 23, 42, .12); }
    .sm-ikon { display: inline-flex; align-items: center; justify-content: center; width: 4.5rem; height: 4.5rem; border-radius: 999px; background: #eef2ff; color: #1e2a78; }
    .sm-judul { margin: 1rem 0 0; font-size: 1.6rem; font-weight: 800; color: #1e2a78; }
    .sm-teks { margin: .6rem auto 0; max-width: 34rem; font-size: .95rem; line-height: 1.6; color: #475569; }
    .sm-label { margin: 1.6rem 0 .6rem; font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #94a3b8; }
    .sm-chip-wadah { display: flex; flex-wrap: wrap; gap: .5rem; justify-content: center; }
    .sm-chip { padding: .3rem .8rem; font-size: .8rem; color: #1e2a78; background: #eef2ff; border-radius: 999px; }
    .sm-langkah { display: inline-block; margin: 0; padding: 0; text-align: left; list-style: none; }
    .sm-langkah li { display: flex; align-items: center; gap: .7rem; margin-top: .55rem; font-size: .9rem; color: #334155; }
    .sm-langkah li span { display: inline-flex; flex: none; align-items: center; justify-content: center; width: 1.7rem; height: 1.7rem; font-size: .8rem; font-weight: 700; color: #fff; background: #1e2a78; border-radius: 999px; }
    .sm-tombol { display: block; margin: 2rem auto 0; max-width: 22rem; padding: .85rem 1rem; font-size: 1rem; font-weight: 700; color: #fff; text-decoration: none; background: #2563eb; border-radius: .6rem; }
    .sm-tombol:hover { background: #1d4ed8; }
    .sm-catatan { margin: 1rem 0 0; font-size: .75rem; color: #94a3b8; }
</style>
@endsection