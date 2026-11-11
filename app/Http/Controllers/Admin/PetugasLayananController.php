<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Mengatur petugas mana yang memegang unit layanan mana
 * (kategori layanan + sub layanan, misalnya Sirup - Fasmon 1).
 */
class PetugasLayananController extends Controller
{
    public function index(): View
    {
        $baris = DB::table('layanan_petugas')
            ->join('users', 'users.id', '=', 'layanan_petugas.user_id')
            ->orderBy('users.name')
            ->get([
                'layanan_petugas.kategori_layanan',
                'layanan_petugas.sub_layanan',
                'users.id',
                'users.name',
                'users.aktif',
            ]);

        // Kunci: "Kategori|Sub" (sub kosong untuk kategori tanpa pilihan lanjutan)
        $penugasan = [];
        foreach ($baris as $b) {
            $penugasan[$b->kategori_layanan . '|' . $b->sub_layanan][] = [
                'id'    => (int) $b->id,
                'nama'  => $b->name,
                'aktif' => (bool) $b->aktif,
            ];
        }

        return view('admin.petugas-layanan.index', [
            'layanan'   => Kunjungan::LAYANAN,
            'penugasan' => $penugasan,
            'petugas'   => User::where('role', 'petugas')
                ->where('aktif', true)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function tambah(Request $request): RedirectResponse
    {
        [$kategori, $sub] = $this->validasiUnit($request);

        $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'petugas')->where('aktif', true)),
            ],
        ], [
            'user_id.required' => 'Pilih petugas terlebih dulu.',
            'user_id.exists'   => 'Petugas tidak valid atau tidak aktif.',
        ]);

        $userId = (int) $request->input('user_id');

        $sudahAda = DB::table('layanan_petugas')
            ->where('kategori_layanan', $kategori)
            ->where('sub_layanan', $sub)
            ->where('user_id', $userId)
            ->exists();

        if (! $sudahAda) {
            DB::table('layanan_petugas')->insert([
                'kategori_layanan' => $kategori,
                'sub_layanan'      => $sub,
                'user_id'          => $userId,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            LogAktivitas::catat(
                'tambah_petugas_layanan',
                (User::find($userId)->name ?? 'petugas') . ' ke ' . $this->namaUnit($kategori, $sub)
            );
        }

        return back()->with('pesan', 'Petugas ditambahkan ke layanan.');
    }

    public function hapus(Request $request): RedirectResponse
    {
        [$kategori, $sub] = $this->validasiUnit($request);

        $request->validate(['user_id' => ['required', 'integer']]);

        $userId = (int) $request->input('user_id');

        $dihapus = DB::table('layanan_petugas')
            ->where('kategori_layanan', $kategori)
            ->where('sub_layanan', $sub)
            ->where('user_id', $userId)
            ->delete();

        if ($dihapus) {
            LogAktivitas::catat(
                'hapus_petugas_layanan',
                (User::find($userId)->name ?? 'petugas') . ' dari ' . $this->namaUnit($kategori, $sub)
            );
        }

        return back()->with('pesan', 'Petugas dilepas dari layanan.');
    }

    /**
     * Memeriksa kategori dan sub layanan yang dikirim.
     * Sub layanan wajib sesuai kategori; kategori tanpa pilihan lanjutan memakai sub kosong.
     *
     * @return array{0:string,1:string}
     */
    private function validasiUnit(Request $request): array
    {
        $data = $request->validate([
            'kategori_layanan' => ['required', Rule::in(array_keys(Kunjungan::LAYANAN))],
            'sub_layanan'      => ['nullable', 'string', 'max:50'],
        ]);

        $kategori = $data['kategori_layanan'];
        $sub = (string) ($data['sub_layanan'] ?? '');
        $daftarSub = Kunjungan::LAYANAN[$kategori];

        $valid = $daftarSub ? in_array($sub, $daftarSub, true) : $sub === '';

        if (! $valid) {
            throw ValidationException::withMessages([
                'sub_layanan' => 'Pilihan layanan tidak sesuai dengan kategorinya.',
            ]);
        }

        return [$kategori, $sub];
    }

    private function namaUnit(string $kategori, string $sub): string
    {
        return $sub !== '' ? $kategori . ' - ' . $sub : $kategori;
    }
}
