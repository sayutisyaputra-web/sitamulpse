<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\Kunjungan;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BidangController extends Controller
{
    public function index(): View
    {
        return view('admin.bidang.index', [
            'bidangs' => Bidang::orderBy('nama')->get(),
            // id bidang yang sudah dipakai kunjungan (termasuk yang sudah dihapus)
            'terpakai' => Kunjungan::withTrashed()->pluck('bidang_id')->unique()->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:bidangs,nama'],
        ]);

        $bidang = new Bidang();
        $bidang->nama = $data['nama'];
        $bidang->save();

        LogAktivitas::catat('tambah_bidang', $bidang->nama);

        return back()->with('pesan', 'Bidang ditambahkan.');
    }

    public function update(Request $request, Bidang $bidang): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('bidangs', 'nama')->ignore($bidang->id)],
        ]);

        $lama = $bidang->nama;

        $bidang->nama = $data['nama'];
        $bidang->save();

        LogAktivitas::catat('ubah_bidang', $lama . ' menjadi ' . $bidang->nama);

        return back()->with('pesan', 'Nama bidang diperbarui.');
    }

    public function toggle(Bidang $bidang): RedirectResponse
    {
        $bidang->aktif = ! $bidang->aktif;
        $bidang->save();

        LogAktivitas::catat('status_bidang', $bidang->nama . ($bidang->aktif ? ' diaktifkan' : ' dinonaktifkan'));

        return back()->with('pesan', $bidang->aktif ? 'Bidang diaktifkan.' : 'Bidang dinonaktifkan.');
    }

    public function destroy(Bidang $bidang): RedirectResponse
    {
        if (Kunjungan::withTrashed()->where('bidang_id', $bidang->id)->exists()) {
            return back()->withErrors([
                'nama' => 'Bidang ini sudah dipakai data kunjungan dan tidak bisa dihapus. Gunakan Nonaktifkan.',
            ]);
        }

        $nama = $bidang->nama;
        $bidang->delete();

        LogAktivitas::catat('hapus_bidang', $nama);

        return back()->with('pesan', 'Bidang dihapus.');
    }
}