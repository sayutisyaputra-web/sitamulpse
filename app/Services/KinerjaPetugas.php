<?php

namespace App\Services;

use App\Models\Kunjungan;
use App\Models\PenilaianPetugas;
use App\Models\User;
use Carbon\Carbon;

class KinerjaPetugas
{
    private const WARNA = ['#1e2a78', '#2563eb', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#64748b'];

    /**
     * Menghitung kinerja semua petugas pada rentang tanggal tertentu.
     *
     * $layanan (opsional) berformat "Kategori" atau "Kategori|Sub layanan",
     * contoh: "Sirup" atau "Sirup|Fasmon 1".
     *
     * @return array{baris: array<int, array>, tanggal: array<int, string>, ringkas: array}
     */
    public function hitung(Carbon $dari, Carbon $sampai, ?string $layanan = null): array
    {
        [$kategori, $sub] = $this->pecahLayanan($layanan);

        $tanggal = [];
        for ($t = $dari->copy()->startOfDay(); $t->lte($sampai); $t->addDay()) {
            $tanggal[] = $t->toDateString();
        }

        $kunjungans = Kunjungan::query()
            ->whereNotNull('petugas_id')
            ->whereBetween('waktu_masuk', [$dari, $sampai])
            ->when($kategori, fn ($q) => $q->where('kategori_layanan', $kategori))
            ->when($sub, fn ($q) => $q->where('sub_layanan', $sub))
            ->get(['id', 'petugas_id', 'kategori_layanan', 'sub_layanan', 'status', 'waktu_masuk', 'waktu_keluar', 'foto_keluar_path']);

        $tanpaPetugas = Kunjungan::query()
            ->whereNull('petugas_id')
            ->whereBetween('waktu_masuk', [$dari, $sampai])
            ->when($kategori, fn ($q) => $q->where('kategori_layanan', $kategori))
            ->when($sub, fn ($q) => $q->where('sub_layanan', $sub))
            ->count();

        $perPetugas = $kunjungans->groupBy('petugas_id');
        $semuaDurasi = collect();
        $baris = [];

        foreach (User::where('role', 'petugas')->orderBy('name')->get(['id', 'name', 'foto', 'aktif']) as $p) {
            $data = $perPetugas->get($p->id, collect());

            // Petugas nonaktif hanya tampil bila punya kunjungan pada periode ini
            if (! $p->aktif && $data->isEmpty()) {
                continue;
            }

            $ditolak = $data->where('status', 'ditolak')->count();

            $tuntas = $data->filter(fn ($k) => $k->waktu_keluar !== null && $k->status !== 'ditolak');
            $selesai = $tuntas->count();
            $melayani = $data->count() - $selesai - $ditolak;

            $durasi = $tuntas->map(fn ($k) => abs((int) $k->waktu_masuk->diffInMinutes($k->waktu_keluar)));
            $semuaDurasi = $semuaDurasi->concat($durasi);

            $denganFoto = $tuntas->filter(fn ($k) => ! empty($k->foto_keluar_path))->count();

            $perHari = $data->groupBy(fn ($k) => $k->waktu_masuk->toDateString())->map->count();

            $baris[] = [
                'id'         => $p->id,
                'nama'       => $p->name,
                'foto'       => $p->foto_url,
                'aktif'      => (bool) $p->aktif,
                'total'      => $data->count(),
                'selesai'    => $selesai,
                'melayani'   => max(0, $melayani),
                'ditolak'    => $ditolak,
                'durasi'     => $durasi->isNotEmpty() ? (int) round($durasi->avg()) : null,
                'denganFoto' => $denganFoto,
                'bukti'      => $selesai > 0 ? (int) round($denganFoto / $selesai * 100) : null,
                'harian'     => array_map(fn ($tgl) => (int) $perHari->get($tgl, 0), $tanggal),
            ];
        }

        // Terbanyak lebih dulu, nama sebagai pembeda
        usort($baris, fn ($a, $b) => [$b['total'], $a['nama']] <=> [$a['total'], $b['nama']]);

        foreach ($baris as $i => $b) {
            $baris[$i]['warna'] = self::WARNA[$i % count(self::WARNA)];
        }

        $total = array_sum(array_column($baris, 'total'));
        $selesaiSemua = array_sum(array_column($baris, 'selesai'));
        $fotoSemua = array_sum(array_column($baris, 'denganFoto'));

        return [
            'baris'   => $baris,
            'tanggal' => $tanggal,
            'ringkas' => [
                'total'         => $total,
                'selesai'       => $selesaiSemua,
                'melayani'      => array_sum(array_column($baris, 'melayani')),
                'ditolak'       => array_sum(array_column($baris, 'ditolak')),
                'rataDurasi'    => $semuaDurasi->isNotEmpty() ? (int) round($semuaDurasi->avg()) : null,
                'denganFoto'    => $fotoSemua,
                'persenBukti'   => $selesaiSemua > 0 ? (int) round($fotoSemua / $selesaiSemua * 100) : null,
                'jumlahPetugas' => count($baris),
                'tanpaPetugas'  => $tanpaPetugas,
            ],
        ];
    }

    /**
     * Kinerja satu bulan penuh (format Y-m) lengkap dengan bintang dari pimpinan.
     * Urutan: tamu terbanyak lebih dulu.
     */
    public function bulanan(string $bulan, ?int $penilaiId = null): array
    {
        $awal = Carbon::createFromFormat('!Y-m', $bulan)->startOfMonth();
        $akhir = $awal->copy()->endOfMonth();

        $baris = $this->hitung($awal, $akhir)['baris'];
        $nilai = PenilaianPetugas::where('periode', $bulan)->get()->groupBy('petugas_id');

        foreach ($baris as $i => $b) {
            $daftar = $nilai->get($b['id'], collect());
            $saya = $penilaiId ? $daftar->firstWhere('penilai_id', $penilaiId) : null;

            $baris[$i]['rataBintang'] = $daftar->isNotEmpty() ? round((float) $daftar->avg('bintang'), 1) : null;
            $baris[$i]['jumlahPenilai'] = $daftar->count();
            $baris[$i]['bintangSaya'] = $saya ? (int) $saya->bintang : null;
            $baris[$i]['catatanSaya'] = $saya ? $saya->catatan : null;
        }

        return $baris;
    }

    /**
     * Memecah "Kategori|Sub" menjadi [kategori, sub]. Bagian yang kosong menjadi null.
     *
     * @return array{0:?string,1:?string}
     */
    private function pecahLayanan(?string $layanan): array
    {
        if ($layanan === null || $layanan === '') {
            return [null, null];
        }

        [$kategori, $sub] = array_pad(explode('|', $layanan, 2), 2, '');

        return [$kategori !== '' ? $kategori : null, $sub !== '' ? $sub : null];
    }
}
