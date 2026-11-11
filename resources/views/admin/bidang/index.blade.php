@extends('layouts.app')

@section('title', 'Kelola Bidang | Buku Tamu')

@section('content')
    @php
        $total = $bidangs->count();
        $jumlahAktif = $bidangs->where('aktif', true)->count();
        $jumlahNonaktif = $total - $jumlahAktif;
    @endphp

    {{-- Judul halaman --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Kelola bidang</h1>
            <p class="mt-1 text-sm text-slate-500">
                Atur daftar bidang yang bisa dipilih tamu saat mengisi buku tamu.
            </p>
        </div>

        <div class="flex flex-wrap gap-2 text-xs font-medium">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-slate-700 shadow-sm ring-1 ring-slate-200">
                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                {{ $total }} bidang
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1.5 text-green-700 ring-1 ring-green-200">
                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                {{ $jumlahAktif }} aktif
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-slate-600 ring-1 ring-slate-200">
                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                {{ $jumlahNonaktif }} nonaktif
            </span>
        </div>
    </div>

    {{-- Pesan kesalahan --}}
    @if ($errors->any())
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <ul class="space-y-0.5">
                @foreach ($errors->all() as $pesanError)
                    <li>{{ $pesanError }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tambah bidang --}}
    <section class="mb-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600/10 text-brand-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Tambah bidang baru</h2>
                <p class="text-xs text-slate-500">Nama bidang harus unik dan maksimal 100 karakter.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.bidang.store') }}" class="flex flex-col gap-3 p-5 sm:flex-row">
            @csrf

            <input
                type="text"
                name="nama"
                value="{{ old('nama') }}"
                maxlength="100"
                required
                placeholder="Contoh: Bidang Pengadaan"
                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20">

            <button
                type="submit"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-600/40">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah
            </button>
        </form>
    </section>

    {{-- Daftar bidang --}}
    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-slate-900">Daftar bidang</h2>
            <span class="text-xs text-slate-500">Ubah nama lalu klik Simpan</span>
        </div>

        @forelse ($bidangs as $b)
            @php($dipakai = in_array($b->id, $terpakai))

            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 last:border-b-0 lg:flex-row lg:items-center {{ $b->aktif ? '' : 'bg-slate-50/70' }}">

                {{-- Ubah nama --}}
                <form method="POST" action="{{ route('admin.bidang.update', $b) }}" class="flex flex-1 items-center gap-2">
                    @csrf
                    @method('PUT')

                    <input
                        type="text"
                        name="nama"
                        value="{{ $b->nama }}"
                        maxlength="100"
                        required
                        class="block w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-slate-900 shadow-sm focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20 {{ $b->aktif ? '' : 'text-slate-500' }}">

                    <button
                        type="submit"
                        class="shrink-0 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Simpan
                    </button>
                </form>

                {{-- Status dan keterangan --}}
                <div class="flex items-center gap-2 lg:w-60 lg:justify-start">
                    @if ($b->aktif)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700 ring-1 ring-green-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                            Nonaktif
                        </span>
                    @endif

                    @if ($dipakai)
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-200">
                            Sudah dipakai
                        </span>
                    @endif
                </div>

                {{-- Aksi --}}
                <div class="flex items-center gap-2 lg:w-52 lg:justify-end">
                    <form method="POST" action="{{ route('admin.bidang.toggle', $b) }}">
                        @csrf

                        <button
                            type="submit"
                            class="rounded-lg px-3 py-2 text-sm font-medium transition {{ $b->aktif ? 'text-amber-700 hover:bg-amber-50' : 'text-green-700 hover:bg-green-50' }}">
                            {{ $b->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>

                    @if (! $dipakai)
                        <form
                            method="POST"
                            action="{{ route('admin.bidang.destroy', $b) }}"
                            onsubmit="return confirm('Hapus bidang {{ $b->nama }}? Tindakan ini tidak bisa dibatalkan.')">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21" />
                    </svg>
                </div>
                <p class="mt-3 text-sm font-medium text-slate-700">Belum ada bidang</p>
                <p class="mt-1 text-xs text-slate-500">Tambahkan bidang pertama lewat formulir di atas.</p>
            </div>
        @endforelse
    </section>

    <p class="mt-4 text-xs text-slate-500">
        Bidang yang sudah dipakai data kunjungan tidak bisa dihapus, hanya bisa dinonaktifkan agar tidak muncul di form tamu.
    </p>
@endsection