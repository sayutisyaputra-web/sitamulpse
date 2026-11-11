@extends('layouts.app')

@section('title', 'Galeri Foto | Buku Tamu')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-xl font-semibold text-slate-800">Galeri Foto</h1>
    <a href="{{ route('galeri.kelola.create') }}"
       class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Tambah foto</a>
</div>

@if (session('status'))
    <div class="mb-4 rounded-lg bg-white p-3 text-sm text-green-700 shadow">{{ session('status') }}</div>
@endif

<div class="overflow-x-auto rounded-xl bg-white shadow">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
                <th class="px-4 py-3">Foto</th>
                <th class="px-4 py-3">Keterangan</th>
                <th class="px-4 py-3">Urutan</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($fotos as $f)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-3">
                        <img src="{{ $f->fotoUrl() }}" alt="" class="h-12 w-16 rounded object-cover">
                    </td>
                    <td class="px-4 py-3 text-slate-800">{{ $f->keterangan ?: '-' }}</td>
                    <td class="px-4 py-3">{{ $f->urutan }}</td>
                    <td class="px-4 py-3">{{ $f->tampil ? 'Tampil' : 'Disembunyikan' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('galeri.kelola.edit', $f) }}" class="text-blue-600 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('galeri.kelola.destroy', $f) }}"
                                  onsubmit="return confirm('Hapus foto ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada foto.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection