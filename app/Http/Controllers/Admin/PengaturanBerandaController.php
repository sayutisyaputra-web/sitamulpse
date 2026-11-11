<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\PengaturanBeranda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanBerandaController extends Controller
{
    public function edit()
    {
        return view('admin.beranda.edit', ['p' => PengaturanBeranda::ambil()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'judul_utama'    => ['required', 'string', 'max:150'],
            'subjudul'       => ['nullable', 'string', 'max:255'],
            'judul_sambutan' => ['required', 'string', 'max:150'],
            'teks_sambutan'  => ['nullable', 'string', 'max:1000'],
            'daftar_layanan' => ['nullable', 'string', 'max:1500'],
            'daftar_langkah' => ['nullable', 'string', 'max:1500'],
            'teks_privasi'   => ['nullable', 'string', 'max:255'],
            'teks_footer'    => ['nullable', 'string', 'max:255'],
            'logo'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $p = PengaturanBeranda::ambil();

        foreach (['logo', 'banner'] as $jenis) {
            $kolom = $jenis . '_path';

            if ($request->boolean('hapus_' . $jenis) && $p->{$kolom}) {
                Storage::disk('public')->delete($p->{$kolom});
                $p->{$kolom} = null;
            }

            if ($request->hasFile($jenis)) {
                if ($p->{$kolom}) {
                    Storage::disk('public')->delete($p->{$kolom});
                }
                $p->{$kolom} = $request->file($jenis)->store('beranda', 'public');
            }
        }

        unset($data['logo'], $data['banner']);
        $p->fill($data)->save();

        LogAktivitas::catat('ubah_beranda', 'Mengubah pengaturan halaman sambutan');

        return redirect()
            ->route('beranda.pengaturan.edit')
            ->with('status', 'Pengaturan beranda berhasil disimpan.');
    }
}