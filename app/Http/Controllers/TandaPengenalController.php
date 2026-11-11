<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\LogAktivitas;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\View\View;

class TandaPengenalController extends Controller
{
    public function cetak(Kunjungan $kunjungan): View
    {
        abort_if($kunjungan->status === 'ditolak', 422, 'Kunjungan yang ditolak tidak bisa dicetak tanda pengenalnya.');

        $kunjungan->load('bidang');

        // QR hanya berisi nomor kunjungan (dibuat sistem), tanpa data pribadi
        $penulis = new Writer(new ImageRenderer(new RendererStyle(180, 2), new SvgImageBackEnd()));
        $svg = $penulis->writeString($kunjungan->nomor_kunjungan);
        $svg = substr($svg, (int) strpos($svg, '<svg'));

        LogAktivitas::catat('cetak_tanda_pengenal', $kunjungan->nomor_kunjungan);

        return view('tanda-pengenal.cetak', [
            'kunjungan' => $kunjungan,
            'qr'        => $svg,
        ]);
    }
}