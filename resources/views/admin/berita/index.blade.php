@extends('layouts.app')

@section('title', 'Informasi Berita | Buku Tamu')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-xl font-semibold text-slate-800">Informasi Berita</h1>
    <a href="{{ route('berita.kelola.create') }}"
       class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Tambah berita</a>
</div>

@if (session('status'))
    <div class="mb-4 rounded-lg bg-white p-3 text-sm text-green-700 shadow">{{ session('status') }}</div>
@endif

<div class="overflow-x-auto rounded-xl bg-white shadow">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
                <th class="px-4 py-3">Foto</th>
                <th class="px-4 py-3">Judul</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Tanggal terbit</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($beritas as $b)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-3">
                        @if ($b->gambarUrl())
                            <img src="{{ $b->gambarUrl() }}" alt="" class="h-12 w-16 rounded object-cover">
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $b->judul }}</td>
                    <td class="px-4 py-3">{{ $b->terbit ? 'Terbit' : 'Draf' }}</td>
                    <td class="px-4 py-3">{{ $b->tanggal_terbit?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if ($b->terbit)
                                <a href="{{ route('berita.show', $b) }}" target="_blank" class="text-slate-600 hover:underline">Lihat</a>
                            @endif
                            <a href="{{ route('berita.kelola.edit', $b) }}" class="text-blue-600 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('berita.kelola.destroy', $b) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada berita.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $beritas->links() }}</div>
@endsection