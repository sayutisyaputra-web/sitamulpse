<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PenyimpanGambar
{
    /**
     * Mengubah data foto JPEG dari browser (base64) menjadi file di folder privat.
     * Gambar dibaca lalu dibuat ulang agar tidak ada data berbahaya yang ikut tersimpan.
     */
    public function simpanJpeg(string $dataUrl, string $folder, string $kolom): string
    {
        $koma = strpos($dataUrl, ',');
        $biner = $koma === false ? false : base64_decode(substr($dataUrl, $koma + 1), true);

        if ($biner === false || strlen($biner) > 2 * 1024 * 1024) {
            throw ValidationException::withMessages([$kolom => 'Foto tidak valid atau terlalu besar.']);
        }

        $info = @getimagesizefromstring($biner);

        if (! $info || $info[2] !== IMAGETYPE_JPEG) {
            throw ValidationException::withMessages([$kolom => 'File bukan foto yang valid.']);
        }

        $gambar = @imagecreatefromstring($biner);

        if (! $gambar) {
            throw ValidationException::withMessages([$kolom => 'Foto tidak dapat diproses.']);
        }

        ob_start();
        imagejpeg($gambar, null, 85);
        $hasil = ob_get_clean();
        imagedestroy($gambar);

        $path = $folder.'/'.Str::uuid().'.jpg';
        Storage::disk('local')->put($path, $hasil);

        return $path;
    }
}
