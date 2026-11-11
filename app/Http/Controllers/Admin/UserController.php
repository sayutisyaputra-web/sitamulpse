<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ROLES = [
        'superadmin' => 'Superadmin',
        'admin'      => 'Admin',
        'petugas'    => 'Petugas',
        'pimpinan'   => 'Pimpinan',
    ];

    /** Peran yang boleh dikelola oleh admin biasa. */
    private const ROLES_ADMIN = ['petugas', 'pimpinan'];

    public function index(): View
    {
        $users = User::orderBy('name')
            ->when(! $this->aktorSuperadmin(), fn ($q) => $q->whereIn('role', self::ROLES_ADMIN))
            ->get();

        return view('admin.user.index', [
            'users' => $users,
            'roles' => $this->rolesTersedia(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', Rule::in(array_keys($this->rolesTersedia()))],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->password = $data['password'];
        $user->aktif = true;
        $user->save();

        LogAktivitas::catat('tambah_akun', $user->email . ' (' . $user->role . ')');

        return back()->with('pesan', 'Akun ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->pastikanBolehMengelola($user);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'  => ['required', Rule::in(array_keys($this->rolesTersedia()))],
        ]);

        if ($data['role'] !== $user->role) {
            if ($user->id === auth()->user()->id) {
                return back()->withErrors(['name' => 'Anda tidak bisa mengubah peran akun sendiri.']);
            }

            if ($user->role === 'superadmin' && $user->aktif && ! $this->adaSuperadminAktifLain($user)) {
                return back()->withErrors(['name' => 'Harus tersisa minimal satu superadmin aktif.']);
            }
        }

        $peranLama = $user->role;

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->save();

        $keterangan = $user->email;
        if ($peranLama !== $user->role) {
            $keterangan .= ' (peran ' . $peranLama . ' menjadi ' . $user->role . ')';
        }

        LogAktivitas::catat('ubah_akun', $keterangan);

        return back()->with('pesan', 'Data akun diperbarui.');
    }

    /** Lengkapi profil: NIK, NIP, jabatan, dan foto profil. */
    public function profil(Request $request, User $user): RedirectResponse
    {
        $this->pastikanBolehMengelola($user);

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
        LogAktivitas::catat('ubah_profil', $user->email);

        return back()->with('pesan', 'Profil ' . $user->name . ' diperbarui.');
    }

    public function hapusFoto(User $user): RedirectResponse
    {
        $this->pastikanBolehMengelola($user);

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
            $user->foto = null;
            $user->save();

            LogAktivitas::catat('hapus_foto_profil', $user->email);
        }

        return back()->with('pesan', 'Foto profil ' . $user->name . ' dihapus.');
    }

    public function toggle(User $user): RedirectResponse
    {
        $this->pastikanBolehMengelola($user);

        if ($user->id === auth()->user()->id) {
            return back()->withErrors(['name' => 'Anda tidak bisa menonaktifkan akun sendiri.']);
        }

        if ($user->aktif && $user->role === 'superadmin' && ! $this->adaSuperadminAktifLain($user)) {
            return back()->withErrors(['name' => 'Harus tersisa minimal satu superadmin aktif.']);
        }

        $user->aktif = ! $user->aktif;
        $user->save();

        LogAktivitas::catat('status_akun', $user->email . ($user->aktif ? ' diaktifkan' : ' dinonaktifkan'));

        return back()->with('pesan', $user->aktif ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }

    public function password(Request $request, User $user): RedirectResponse
    {
        $this->pastikanBolehMengelola($user);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = $data['password'];
        $user->save();

        LogAktivitas::catat('reset_password', $user->email);

        return back()->with('pesan', 'Password akun ' . $user->name . ' diperbarui.');
    }

    public function resetPin(User $user): RedirectResponse
    {
        $this->pastikanBolehMengelola($user);

        $user->pin = null;
        $user->save();

        LogAktivitas::catat('reset_pin', $user->email);

        return back()->with('pesan', 'PIN akun ' . $user->name . ' dihapus. Pemilik akun dapat membuat PIN baru di halaman PIN buku tamu.');
    }

    private function aktorSuperadmin(): bool
    {
        return auth()->user()->role === 'superadmin';
    }

    /** Daftar peran yang boleh dipilih oleh pengguna yang sedang login. */
    private function rolesTersedia(): array
    {
        if ($this->aktorSuperadmin()) {
            return self::ROLES;
        }

        return array_intersect_key(self::ROLES, array_flip(self::ROLES_ADMIN));
    }

    /** Admin biasa hanya boleh mengelola petugas dan pimpinan. */
    private function pastikanBolehMengelola(User $target): void
    {
        if ($this->aktorSuperadmin()) {
            return;
        }

        abort_unless(
            in_array($target->role, self::ROLES_ADMIN, true),
            403,
            'Anda tidak memiliki akses untuk mengelola akun ini.'
        );
    }

    private function adaSuperadminAktifLain(User $user): bool
    {
        return User::where('role', 'superadmin')
            ->where('aktif', true)
            ->where('id', '!=', $user->id)
            ->exists();
    }
}