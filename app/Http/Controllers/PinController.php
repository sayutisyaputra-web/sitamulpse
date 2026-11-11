<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PinController extends Controller
{
    private const PERAN_BOLEH = ['admin', 'petugas', 'superadmin'];

    public function edit(Request $request): View
    {
        $this->pastikanBoleh($request);

        return view('profil.pin', [
            'sudahAda' => $request->user()->pin !== null,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->pastikanBoleh($request);

        $user = $request->user();

        $data = $request->validate(
            [
                'password_sekarang' => ['required', 'current_password'],
                'pin'               => ['required', 'digits:6', 'confirmed'],
            ],
            [
                'password_sekarang.required'         => 'Password saat ini wajib diisi.',
                'password_sekarang.current_password' => 'Password saat ini tidak sesuai.',
                'pin.required'                       => 'PIN wajib diisi.',
                'pin.digits'                         => 'PIN harus berupa 6 digit angka.',
                'pin.confirmed'                      => 'Konfirmasi PIN tidak sama.',
            ]
        );

        // Satu PIN hanya untuk satu akun, supaya pencatatan "siapa yang membuka" tidak keliru
        $dipakaiLain = User::whereNotNull('pin')
            ->where('id', '!=', $user->id)
            ->get()
            ->contains(fn (User $lain) => Hash::check($data['pin'], $lain->pin));

        if ($dipakaiLain) {
            return back()->withErrors(['pin' => 'PIN ini tidak dapat dipakai. Silakan pilih PIN lain.']);
        }

        $user->pin = Hash::make($data['pin']);
        $user->save();

        LogAktivitas::catat('atur_pin', 'PIN buku tamu diperbarui');

        return back()->with('pesan', 'PIN buku tamu berhasil disimpan.');
    }

    private function pastikanBoleh(Request $request): void
    {
        abort_unless(in_array($request->user()->role, self::PERAN_BOLEH, true), 403);
    }
}