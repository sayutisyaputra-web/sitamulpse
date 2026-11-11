<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Menampilkan halaman ganti password.
     */
    public function edit(): View
    {
        return view('profil.password');
    }

    /**
     * Memproses perubahan password.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'password_lama' => ['required', 'current_password'],
                'password'      => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'different:password_lama',
                ],
            ],
            [
                'password_lama.current_password' => 'Password lama tidak sesuai.',
                'password.min'                   => 'Password baru minimal 8 karakter.',
                'password.confirmed'             => 'Konfirmasi password baru tidak sama.',
                'password.different'             => 'Password baru harus berbeda dari password lama.',
            ]
        );

        $user = $request->user();
        $user->password = $data['password'];
        $user->save();

        $request->session()->regenerate();

        LogAktivitas::catat('ganti_password');

        return redirect()
            ->route('profil.password')
            ->with('pesan', 'Password berhasil diganti.');
    }

    /**
     * Menampilkan halaman Profil Saya.
     */
    public function editProfil(Request $request): View
    {
        return view('profil.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Menyimpan perubahan Profil Saya.
     */
    public function updateProfil(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users,email,' . $user->id,
                ],
                'nik' => [
                    'nullable',
                    'digits:16',
                    'unique:users,nik,' . $user->id,
                ],
                'nip' => [
                    'nullable',
                    'digits:18',
                    'unique:users,nip,' . $user->id,
                ],
                'jabatan' => ['nullable', 'string', 'max:150'],
                'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ],
            [
                'name.required' => 'Nama wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan pengguna lain.',
                'nik.digits' => 'NIK harus terdiri dari 16 digit.',
                'nik.unique' => 'NIK sudah digunakan pengguna lain.',
                'nip.digits' => 'NIP harus terdiri dari 18 digit.',
                'nip.unique' => 'NIP sudah digunakan pengguna lain.',
                'nib.digits' => 'NIB harus terdiri dari 16 digit.',
                'nib.unique' => 'NIB sudah digunakan pengguna lain.',
                'jabatan.max' => 'Jabatan maksimal 150 karakter.',
                'foto.image' => 'File foto harus berupa gambar.',
                'foto.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
                'foto.max' => 'Ukuran foto maksimal 2 MB.',
            ]
        );

        unset($data['foto']);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->nik = $data['nik'] ?? null;
        $user->nip = $data['nip'] ?? null;
        $user->nib = $data['nib'] ?? null;
        $user->jabatan = $data['jabatan'] ?? null;

        if ($request->hasFile('foto')) {
            $pathLama = $user->foto;

            $pathBaru = $request->file('foto')->store('foto-profil', 'public');

            $user->foto = $pathBaru;

            if ($pathLama) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pathLama);
            }
        }

        $user->save();

        LogAktivitas::catat('ubah_profil');

        return redirect()
            ->route('profil.edit')
            ->with('pesan', 'Profil berhasil diperbarui.');
    }
}