@php
    // Foto diperkecil (lebar 80 px) supaya file PDF tidak berat.
    $mini = function (?string $path) {
        $disk = \Illuminate\Support\Facades\Storage::disk('local');

        if (! $path || ! $disk->exists($path)) {
            return null;
        }

        $gambar = @imagecreatefromstring($disk->get($path));

        if (! $gambar) {
            return null;
        }

        $kecil = imagescale($gambar, 80);
        imagedestroy($gambar);

        if (! $kecil) {
            return null;
        }

        ob_start();
        imagejpeg($kecil, null, 65);
        $jpeg = ob_get_clean();
        imagedestroy($kecil);

        return 'data:image/jpeg;base64,' . base64_encode($jpeg);
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Tamu</title>
    <style>
        @page { size: a4 landscape; margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7.5px; }
        h2 { text-align: center; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #444; padding: 3px; text-align: left; vertical-align: middle; word-wrap: break-word; }
        th { background: #eee; text-align: center; }
        tr { page-break-inside: avoid; }
        td.tengah { text-align: center; }
        td.foto, th.foto { text-align: center; }
        td.foto img { width: 80px; height: auto; }
        .sub { color: #555; }
    </style>
</head>
<body>
    <h2>Laporan Buku Tamu</h2>
    <p>Dicetak: {{ now()->format('d/m/Y H:i') }} oleh {{ $pencetak ?? '-' }}<br>Petugas: {{ $namaPetugas ?? 'Semua petugas' }}</p>
    <table>
        <thead>
            <tr>
                <th style="width: 3%">No</th>
                <th style="width: 7%">No. Kunjungan</th>
                <th style="width: 6.5%">Waktu Mulai Pelayanan</th>
                <th style="width: 6.5%">Nama</th>
                <th style="width: 6.5%">No. HP</th>
                <th style="width: 9%">Email</th>
                <th style="width: 6.5%">Instansi / Nama Usaha</th>
                <th style="width: 7.5%">Kategori Layanan</th>
                <th style="width: 10%">Permasalahan (Detail)</th>
                <th style="width: 5%">Media</th>
                <th style="width: 6.5%">Petugas Pelayanan</th>
                <th style="width: 7.5%">Informasi Penyelesaian Permasalahan</th>
                <th style="width: 6.5%">Waktu Selesai Pelayanan</th>
                <th class="foto" style="width: 11%">Bukti Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kunjungans as $k)
            @php
                $buktiKunjungan = $mini($k->foto_keluar_path);
            @endphp
            <tr>
                <td class="tengah">{{ $loop->iteration }}</td>
                <td>{{ $k->nomor_kunjungan }}</td>
                <td>{{ $k->waktu_masuk->format('d/m/Y H:i') }}</td>
                <td>{{ $k->nama }}</td>
                <td>{{ $k->no_hp }}</td>
                <td>{{ $k->email ?: '-' }}</td>
                <td>{{ $k->instansi ?: '-' }}</td>
                <td>
                    @if ($k->kategori_layanan)
                        {{ $k->kategori_layanan }}
                        @if ($k->sub_layanan)
                            <br><span class="sub">{{ $k->sub_layanan }}</span>
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td>{{ $k->keperluan }}</td>
                <td>{{ $k->media_pelaporan ?: '-' }}</td>
                <td>{{ $k->petugas->name ?? 'Antrean' }}</td>
                <td>{{ $k->status }}</td>
                <td>{{ $k->waktu_keluar ? $k->waktu_keluar->format('d/m/Y H:i') : '-' }}</td>
                <td class="foto">
                    @if ($buktiKunjungan)
                        <img src="{{ $buktiKunjungan }}" alt="Bukti kunjungan">
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
