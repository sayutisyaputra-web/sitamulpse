<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index()
    {
        $beritas = Berita::latest()->paginate(10);

        return view('admin.berita.index', compact('beritas'));
    }

    public function create()
    {
        return view('admin.berita.form', ['berita' => new Berita()]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $berita = new Berita();
        $berita->slug = Str::slug($data['judul']) . '-' . Str::lower(Str::random(5));
        $berita->user_id = auth()->id();
        $this->isiData($berita, $request, $data);
        $berita->save();

        LogAktivitas::catat('tambah_berita', 'Menambah berita: ' . $berita->judul);

        return redirect()->route('berita.kelola.index')->with('status', 'Berita berhasil ditambahkan.');
    }

    public function edit(Berita $berita)
    {
        return view('admin.berita.form', compact('berita'));
    }

    public function update(Request $request, Berita $berita)
    {
        $data = $this->validasi($request);

        $this->isiData($berita, $request, $data);
        $berita->save();

        LogAktivitas::catat('ubah_berita', 'Mengubah berita: ' . $berita->judul);

        return redirect()->route('berita.kelola.index')->with('status', 'Berita berhasil diubah.');
    }

    public function destroy(Berita $berita)
    {
        if ($berita->gambar_path) {
            Storage::disk('public')->delete($berita->gambar_path);
        }

        $judul = $berita->judul;
        $berita->delete();

        LogAktivitas::catat('hapus_berita', 'Menghapus berita: ' . $judul);

        return redirect()->route('berita.kelola.index')->with('status', 'Berita berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'judul'     => ['required', 'string', 'max:200'],
            'ringkasan' => ['nullable', 'string', 'max:300'],
            'isi'       => ['required', 'string', 'max:20000'],
            'gambar'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function isiData(Berita $berita, Request $request, array $data): void
    {
        $berita->judul = $data['judul'];
        $berita->ringkasan = $data['ringkasan'] ?? null;
        $berita->isi = $data['isi'];
        $berita->terbit = $request->boolean('terbit');

        if ($berita->terbit && ! $berita->tanggal_terbit) {
            $berita->tanggal_terbit = now();
        }

        if ($request->boolean('hapus_gambar') && $berita->gambar_path) {
            Storage::disk('public')->delete($berita->gambar_path);
            $berita->gambar_path = null;
        }

        if ($request->hasFile('gambar')) {
            if ($berita->gambar_path) {
                Storage::disk('public')->delete($berita->gambar_path);
            }
            $berita->gambar_path = $request->file('gambar')->store('beranda/berita', 'public');
        }
    }
}