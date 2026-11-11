@extends('layouts.app')

@section('title', ($foto->exists ? 'Ubah' : 'Tambah') . ' Foto | Buku Tamu')

@section('content')
<h1 class="mb-4 text-xl font-semibold text-slate-800">{{ $foto->exists ? 'Ubah foto' : 'Tambah foto' }}</h1>

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
      action="{{ $foto->exists ? route('galeri.kelola.update', $foto) : route('galeri.kelola.store') }}"
      enctype="multipart/form-data"
      class="mb-4 max-w-2xl rounded-xl bg-white p-4 shadow">
    @csrf
    @if ($foto->exists)
        @method('PUT')
    @endif

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">
            Foto (JPG/PNG/WEBP, maks. 4 MB){{ $foto->exists ? ' - kosongkan bila tidak diganti' : '' }}
        </label>
        @if ($foto->exists)
            <img src="{{ $foto->fotoUrl() }}" alt="" class="mt-2 max-h-40 rounded">
        @endif
        <input type="file" name="foto" accept="image/*" class="mt-2 block text-sm">
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Keterangan (opsional, maks. 200 karakter)</label>
        <input type="text" name="keterangan" value="{{ old('keterangan', $foto->keterangan) }}"
               class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-slate-600">Urutan (angka kecil tampil lebih dulu)</label>
        <input type="number" name="urutan" min="0" max="9999" value="{{ old('urutan', $foto->urutan) }}"
               class="mt-1 w-32 rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </div>

    <div class="mb-4">
        <label class="text-sm text-slate-700">
            <input type="checkbox" name="tampil" value="1" @checked(old('tampil', $foto->tampil))>
            Tampilkan di halaman sambutan
        </label>
    </div>

    <div class="flex gap-2">
        <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan</button>
        <a href="{{ route('galeri.kelola.index') }}"
           class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Batal</a>
    </div>
</form>
@endsection