@extends('layouts.app')

@section('title', 'Pengaturan Beranda | Buku Tamu')

@section('content')
<h1 class="mb-4 text-xl font-semibold text-slate-800">Pengaturan Beranda</h1>
<div class="mb-4 flex gap-2 text-sm">
    <a href="{{ route('berita.kelola.index') }}"
       class="rounded-lg bg-white px-3 py-2 text-blue-700 shadow">Kelola berita</a>
</div>

@if (session('status'))
    <div class="mb-4 rounded-lg bg-white p-3 text-sm text-green-700 shadow">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-lg bg-white p-3 text-sm text-red-700 shadow">
        <ul>
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('beranda.pengaturan.update') }}" enctype="multipart/form-data"
      class="mb-4 rounded-xl bg-white p-4 shadow">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Judul utama (bagian atas)</label>
        <input type="text" name="judul_utama" value="{{ old('judul_utama', $p->judul_utama) }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Subjudul</label>
        <input type="text" name="subjudul" value="{{ old('subjudul', $p->subjudul) }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Judul sambutan</label>
        <input type="text" name="judul_sambutan" value="{{ old('judul_sambutan', $p->judul_sambutan) }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Teks sambutan</label>
        <textarea name="teks_sambutan" rows="4"
                  class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('teks_sambutan', $p->teks_sambutan) }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Layanan Kami (satu layanan per baris)</label>
        <textarea name="daftar_layanan" rows="8"
                  class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('daftar_layanan', $p->daftar_layanan) }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Langkah Singkat (satu langkah per baris)</label>
        <textarea name="daftar_langkah" rows="4"
                  class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('daftar_langkah', $p->daftar_langkah) }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Teks privasi</label>
        <input type="text" name="teks_privasi" value="{{ old('teks_privasi', $p->teks_privasi) }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Teks footer</label>
        <input type="text" name="teks_footer" value="{{ old('teks_footer', $p->teks_footer) }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <div class="mb-4 border-t border-slate-200 pt-4">
        <label class="block text-xs font-medium text-slate-600">Logo (JPG/PNG/WEBP, maks. 2 MB; PNG transparan disarankan)</label>
        @if ($p->logoUrl())
            <img src="{{ $p->logoUrl() }}" alt="Logo" class="mt-2 max-h-16">
            <label class="mt-2 block text-sm text-slate-700">
                <input type="checkbox" name="hapus_logo" value="1"> Hapus logo (kembali ke bawaan)
            </label>
        @endif
        <input type="file" name="logo" accept="image/*" 
        class="mt-2 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100">
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Banner (JPG/PNG/WEBP, maks. 4 MB)</label>
        @if ($p->bannerUrl())
            <img src="{{ $p->bannerUrl() }}" alt="Banner" class="mt-2 max-h-32">
            <label class="mt-2 block text-sm text-slate-700">
                <input type="checkbox" name="hapus_banner" value="1"> Hapus banner (kembali ke bawaan)
            </label>
        @endif
        <input type="file" name="banner" accept="image/*" 
    </div>

    <button type="submit"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan</button>
</form>
@endsection