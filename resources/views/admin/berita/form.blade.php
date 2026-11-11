@extends('layouts.app')

@section('title', ($berita->exists ? 'Ubah' : 'Tambah') . ' Berita | Buku Tamu')

@section('content')
<h1 class="mb-4 text-xl font-semibold text-slate-800">{{ $berita->exists ? 'Ubah berita' : 'Tambah berita' }}</h1>

@if ($errors->any())
    <div class="mb-4 rounded-lg bg-white p-3 text-sm text-red-700 shadow">
        <ul>
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ $berita->exists ? route('berita.kelola.update', $berita) : route('berita.kelola.store') }}"
      enctype="multipart/form-data"
      class="mb-4 max-w-3xl rounded-xl bg-white p-4 shadow">
    @csrf
    @if ($berita->exists)
        @method('PUT')
    @endif

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Judul</label>
        <input type="text" name="judul" value="{{ old('judul', $berita->judul) }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Ringkasan (maks. 300 karakter)</label>
        <textarea name="ringkasan" rows="2"
                  class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('ringkasan', $berita->ringkasan) }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Isi berita</label>
        <textarea name="isi" rows="10"
                  class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('isi', $berita->isi) }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Foto sampul (JPG/PNG/WEBP, maks. 4 MB)</label>
        @if ($berita->gambarUrl())
            <img src="{{ $berita->gambarUrl() }}" alt="" class="mt-2 max-h-32 rounded">
            <label class="mt-2 block text-sm text-slate-700">
                <input type="checkbox" name="hapus_gambar" value="1"> Hapus foto
            </label>
        @endif
        <input type="file" name="gambar" accept="image/*" class="mt-2 block text-sm">
    </div>

    <div class="mb-4">
        <label class="text-sm text-slate-700">
            <input type="checkbox" name="terbit" value="1" @checked(old('terbit', $berita->terbit))>
            Terbitkan (tampil untuk umum)
        </label>
    </div>

    <div class="flex gap-2">
        <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan</button>
        <a href="{{ route('berita.kelola.index') }}"
           class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Batal</a>
    </div>
</form>
@endsection