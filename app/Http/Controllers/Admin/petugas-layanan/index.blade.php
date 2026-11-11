@extends('layouts.app')

@section('title', 'Petugas Layanan | Buku Tamu')

@push('styles')
<style>
    .pl { --navy: #26378a; --garis: #e2e8f0; --teks: #0f172a; --redup: #64748b; }

    .pl-judul { margin-bottom: 24px; }
    .pl-judul h1 { margin: 0; font-size: 24px; font-weight: 600; color: var(--teks); }
    .pl-judul p { margin: 4px 0 0; font-size: 14px; color: var(--redup); }

    .pl-galat { margin-bottom: 20px; padding: 12px 16px; font-size: 14px; color: #991b1b; background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; }
    .pl-galat ul { margin: 0; padding: 0; list-style: none; }

    .pl-kartu { margin-bottom: 20px; overflow: hidden; background: #fff; border: 1px solid var(--garis); border-radius: 16px; box-shadow: 0 1px 2px rgba(15, 23, 42, .04); }
    .pl-kepala { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 24px; background: #f8fafc; border-bottom: 1px solid var(--garis); }
    .pl-kepala h2 { margin: 0; font-size: 15px; font-weight: 600; color: var(--teks); }
    .pl-kepala small { font-size: 12px; color: var(--redup); }

    .pl-baris { display: grid; grid-template-columns: 200px minmax(0, 1fr) 320px; align-items: center; column-gap: 20px; padding: 14px 24px; border-bottom: 1px solid #f1f5f9; }
    .pl-baris:last-child { border-bottom: 0; }
    .pl-unit { font-size: 14px; font-weight: 500; color: var(--teks); }

    .pl-chip-wadah { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
    .pl-chip { display: inline-flex; align-items: center; gap: 4px; height: 28px; padding: 0 4px 0 12px; font-size: 13px; color: #1e293b; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 999px; }
    .pl-chip.mati { color: #94a3b8; background: #f1f5f9; border-color: #e2e8f0; }
    .pl-chip form { margin: 0; }
    .pl-chip button { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; font-size: 14px; line-height: 1; color: #64748b; cursor: pointer; background: transparent; border: 0; border-radius: 50%; }
    .pl-chip button:hover { color: #b91c1c; background: #fee2e2; }
    .pl-kosong { font-size: 12px; color: #b45309; background: #fffbeb; padding: 3px 10px; border-radius: 999px; }

    .pl-tambah { display: flex; gap: 8px; margin: 0; }
    .pl-pilih { box-sizing: border-box; width: 100%; height: 36px; padding: 0 10px; font-size: 13px; color: var(--teks); background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; }
    .pl-pilih:focus { border-color: var(--navy); outline: none; box-shadow: 0 0 0 3px rgba(38, 55, 138, .15); }
    .pl-tombol { height: 36px; padding: 0 14px; font-size: 13px; font-weight: 600; color: #fff; white-space: nowrap; cursor: pointer; background: var(--navy); border: 0; border-radius: 8px; }
    .pl-tombol:hover { background: #1b2a6b; }

    .pl-catatan { margin: 0 0 24px; font-size: 12px; color: var(--redup); }

    @media (max-width: 1000px) {
        .pl-baris { grid-template-columns: 1fr; row-gap: 10px; padding: 16px; }
    }
</style>
@endpush

@section('content')
<div class="pl">

    <div class="pl-judul">
        <h1>Petugas layanan</h1>
        <p>Atur petugas yang memegang tiap layanan. Tamu yang memilih layanan tertentu akan diarahkan ke petugas yang ditugaskan di sini.</p>
    </div>

    @if ($errors->any())
        <div class="pl-galat">
            <ul>
                @foreach ($errors->all() as $pesanError)
                    <li>{{ $pesanError }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @foreach ($layanan as $kategori => $daftarSub)
        @php
            // Kategori tanpa pilihan lanjutan hanya punya satu unit (sub kosong)
            $unit = $daftarSub ?: [''];
        @endphp

        <section class="pl-kartu">
            <div class="pl-kepala">
                <h2>{{ $kategori }}</h2>
                <small>{{ count($unit) }} unit layanan</small>
            </div>

            @foreach ($unit as $sub)
                @php
                    $terpasang = $penugasan[$kategori . '|' . $sub] ?? [];
                    $idTerpasang = array_column($terpasang, 'id');
                    $bisaDitambah = $petugas->reject(fn ($p) => in_array((int) $p->id, $idTerpasang, true));
                @endphp

                <div class="pl-baris">
                    <div class="pl-unit">{{ $sub !== '' ? $sub : 'Semua tamu kategori ini' }}</div>

                    <div class="pl-chip-wadah">
                        @forelse ($terpasang as $p)
                            <span class="pl-chip {{ $p['aktif'] ? '' : 'mati' }}">
                                {{ $p['nama'] }}{{ $p['aktif'] ? '' : ' (nonaktif)' }}
                                <form method="POST" action="{{ route('admin.petugas-layanan.hapus') }}"
                                      onsubmit="return confirm(@js('Lepas ' . $p['nama'] . ' dari layanan ini?'))">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="kategori_layanan" value="{{ $kategori }}">
                                    <input type="hidden" name="sub_layanan" value="{{ $sub }}">
                                    <input type="hidden" name="user_id" value="{{ $p['id'] }}">
                                    <button type="submit" title="Lepas petugas" aria-label="Lepas {{ $p['nama'] }}">&times;</button>
                                </form>
                            </span>
                        @empty
                            <span class="pl-kosong">Belum ada petugas</span>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('admin.petugas-layanan.tambah') }}" class="pl-tambah">
                        @csrf
                        <input type="hidden" name="kategori_layanan" value="{{ $kategori }}">
                        <input type="hidden" name="sub_layanan" value="{{ $sub }}">

                        <select name="user_id" class="pl-pilih" {{ $bisaDitambah->isEmpty() ? 'disabled' : '' }} required>
                            <option value="">{{ $bisaDitambah->isEmpty() ? 'Semua petugas sudah ditambahkan' : '-- Tambah petugas --' }}</option>
                            @foreach ($bisaDitambah as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="pl-tombol" {{ $bisaDitambah->isEmpty() ? 'disabled' : '' }}>Tambah</button>
                    </form>
                </div>
            @endforeach
        </section>
    @endforeach

    <p class="pl-catatan">
        Satu layanan bisa dipegang beberapa petugas, dan satu petugas bisa memegang beberapa layanan.
        Petugas yang sedang melayani tamu akan dilewati, dan tamu dialihkan ke petugas lain di layanan yang sama.
    </p>
</div>
@endsection
