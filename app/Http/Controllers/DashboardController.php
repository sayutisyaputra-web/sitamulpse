<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const STATUS = [
        'menunggu' => 'Menunggu',
        'diterima' => 'Diterima',
        'selesai'  => 'Selesai',
        'ditolak'  => 'Ditolak',
    ];

    public function index(): View
    {
        // Kunjungan 14 hari terakhir (hari kosong tetap tampil dengan angka 0)
        $mulai = today()->subDays(13);

        $perHari = Kunjungan::whereDate('waktu_masuk', '>=', $mulai)
            ->selectRaw('DATE(waktu_masuk) as tgl, COUNT(*) as total')
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        $hari = [];
        for ($i = 0; $i < 14; $i++) {
            $tanggal = $mulai->copy()->addDays($i);
            $hari[] = [
                'label' => $tanggal->format('d/m'),
                'total' => (int) ($perHari[$tanggal->format('Y-m-d')] ?? 0),
            ];
        }

        $maksHari = max(1, collect($hari)->max('total'));
        $grafikHari = collect($hari)->map(function ($h) use ($maksHari) {
            $h['persen'] = round($h['total'] / $maksHari * 85);

            return $h;
        })->all();

        // Kunjungan per kategori layanan bulan ini
        // (data lama yang belum punya kategori dikelompokkan sebagai satu baris)
        $perLayanan = Kunjungan::whereYear('waktu_masuk', now()->year)
            ->whereMonth('waktu_masuk', now()->month)
            ->selectRaw('kategori_layanan, COUNT(*) as total')
            ->groupBy('kategori_layanan')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $maksLayanan = max(1, (int) $perLayanan->max('total'));

        // Rincian status bulan ini
        $perStatus = Kunjungan::whereYear('waktu_masuk', now()->year)
            ->whereMonth('waktu_masuk', now()->month)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Rata-rata durasi kunjungan (menit) bulan ini, hanya yang sudah check-out
        $rataDurasi = Kunjungan::whereNotNull('waktu_keluar')
            ->whereYear('waktu_masuk', now()->year)
            ->whereMonth('waktu_masuk', now()->month)
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, waktu_masuk, waktu_keluar)) as rata')
            ->value('rata');

        return view('dashboard', [
            'hariIni'     => Kunjungan::whereDate('waktu_masuk', today())->count(),
            'mingguIni'   => Kunjungan::whereBetween('waktu_masuk', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'bulanIni'    => Kunjungan::whereYear('waktu_masuk', now()->year)
                                      ->whereMonth('waktu_masuk', now()->month)->count(),
            'didalam'     => Kunjungan::whereNull('waktu_keluar')
                                      ->where('status', '!=', 'ditolak')
                                      ->whereDate('waktu_masuk', today())->count(),
            'rataDurasi'  => $rataDurasi !== null ? (int) round($rataDurasi) : null,
            'grafikHari'  => $grafikHari,
            'perLayanan'  => $perLayanan,
            'maksLayanan' => $maksLayanan,
            'perStatus'   => $perStatus,
            'statuses'    => self::STATUS,
            'terbaru'     => Kunjungan::latest('waktu_masuk')->limit(5)->get(),
        ]);
    }
}
