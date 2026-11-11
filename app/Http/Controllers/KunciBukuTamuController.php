<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use App\Support\KunciBukuTamu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KunciBukuTamuController extends Controller
{
    public function buka(Request $request): RedirectResponse
    {
        $data = $request->validate(
            ['pin' => ['required', 'digits:6']],
            [
                'pin.required' => 'PIN wajib diisi.',
                'pin.digits'   => 'PIN harus berupa 6 digit angka.',
            ]
        );

        $petugas = User::where('aktif', true)
            ->whereIn('role', ['admin', 'petugas', 'superadmin'])
            ->whereNotNull('pin')
            ->get()
            ->first(fn (User $user) => Hash::check($data['pin'], $user->pin));

        if (! $petugas) {
            LogAktivitas::catat('buka_buku_gagal', 'PIN tidak sesuai');

            return back()->withErrors(['pin' => 'PIN tidak sesuai.']);
        }

        $request->session()->regenerate();
        KunciBukuTamu::buka();

        LogAktivitas::catat('buka_buku_tamu', 'Dibuka oleh ' . $petugas->name, $petugas->id);

        return redirect()->route('kunjungan.create');
    }

    public function kunci(): RedirectResponse
    {
        KunciBukuTamu::kunci();

        return redirect()->route('kunjungan.create');
    }
}