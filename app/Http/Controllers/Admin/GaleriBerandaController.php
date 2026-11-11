<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GaleriBeranda;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriBerandaController extends Controller
{
    public function index()
    {
        $fotos = GaleriBeranda::orderBy('urutan')->orderBy('id')->get();

        return view('admin.galeri.index', compact('fotos'));
    }

    public function create()
    {
        return view('admin.galeri.form', [
            'foto' => new GaleriBeranda(['urutan' => 0, 'tampil' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'keterangan' => ['nullable', 'string', 'max:200'],
            'urutan'     => ['nullable', 'integer', 'min:0', 'max:9999'],
            'foto'       => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $foto = new GaleriBeranda();
        $foto->keterangan = $data['keterangan'] ?? null;
        $foto->urutan = $data['urutan'] ?? 0;
        $foto->tampil = $request->boolean('tampil');
        $foto->foto_path = $request->file('foto')->store('beranda/galeri', 'public');
        $foto->save();

        LogAktivitas::catat('tambah_galeri', 'Menambah foto galeri: ' . ($foto->keterangan ?: 'tanpa keterangan'));

        return redirect()->route('galeri.kelola.index')->with('status', 'Foto berhasil ditambahkan.');
    }

    public function edit(GaleriBeranda $galeri)
    {
        return view('admin.galeri.form', ['foto' => $galeri]);
    }

    public function update(Request $request, GaleriBeranda $galeri)
    {
        $data = $request->validate([
            'keterangan' => ['nullable', 'string', 'max:200'],
            'urutan'     => ['nullable', 'integer', 'min:0', 'max:9999'],
            'foto'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $galeri->keterangan = $data['keterangan'] ?? null;
        $galeri->urutan = $data['urutan'] ?? 0;
        $galeri->tampil = $request->boolean('tampil');

        if ($request->hasFile('foto')) {
            Storage::disk('public')->delete($galeri->foto_path);
            $galeri->foto_path = $request->file('foto')->store('beranda/galeri', 'public');
        }

        $galeri->save();

        LogAktivitas::catat('ubah_galeri', 'Mengubah foto galeri: ' . ($galeri->keterangan ?: 'tanpa keterangan'));

        return redirect()->route('galeri.kelola.index')->with('status', 'Foto berhasil diubah.');
    }

    public function destroy(GaleriBeranda $galeri)
    {
        Storage::disk('public')->delete($galeri->foto_path);

        $ket = $galeri->keterangan ?: 'tanpa keterangan';
        $galeri->delete();

        LogAktivitas::catat('hapus_galeri', 'Menghapus foto galeri: ' . $ket);

        return redirect()->route('galeri.kelola.index')->with('status', 'Foto berhasil dihapus.');
    }
}