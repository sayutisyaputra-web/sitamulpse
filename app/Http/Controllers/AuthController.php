<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Hanya akun yang aktif yang boleh login
        $berhasil = Auth::attempt(
            ['email' => $data['email'], 'password' => $data['password'], 'aktif' => true],
            $request->boolean('ingat')
        );

        if (! $berhasil) {
            LogAktivitas::catat('login_gagal', 'Email: ' . $data['email']);

            return back()
                ->withErrors(['email' => 'Email atau password salah, atau akun tidak aktif.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        LogAktivitas::catat('login');

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        LogAktivitas::catat('logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}