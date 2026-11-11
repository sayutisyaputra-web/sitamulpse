<?php

namespace App\Http\Middleware;

use App\Models\Kunjungan;
use App\Models\LogAktivitas;
use App\Services\PenyimpanGambar;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check-out hanya diproses bila disertai foto bukti.
 * Foto disimpan di sini, lalu proses check-out yang sudah ada berjalan seperti biasa.
 */
class WajibFotoCheckout
{
    public function __construct(private PenyimpanGambar $gambar)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $kunjungan = $request->route('kunjungan');

        if ($kunjungan instanceof Kunjungan && ! $kunjungan->waktu_keluar && $kunjungan->status !== 'ditolak') {
            $data = $request->validate(
                [
                    'foto_keluar_data' => ['required', 'string', 'max:3000000', 'starts_with:data:image/jpeg;base64,'],
                ],
                [
                    'foto_keluar_data.required'    => 'Foto check-out wajib diambil atau dipilih.',
                    'foto_keluar_data.starts_with' => 'Format foto tidak valid. Silakan ambil ulang.',
                    'foto_keluar_data.max'         => 'Ukuran foto terlalu besar. Silakan ambil ulang.',
                ]
            );

            $path = $this->gambar->simpanJpeg($data['foto_keluar_data'], 'kunjungan/foto-keluar', 'foto_keluar_data');

            if ($kunjungan->foto_keluar_path) {
                Storage::disk('local')->delete($kunjungan->foto_keluar_path);
            }

            $kunjungan->foto_keluar_path = $path;
            $kunjungan->save();

            LogAktivitas::catat('foto_checkout', $kunjungan->nomor_kunjungan);
        }

        return $next($request);
    }
}
