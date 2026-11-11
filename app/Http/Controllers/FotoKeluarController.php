<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FotoKeluarController extends Controller
{
    // Menampilkan foto check-out (hanya admin dan petugas yang login, diatur di route)
    public function tampil(Kunjungan $kunjungan): StreamedResponse
    {
        $path = $kunjungan->foto_keluar_path;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        LogAktivitas::catat('lihat_foto_checkout', $kunjungan->nomor_kunjungan);

        return Storage::disk('local')->response($path);
    }
}
