<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tanda Pengenal {{ $kunjungan->nomor_kunjungan }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; font-family: Arial, Helvetica, sans-serif; background: #f1f5f9; color: #0f172a; }
        .aksi { max-width: 90mm; margin: 0 auto 16px; display: flex; gap: 8px; }
        .aksi button, .aksi a { padding: 8px 14px; border-radius: 8px; font-size: 14px; cursor: pointer; text-decoration: none; }
        .aksi button { background: #2563eb; color: #fff; border: 0; }
        .aksi a { background: #fff; color: #334155; border: 1px solid #cbd5e1; }

        .kartu { width: 90mm; min-height: 60mm; margin: 0 auto; padding: 5mm; background: #fff; border: 1px solid #94a3b8; border-radius: 4mm; display: flex; flex-direction: column; }
        .kepala { text-align: center; border-bottom: 1px solid #cbd5e1; padding-bottom: 2mm; margin-bottom: 3mm; }
        .kepala .judul { font-size: 9pt; font-weight: bold; }
        .kepala .tamu { font-size: 14pt; font-weight: bold; letter-spacing: 3px; margin-top: 1mm; }
        .isi { display: flex; gap: 4mm; align-items: flex-start; }
        .data { flex: 1; font-size: 8.5pt; line-height: 1.35; }
        .data .nama { font-size: 12pt; font-weight: bold; margin-bottom: 1mm; word-break: break-word; }
        .data .label { color: #64748b; font-size: 7pt; margin-top: 1.5mm; text-transform: uppercase; }
        .qr { width: 28mm; flex-shrink: 0; text-align: center; }
        .qr svg { width: 100%; height: auto; display: block; }
        .qr .nomor { font-family: "Courier New", monospace; font-size: 6.5pt; margin-top: 1mm; word-break: break-all; }

        @page { size: 90mm 60mm; margin: 0; }
        @media print {
            body { padding: 0; background: #fff; }
            .aksi { display: none; }
            .kartu { border: 0; border-radius: 0; width: 90mm; min-height: 60mm; }
        }
    </style>
</head>
<body>
    <div class="aksi">
        <button type="button" onclick="window.print()">Cetak</button>
        <a href="{{ route('petugas.kunjungan.show', $kunjungan) }}">Kembali</a>
    </div>

    <div class="kartu">
        <div class="kepala">
            <div class="judul">SITAMU LPSE ( Sistem Informasi Tamu LPSE )</div>
            <div class="tamu">TAMU</div>
        </div>

        <div class="isi">
            <div class="data">
                <div class="nama">{{ $kunjungan->nama }}</div>
                @if ($kunjungan->instansi)
                    <div>{{ $kunjungan->instansi }}</div>
                @endif

                <div class="label">Layanan</div>
                {{-- Data lama yang belum punya kategori layanan menampilkan bidangnya --}}
                <div>{{ $kunjungan->kategori_layanan ? $kunjungan->layananLengkap() : ($kunjungan->bidang->nama ?? '-') }}</div>

                <div class="label">Waktu masuk</div>
                <div>{{ $kunjungan->waktu_masuk->format('d/m/Y H:i') }}</div>
            </div>

            <div class="qr">
                {!! $qr !!}
                <div class="nomor">{{ $kunjungan->nomor_kunjungan }}</div>
            </div>
        </div>
    </div>
</body>
</html>
