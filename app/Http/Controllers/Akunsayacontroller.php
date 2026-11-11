<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AkunSayaController extends Controller
{
    public function edit(Request $request): View
    {
        return view('akun-saya', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'nik'     => ['nullable', 'digits:16'],
            'nip'     => ['nullable', 'digits:18', Rule::unique('users', 'nip')->ignore($user->id)],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'foto'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->nik = $data['nik'] ?? null;
        $user->nip = $data['nip'] ?? null;
        $user->jabatan = $data['jabatan'] ?? null;

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('foto-profil', 'public');
        }

        $user->save();

        // NIK sengaja tidak ikut dicatat di log.
        LogAktivitas::catat('ubah_profil_sendiri', $user->email);

        return back()->with('pesan', 'Profil Anda diperbarui.');
    }

    public function hapusFoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
            $user->foto = null;
            $user->save();

            LogAktivitas::catat('hapus_foto_profil_sendiri', $user->email);
        }

        return back()->with('pesan', 'Foto profil dihapus.');
    }
}