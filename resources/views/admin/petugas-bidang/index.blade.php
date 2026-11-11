@extends('layouts.app')

@section('title', 'Petugas Bidang | Buku Tamu')

@section('content')
<div class="mb-6">
    <h1 class="ui-title">Petugas bidang</h1>
    <p class="mt-1 text-sm text-slate-500">Tentukan petugas yang menangani tiap bidang. Tamu akan memilih petugas dari daftar ini.</p>
</div>

@if ($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
@endif

@if ($petugas->isEmpty())
    <div class="ui-card text-center">
        <p class="text-sm font-medium text-slate-700">Belum ada akun petugas yang aktif</p>
        <p class="mt-1 text-sm text-slate-500">Buat akun dengan peran Petugas di menu Kelola akun terlebih dahulu.</p>
    </div>
@else
    <div class="grid gap-6 lg:grid-cols-2">
        @foreach ($bidangs as $b)
            @php
                $idDipasang = $terpasang[$b->id] ?? [];
                $dipasang = $petugas->whereIn('id', $idDipasang);
                $belum = $petugas->whereNotIn('id', $idDipasang);
            @endphp

            <div class="ui-card flex flex-col">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-brand-900">{{ $b->nama }}</h2>
                    @if ($b->aktif)
                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">Aktif</span>
                    @else
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-200">Nonaktif</span>
                    @endif
                </div>

                @if ($dipasang->isEmpty())
                    <p class="mb-4 rounded-lg border border-dashed border-slate-300 px-3 py-4 text-center text-sm text-slate-500">
                        Belum ada petugas di bidang ini.
                    </p>
                @else
                    <ul class="mb-4 space-y-2">
                        @foreach ($dipasang as $p)
                            <li class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium text-slate-800">{{ $p->name }}</span>
                                    @if (in_array($p->id, $sibuk))
                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200">Sedang melayani</span>
                                    @endif
                                </span>
                                <form method="POST" action="{{ route('admin.petugas-bidang.hapus', [$b, $p->id]) }}"
                                      onsubmit="return confirm('Lepas {{ e($p->name) }} dari bidang {{ e($b->nama) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="ui-btn border border-red-200 bg-white text-red-600 hover:bg-red-50 !px-3 !py-1">Hapus</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-auto border-t border-slate-100 pt-4">
                    @if ($belum->isEmpty())
                        <p class="text-xs text-slate-500">Semua petugas aktif sudah ada di bidang ini.</p>
                    @else
                        <form method="POST" action="{{ route('admin.petugas-bidang.tambah', $b) }}" class="flex flex-wrap items-center gap-2">
                            @csrf
                            <select name="user_id" required class="ui-field min-w-0 flex-1">
                                <option value="">-- Tambah petugas --</option>
                                @foreach ($belum as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <button class="ui-btn ui-btn-primary">Tambah</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
