<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\LogAktivitas;
use App\Models\User;
use App\Services\PenugasanPetugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PetugasBidangController extends Controller
{
    public function index(PenugasanPetugas $penugasan): View
    {
        $terpasang = DB::table('bidang_petugas')
            ->get(['bidang_id', 'user_id'])
            ->groupBy('bidang_id')
            ->map(fn ($baris) => $baris->pluck('user_id')->map(fn ($id) => (int) $id)->all())
            ->all();

        return view('admin.petugas-bidang.index', [
            'bidangs'   => Bidang::orderBy('nama')->get(),
            'petugas'   => User::where('role', 'petugas')->where('aktif', true)->orderBy('name')->get(['id', 'name']),
            'terpasang' => $terpasang,
            'sibuk'     => $penugasan->idSibuk(),
        ]);
    }

    // Menambahkan satu petugas ke sebuah bidang
    public function tambah(Request $request, Bidang $bidang): RedirectResponse
    {
        $data = $request->validate(
            ['user_id' => ['required', 'integer']],
            ['user_id.required' => 'Pilih petugas yang akan ditambahkan.']
        );

        $petugas = User::where('role', 'petugas')->where('aktif', true)->find($data['user_id']);

        if (! $petugas) {
            return back()->withErrors(['user_id' => 'Petugas tidak valid atau tidak aktif.']);
        }

        $sudahAda = DB::table('bidang_petugas')
            ->where('bidang_id', $bidang->id)
            ->where('user_id', $petugas->id)
            ->exists();

        if (! $sudahAda) {
            DB::table('bidang_petugas')->insert([
                'bidang_id'  => $bidang->id,
                'user_id'    => $petugas->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            LogAktivitas::catat('atur_petugas_bidang', $bidang->nama.': tambah '.$petugas->name);
        }

        return back()->with('pesan', $petugas->name.' ditambahkan ke bidang '.$bidang->nama.'.');
    }

    // Melepas satu petugas dari sebuah bidang
    public function hapus(Bidang $bidang, User $user): RedirectResponse
    {
        $terhapus = DB::table('bidang_petugas')
            ->where('bidang_id', $bidang->id)
            ->where('user_id', $user->id)
            ->delete();

        if ($terhapus) {
            LogAktivitas::catat('atur_petugas_bidang', $bidang->nama.': lepas '.$user->name);
        }

        return back()->with('pesan', $user->name.' dilepas dari bidang '.$bidang->nama.'.');
    }

    // Menyimpan daftar lengkap sekaligus (dipertahankan agar route lama tetap berfungsi)
    public function update(Request $request, Bidang $bidang): RedirectResponse
    {
        $data = $request->validate([
            'petugas'   => ['nullable', 'array'],
            'petugas.*' => ['integer'],
        ]);

        // Hanya petugas aktif yang boleh dipilih
        $layak = User::where('role', 'petugas')->where('aktif', true)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $dipilih = array_values(array_intersect($layak, array_map('intval', $data['petugas'] ?? [])));

        DB::transaction(function () use ($bidang, $layak, $dipilih) {
            DB::table('bidang_petugas')
                ->where('bidang_id', $bidang->id)
                ->whereIn('user_id', $layak)
                ->whereNotIn('user_id', $dipilih)
                ->delete();

            $sudahAda = DB::table('bidang_petugas')
                ->where('bidang_id', $bidang->id)
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            foreach (array_diff($dipilih, $sudahAda) as $userId) {
                DB::table('bidang_petugas')->insert([
                    'bidang_id'  => $bidang->id,
                    'user_id'    => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        LogAktivitas::catat('atur_petugas_bidang', $bidang->nama.': '.count($dipilih).' petugas');

        return back()->with('pesan', 'Petugas untuk bidang '.$bidang->nama.' disimpan.');
    }
}
