<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\LogAktivitas;
use App\Models\PenilaianPetugas;
use App\Models\User;
use App\Services\KinerjaPetugas;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KinerjaPetugasController extends Controller
{
    // Maksimal rentang tanggal agar grafik tetap terbaca
    private const MAKS_HARI = 92;

    public function index(Request $request, KinerjaPetugas $kinerja): View
    {
        $request->validate([
            'dari'    => ['nullable', 'date'],
            'sampai'  => ['nullable', 'date', 'after_or_equal:dari'],
            'layanan' => [
                'nullable',
                'string',
                'max:100',
                function ($atribut, $nilai, $gagal) {
                    // Format: "Kategori" atau "Kategori|Sub layanan"
                    [$kategori, $sub] = array_pad(explode('|', (string) $nilai, 2), 2, '');
                    $daftarSub = Kunjungan::LAYANAN[$kategori] ?? null;

                    if ($daftarSub === null || ($sub !== '' && ! in_array($sub, $daftarSub, true))) {
                        $gagal('Pilihan layanan tidak valid.');
                    }
                },
            ],
            'bulan'   => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ]);

        $sampai = $request->filled('sampai')
            ? Carbon::parse($request->input('sampai'))->endOfDay()
            : now()->endOfDay();

        $dari = $request->filled('dari')
            ? Carbon::parse($request->input('dari'))->startOfDay()
            : $sampai->copy()->subDays(29)->startOfDay();

        if ($dari->greaterThan($sampai)) {
            $dari = $sampai->copy()->startOfDay();
        }

        if ($dari->diffInDays($sampai) > self::MAKS_HARI - 1) {
            $dari = $sampai->copy()->subDays(self::MAKS_HARI - 1)->startOfDay();
        }

        $layananDipilih = $request->filled('layanan') ? (string) $request->input('layanan') : null;

        $bulan = $request->input('bulan', now()->format('Y-m'));
        if ($bulan > now()->format('Y-m')) {
            $bulan = now()->format('Y-m');
        }

        $hasil = $kinerja->hitung($dari, $sampai, $layananDipilih);
        $aktor = $request->user();

        return view('kinerja.index', [
            'baris'          => $hasil['baris'],
            'tanggal'        => $hasil['tanggal'],
            'ringkas'        => $hasil['ringkas'],
            'dari'           => $dari,
            'sampai'         => $sampai,
            'layanans'       => Kunjungan::LAYANAN,
            'layananDipilih' => $layananDipilih,
            'bulan'          => $bulan,
            'namaBulan'      => Carbon::createFromFormat('!Y-m', $bulan)->locale('id')->translatedFormat('F Y'),
            'barisBulan'     => $kinerja->bulanan($bulan, $aktor->id),
            'bisaMenilai'    => $aktor->role === 'pimpinan',
        ]);
    }

    // Pimpinan memberi bintang 1 sampai 5 untuk seorang petugas pada satu bulan
    public function nilai(Request $request, User $petugas): RedirectResponse
    {
        abort_unless($request->user()->role === 'pimpinan', 403, 'Hanya pimpinan yang dapat memberi penilaian.');
        abort_unless($petugas->role === 'petugas', 404);

        $data = $request->validate(
            [
                'bintang' => ['required', 'integer', 'between:1,5'],
                'bulan'   => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
                'catatan' => ['nullable', 'string', 'max:200'],
            ],
            [
                'bintang.required' => 'Pilih jumlah bintang terlebih dahulu.',
                'bintang.integer'  => 'Penilaian tidak valid.',
                'bintang.between'  => 'Bintang harus antara 1 sampai 5.',
                'bulan.regex'      => 'Periode penilaian tidak valid.',
                'catatan.max'      => 'Catatan maksimal 200 karakter.',
            ]
        );

        abort_if($data['bulan'] > now()->format('Y-m'), 422, 'Bulan penilaian belum berjalan.');

        PenilaianPetugas::updateOrCreate(
            [
                'petugas_id' => $petugas->id,
                'penilai_id' => $request->user()->id,
                'periode'    => $data['bulan'],
            ],
            [
                'bintang' => (int) $data['bintang'],
                'catatan' => $data['catatan'] ?? null,
            ]
        );

        LogAktivitas::catat('nilai_petugas', $petugas->name.': '.$data['bintang'].' bintang ('.$data['bulan'].')');

        return redirect()
            ->route('kinerja.index', ['bulan' => $data['bulan']])
            ->with('pesan', 'Penilaian untuk '.$petugas->name.' disimpan.');
    }
}
